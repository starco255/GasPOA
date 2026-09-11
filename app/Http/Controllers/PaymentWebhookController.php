<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Models\RetailOrder;
use App\Models\WholesaleOrder;
use App\Services\ClickPesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentWebhookController extends Controller
{
    public function clickPesa(Request $request, ClickPesaService $clickPesa)
    {
        $payload = $request->all();
        if (!$clickPesa->validWebhook($payload)) {
            abort(403, 'Invalid payment signature.');
        }

        $data = $request->input('data', []);
        $reference = data_get($data, 'orderReference');
        $event = strtoupper((string) $request->input('event'));
        $status = strtoupper((string) data_get($data, 'status'));
        abort_unless($reference && in_array($event, ['PAYMENT RECEIVED', 'PAYMENT FAILED'], true), 422);

        DB::transaction(function () use ($reference, $event, $status, $data, $payload) {
            $payment = Payment::where('provider_reference', $reference)->lockForUpdate()->firstOrFail();
            // Reference za manual zinathibitishwa na muuzaji pekee; callback ya link ya zamani
            // isibadilishe tena uchaguzi wa manual kuwa malipo yaliyokamilika.
            if ($payment->provider !== 'clickpesa') {
                return;
            }
            $succeeded = $event === 'PAYMENT RECEIVED' && $status === 'SUCCESS';
            $failed = $event === 'PAYMENT FAILED' || $status === 'FAILED';
            if (!$succeeded && !$failed) return;
            $providerPaymentReference = data_get($data, 'paymentReference') ?: data_get($data, 'id');
            $gatewayEventId = data_get($data, 'id') ?: $providerPaymentReference;
            $eventKey = 'clickpesa:' . $event . ':' . $status . ':' . ($gatewayEventId ?: ($payload['checksum'] ?? hash('sha256', json_encode($payload))));
            $alreadyProcessed = PaymentWebhookEvent::where('event_key', $eventKey)->exists();
            if ($alreadyProcessed) return;

            $paidAmount = (float) data_get($data, 'collectedAmount', data_get($data, 'amount', 0));
            if ($succeeded && round($paidAmount, 2) < round((float) $payment->amount, 2)) {
                abort(422, 'Payment amount is less than order amount.');
            }
            PaymentWebhookEvent::create([
                'payment_id' => $payment->id,
                'provider' => 'clickpesa',
                'event_key' => $eventKey,
                'event_name' => $event,
                'event_status' => $status,
                'payload' => $payload,
                'received_at' => now(),
            ]);
            $payment->update([
                'status' => $succeeded ? 'success' : 'failed',
                'provider_payment_reference' => $providerPaymentReference,
                'raw_callback_payload' => $payload,
                'gateway_event_id' => $gatewayEventId,
                'gateway_event' => $event,
                'gateway_status' => $status,
                'gateway_paid_amount' => $paidAmount ?: null,
                'gateway_currency' => data_get($data, 'collectedCurrency', data_get($data, 'currency', 'TZS')),
                'gateway_callback_received_at' => now(),
                'paid_at' => $succeeded ? now() : null,
                'failed_at' => $failed ? now() : null,
                'failure_reason' => $failed ? data_get($data, 'message') : null,
            ]);
            if ($succeeded) {
                $model = $payment->order_type === 'retail' ? RetailOrder::class : WholesaleOrder::class;
                $model::whereKey($payment->order_id)->update([
                    'payment_status' => 'paid',
                    'transaction_reference' => $payment->provider_payment_reference,
                ]);
            }
        });
        return response()->json(['success' => true]);
    }
}
