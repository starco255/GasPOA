<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\RetailOrder;
use App\Models\WholesaleOrder;
use App\Services\ClickPesaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function payRetail(RetailOrder $order, ClickPesaService $clickPesa): RedirectResponse
    {
        abort_unless($order->consumer_id === auth()->id(), 403);
        return $this->start($order, 'retail', $order->consumer, $clickPesa, 'Malipo ya agizo ' . $order->order_number);
    }

    public function payWholesale(WholesaleOrder $order, ClickPesaService $clickPesa): RedirectResponse
    {
        abort_unless(optional($order->retailer)->user_id === auth()->id(), 403);
        $customer = optional($order->retailer)->user;
        abort_unless($customer, 403);
        return $this->start($order, 'wholesale', $customer, $clickPesa, 'Malipo ya agizo la jumla ' . $order->order_number);
    }

    /** Records the consumer's payment reference for the retailer to verify. */
    public function submitRetailManualPayment(Request $request, RetailOrder $order): RedirectResponse
    {
        abort_unless($order->consumer_id === auth()->id(), 403);
        abort_if($order->payment_status === 'paid', 422, 'Malipo haya tayari yamehakikishwa; reference haiwezi kubadilishwa.');
        abort_if(($order->payment_method_provider ?? $order->payment_method) === 'cash', 422, 'Pesa taslimu haina reference ya muamala wa kutumwa.');

        $reference = $request->validate([
            'transaction_reference' => ['required', 'digits_between:10,100'],
        ])['transaction_reference'];

        DB::transaction(function () use ($order, $reference) {
            $order->update(['transaction_reference' => $reference]);
            $payment = Payment::where('order_type', 'retail')
                ->where('order_id', $order->id)
                ->lockForUpdate()
                ->first();

            if ($payment && $payment->status !== 'success') {
                // Reference ya manual huchagua manual flow na kuondoa checkout link ya zamani.
                // Callback yoyote ya link hiyo haiwezi tena kubadilisha order hii kuwa paid.
                $payment->update([
                    'status' => 'pending',
                    'provider' => 'manual',
                    'provider_payment_reference' => $reference,
                    'checkout_url' => null,
                    'failed_at' => null,
                    'failure_reason' => null,
                ]);
            }
        });

        return back()->with('success', 'Reference ID imetumwa kwa muuzaji. Unaweza kuibadili hadi muuzaji athibitishe malipo.');
    }

    /** Records the retailer's receipt/reference while the wholesaler verifies a direct payment. */
    public function submitWholesaleManualPayment(Request $request, WholesaleOrder $order): RedirectResponse
    {
        abort_unless(optional($order->retailer)->user_id === auth()->id(), 403);
        abort_if($order->payment_status === 'paid', 422, 'Agizo hili tayari limelipwa.');
        abort_if($order->payment_method === 'cash', 422, 'Taslimu huthibitishwa wakati wa delivery.');

        $reference = $request->validate([
            'transaction_reference' => ['required', 'digits_between:10,100'],
        ])['transaction_reference'];

        DB::transaction(function () use ($order, $reference) {
            $order->update(['transaction_reference' => $reference]);
            $payment = Payment::where('order_type', 'wholesale')
                ->where('order_id', $order->id)
                ->lockForUpdate()
                ->first();

            if ($payment && $payment->status !== 'success') {
                $payment->update([
                    'status' => 'pending',
                    'provider' => 'manual',
                    'provider_payment_reference' => $reference,
                    'checkout_url' => null,
                    'failed_at' => null,
                    'failure_reason' => null,
                ]);
            }
        });

        return back()->with('success', 'Rejea ya malipo imetumwa kwa wholesaler. Status itakuwa Imelipwa baada ya kuthibitishwa.');
    }

    /** Confirms a direct Lipa Namba/bank transfer after the seller checks it. */
    public function confirmRetail(RetailOrder $order): RedirectResponse
    {
        $profile = auth()->user()->businessProfile()->where('business_type', 'retailer')->first();
        abort_unless($profile && $order->retailer_id === $profile->id, 403);
        $reference = $order->transaction_reference;
        abort_unless($reference && preg_match('/^\d{10,100}$/', $reference), 422, 'Transaction ID ya mteja haipo au si sahihi.');
        $this->confirmManualPayment($order, 'retail', $reference);
        return back()->with('success', 'Malipo ya agizo yamethibitishwa na kuwekwa Paid.');
    }

    /** Confirms a direct Lipa Namba/bank transfer after the wholesaler checks it. */
    public function confirmWholesale(WholesaleOrder $order): RedirectResponse
    {
        $profile = auth()->user()->businessProfile()->where('business_type', 'wholesaler')->first();
        abort_unless($profile && $order->wholesaler_id === $profile->id, 403);
        $reference = $order->transaction_reference;
        abort_unless($reference && preg_match('/^\d{10,100}$/', $reference), 422, 'Transaction ID ya retailer haipo au si sahihi.');
        $this->confirmManualPayment($order, 'wholesale', $reference);
        return back()->with('success', 'Malipo ya agizo yamethibitishwa na kuwekwa Paid.');
    }

    /** Reject a consumer's manual reference so they can submit a corrected one. */
    public function rejectRetail(RetailOrder $order): RedirectResponse
    {
        $profile = auth()->user()->businessProfile()->where('business_type', 'retailer')->first();
        abort_unless($profile && $order->retailer_id === $profile->id, 403);
        $this->rejectManualReference($order, 'retail');

        return back()->with('warning', 'Reference ID imekataliwa. Mteja amepewa nafasi ya kutuma namba sahihi tena.');
    }

    /** Reject a retailer's manual reference so they can submit a corrected one. */
    public function rejectWholesale(WholesaleOrder $order): RedirectResponse
    {
        $profile = auth()->user()->businessProfile()->where('business_type', 'wholesaler')->first();
        abort_unless($profile && $order->wholesaler_id === $profile->id, 403);
        $this->rejectManualReference($order, 'wholesale');

        return back()->with('warning', 'Reference ID imekataliwa. Retailer amepewa nafasi ya kutuma namba sahihi tena.');
    }

    private function start(
    object $order,
    string $type,
    object $customer,
    ClickPesaService $clickPesa,
    string $description
): RedirectResponse {

    if ($order->payment_status === 'paid') {
        return back()->with(
            'success',
            'Agizo hili tayari limelipwa.'
        );
    }

    if (filled($order->transaction_reference)) {
        return back()->with(
            'info',
            'Reference ID ya manual tayari imetumwa. ' .
            'Subiri muuzaji aithibitishe au aikatae kabla ya kutumia ClickPesa.'
        );
    }

    $payment = Payment::where('order_type', $type)
        ->where('order_id', $order->id)
        ->latest('id')
        ->first();

    if (!$payment || $payment->payment_method === 'cash') {
        return back()->with(
            'info',
            'Malipo ya taslimu huthibitishwa baada ya kupokelewa na muuzaji.'
        );
    }

    if (!$clickPesa->isConfigured()) {
        return back()->with(
            'warning',
            'ClickPesa bado haijasanidiwa. ' .
            'Hakikisha CLICKPESA_ENABLED=true, ' .
            'CLICKPESA_CLIENT_ID, CLICKPESA_API_KEY, ' .
            'CLICKPESA_CHECKSUM_KEY na CLICKPESA_BASE_URL ' .
            'zimewekwa kwenye .env.'
        );
    }

    try {

        /*
         * Tayarisha payment kwa checkout mpya.
         */
        if ($payment->status !== 'pending') {
            $payment->update([
                'status' => 'pending',
                'provider' => 'clickpesa',
                'provider_payment_reference' => null,
                'checkout_url' => null,
                'failed_at' => null,
                'failure_reason' => null,
            ]);

            $payment->refresh();
        }

        /*
         * Kama checkout URL ya zamani ipo, itumie.
         * Kama haipo, tengeneza mpya ClickPesa.
         */
        $url = $payment->checkout_url;

        if (!filled($url)) {
            $url = $clickPesa->createCheckoutLink(
                $payment,
                $customer->full_name,
                $customer->email,
                $customer->phone_number,
                $description
            );
        }

        return redirect()->away($url);

    } catch (\Throwable $e) {

        Log::error(
            'Unable to create ClickPesa checkout link',
            [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'order_type' => $type,
                'provider_reference' =>
                    $payment->provider_reference,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]
        );

        /*
         * Customer asione technical secrets.
         * Technical error itaonekana kwenye laravel.log.
         */
        return back()->with(
            'error',
            'Imeshindikana kuanzisha malipo ya ClickPesa. ' .
            'Tafadhali jaribu tena.'
        );
    }
}

    private function confirmManualPayment(object $order, string $type, string $reference): void
    {
        abort_if($order->payment_status === 'paid', 422, 'Agizo hili tayari limelipwa.');
        abort_if(($order->payment_method_provider ?? $order->payment_method) === 'cash', 422, 'Taslimu huthibitishwa wakati wa delivery.');

        DB::transaction(function () use ($order, $type, $reference) {
            $payment = Payment::where('order_type', $type)->where('order_id', $order->id)->lockForUpdate()->first();
            if ($payment) {
                abort_unless($payment->provider === 'manual', 422, 'Malipo ya ClickPesa huthibitishwa automatically na mfumo.');
                $payment->update([
                    'status' => 'success', 'provider' => 'manual', 'provider_payment_reference' => $reference,
                    'paid_at' => now(),
                ]);
            }
            $order->update(['payment_status' => 'paid', 'transaction_reference' => $reference]);
        });
    }

    private function rejectManualReference(object $order, string $type): void
    {
        abort_if($order->payment_status === 'paid', 422, 'Malipo haya tayari yamehakikishwa.');
        abort_if(($order->payment_method_provider ?? $order->payment_method) === 'cash', 422, 'Taslimu haina Reference ID ya kukataliwa.');
        abort_unless(filled($order->transaction_reference), 422, 'Hakuna Reference ID ya kukataliwa.');

        DB::transaction(function () use ($order, $type) {
            $payment = Payment::where('order_type', $type)->where('order_id', $order->id)->lockForUpdate()->first();
            abort_unless($payment && $payment->provider === 'manual', 422, 'Reference ya ClickPesa huthibitishwa automatically na mfumo.');

            $payment->update([
                'status' => 'failed',
                'provider_payment_reference' => $order->transaction_reference,
                'failed_at' => now(),
                'failure_reason' => 'Reference ID imekataliwa na muuzaji.',
            ]);
            $order->update([
                'payment_status' => 'pending',
                'transaction_reference' => null,
            ]);
        });
    }
}
