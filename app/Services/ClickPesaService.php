<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ClickPesaService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.clickpesa.enabled')
            && filled(config('services.clickpesa.client_id'))
            && filled(config('services.clickpesa.api_key'))
            && filled(config('services.clickpesa.checksum_key'))
            && filled(config('services.clickpesa.base_url'));
    }

    public function createCheckoutLink(
        Payment $payment,
        string $customerName,
        ?string $customerEmail,
        string $customerPhone,
        string $description
    ): string {
        if (!$this->isConfigured()) {
            throw new RuntimeException(
                'ClickPesa haijawezeshwa au configuration haijakamilika. ' .
                'Hakikisha CLICKPESA_ENABLED, CLIENT_ID, API_KEY, ' .
                'CHECKSUM_KEY na BASE_URL zimewekwa.'
            );
        }

        /*
         * ClickPesa orderReference:
         * - lazima iwe alphanumeric
         * - isitumike mara mbili
         *
         * Mfano:
         * GPOA-20260909-725
         *        ↓
         * GPOA20260909725
         */
        $orderReference = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            (string) $payment->provider_reference
        );

        if (!$orderReference) {
            throw new RuntimeException(
                'ClickPesa orderReference ni tupu au si sahihi.'
            );
        }

        /*
         * Hakikisha reference tunayotuma ClickPesa ndiyo
         * tunayohifadhi kwenye Payment.
         */
        if ($payment->provider_reference !== $orderReference) {
            $payment->update([
                'provider_reference' => $orderReference,
            ]);
        }

        /*
         * ClickPesa inataka phone:
         * 255712345678
         *
         * Sio:
         * +255712345678
         * 0712345678
         */
        $customerPhone = preg_replace(
            '/\D+/',
            '',
            trim($customerPhone)
        );

        if (str_starts_with($customerPhone, '0')) {
            $customerPhone = '255' . substr($customerPhone, 1);
        }

        if (!preg_match('/^255\d{9}$/', $customerPhone)) {
            throw new RuntimeException(
                'Namba ya simu ya mteja si sahihi kwa ClickPesa: ' .
                $customerPhone
            );
        }

        $payload = [
            'totalPrice'     => (string) $payment->amount,
            'orderReference' => $orderReference,
            'orderCurrency'  => 'TZS',
            'customerName'   => trim($customerName),
            'customerEmail'  => $customerEmail
                ? trim($customerEmail)
                : null,
            'customerPhone'  => $customerPhone,
            'description'    => trim($description),
            'callbackUrl'    => route('webhooks.clickpesa'),
        ];

        /*
         * Ondoa null/empty values kabla ya checksum.
         */
        $payload = array_filter(
            $payload,
            fn ($value) => $value !== null && $value !== ''
        );

        /*
         * Checksum lazima itengenezwe kabla ya kuongeza
         * checksum yenyewe kwenye payload.
         */
        $payload['checksum'] = $this->checksum($payload);

        $endpoint = rtrim(
            config('services.clickpesa.base_url'),
            '/'
        ) . '/checkout-link/generate-checkout-url';

        Log::info('ClickPesa checkout request', [
            'payment_id' => $payment->id,
            'order_reference' => $orderReference,
            'amount' => $payload['totalPrice'],
            'currency' => $payload['orderCurrency'],
            'customer_phone' => $customerPhone,
            'endpoint' => $endpoint,
        ]);

        /*
         * ClickPesa token tayari ina:
         *
         * Bearer eyJ...
         *
         * Kwa hiyo tunaitumia moja kwa moja kwenye
         * Authorization header.
         */
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => $this->token(),
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($endpoint, $payload);

        /*
         * USI-fiche error ya ClickPesa.
         * Iweke kwenye Laravel log ili tujue exact reason.
         */
        if ($response->failed()) {
            Log::error('ClickPesa checkout request failed', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'response' => $response->body(),
                'order_reference' => $orderReference,
            ]);

            throw new RuntimeException(
                'ClickPesa HTTP ' .
                $response->status() .
                ': ' .
                $response->body()
            );
        }

        $data = $response->json();

        Log::info('ClickPesa checkout response', [
            'payment_id' => $payment->id,
            'status' => $response->status(),
            'response' => $data,
        ]);

        $url = Arr::get($data, 'checkoutLink');

        if (!is_string($url) || trim($url) === '') {
            throw new RuntimeException(
                'ClickPesa haikurudisha checkoutLink. Response: ' .
                $response->body()
            );
        }

        $payment->update([
            'checkout_url' => $url,
            'provider' => 'clickpesa',
        ]);

        return $url;
    }

    public function validWebhook(array $payload): bool
    {
        $key = config('services.clickpesa.checksum_key');

        if (!$key || empty($payload['checksum'])) {
            return false;
        }

        $received = (string) $payload['checksum'];

        unset(
            $payload['checksum'],
            $payload['checksumMethod']
        );

        return hash_equals(
            $this->checksum($payload),
            $received
        );
    }

    private function token(): string
    {
        return Cache::remember(
            'clickpesa.authorization_token',
            now()->addMinutes(55),
            function (): string {

                $endpoint = rtrim(
                    config('services.clickpesa.base_url'),
                    '/'
                ) . '/generate-token';

                $response = Http::acceptJson()
                    ->withHeaders([
                        'client-id' => config(
                            'services.clickpesa.client_id'
                        ),
                        'api-key' => config(
                            'services.clickpesa.api_key'
                        ),
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(30)
                    ->post($endpoint);

                if ($response->failed()) {
                    Log::error('ClickPesa token request failed', [
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);

                    throw new RuntimeException(
                        'ClickPesa Token HTTP ' .
                        $response->status() .
                        ': ' .
                        $response->body()
                    );
                }

                $data = $response->json();

                $token = Arr::get($data, 'token');

                if (!is_string($token) || trim($token) === '') {
                    throw new RuntimeException(
                        'ClickPesa haikurudisha authorization token. ' .
                        'Response: ' . $response->body()
                    );
                }

                /*
                 * ClickPesa token tayari ina "Bearer ".
                 *
                 * Hatuiondoi.
                 */
                return trim($token);
            }
        );
    }

    private function checksum(array $payload): string
    {
        $key = config('services.clickpesa.checksum_key');

        if (!$key) {
            throw new RuntimeException(
                'CLICKPESA_CHECKSUM_KEY haijawekwa.'
            );
        }

        /*
         * checksum na checksumMethod hazipaswi kuingia
         * kwenye checksum calculation.
         */
        unset(
            $payload['checksum'],
            $payload['checksumMethod']
        );

        $canonicalPayload = $this->canonicalize($payload);

        $json = json_encode(
            $canonicalPayload,
            JSON_UNESCAPED_SLASHES
        );

        if ($json === false) {
            throw new RuntimeException(
                'Imeshindikana kutengeneza JSON ya ClickPesa checksum.'
            );
        }

        return hash_hmac(
            'sha256',
            $json,
            $key
        );
    }

    private function canonicalize(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        /*
         * Preserve array/list order.
         */
        if (array_is_list($value)) {
            return array_map(
                fn ($item) => $this->canonicalize($item),
                $value
            );
        }

        /*
         * Sort object keys alphabetically recursively.
         */
        ksort($value);

        foreach ($value as $key => $item) {
            $value[$key] = $this->canonicalize($item);
        }

        return $value;
    }
}