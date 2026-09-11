<?php

namespace App\Services;

use App\Models\BusinessProfile;

class PaymentMethodService
{
    public const METHODS = ['cash', 'mpesa', 'tigopesa', 'airtelmoney', 'halopesa', 'bank'];

    public function getAllowedMethods(BusinessProfile $businessProfile): array
    {
        return array_values(array_filter(self::METHODS, fn (string $method) => $this->isAllowed($businessProfile, $method)));
    }

    public function isAllowed(BusinessProfile $businessProfile, string $method): bool
    {
        return match ($method) {
            'cash' => (bool) $businessProfile->accept_cash,
            'mpesa' => (bool) $businessProfile->accept_mpesa && filled($businessProfile->mpesa_number),
            'tigopesa' => (bool) $businessProfile->accept_tigopesa && filled($businessProfile->mixx_number),
            'airtelmoney' => (bool) $businessProfile->accept_airtelmoney && filled($businessProfile->airtel_number),
            'halopesa' => (bool) $businessProfile->accept_halopesa && filled($businessProfile->halopesa_number),
            'bank' => (bool) $businessProfile->accept_bank && filled($businessProfile->bank_account_number),
            default => false,
        };
    }

    public function getMethodDetails(BusinessProfile $businessProfile, string $method): array
    {
        $details = [
            'value' => $method,
            'label' => match ($method) {
                'cash' => 'Pesa Taslimu', 'mpesa' => 'M-Pesa', 'tigopesa' => 'TigoPesa / Mixx by Yas',
                'airtelmoney' => 'Airtel Money', 'halopesa' => 'HaloPesa', 'bank' => 'Bank Transfer',
            },
            'enabled' => $this->isAllowed($businessProfile, $method),
        ];

        if (in_array($method, ['mpesa', 'tigopesa', 'airtelmoney', 'halopesa'], true)) {
            $details['account'] = match ($method) {
                'mpesa' => $businessProfile->mpesa_number, 'tigopesa' => $businessProfile->mixx_number,
                'airtelmoney' => $businessProfile->airtel_number, 'halopesa' => $businessProfile->halopesa_number,
            };
        }
        if ($method === 'bank') {
            $bankName = match (strtolower((string) $businessProfile->bank_name)) {
                'nmb' => 'NMB',
                'crdb' => 'CRDB',
                'nbc' => 'NBC',
                'other', 'nyingine' => 'Nyingine',
                default => $businessProfile->bank_name,
            };
            $details += ['bank_name' => $bankName, 'account_number' => $businessProfile->bank_account_number, 'account_name' => $businessProfile->bank_account_name];
        }
        return $details;
    }
}
