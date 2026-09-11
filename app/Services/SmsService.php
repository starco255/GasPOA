<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS to a phone number.
     */
    public function send($phoneNumber, $message)
    {
        // Kwa maendeleo (development), tunaandika kwenye log
        // Baadaye utaunganisha na Beem Africa, Infobip, Twilio, n.k.
        
        Log::info("SMS sent to {$phoneNumber}: {$message}");
        
        // TODO: Unganisha na API halisi
        // $this->sendViaBeem($phoneNumber, $message);
        // $this->sendViaInfobip($phoneNumber, $message);
        
        return true;
    }
    
    /**
     * Send OTP message.
     */
    public function sendOtp($phoneNumber, $otp)
    {
        $message = "GasPOA: Namba yako ya uthibitisho (OTP) ni: {$otp}. Usimwambie mtu yeyote. Inaisha baada ya dakika 5.";
        return $this->send($phoneNumber, $message);
    }
    
    /**
     * Send via Beem Africa (Mfano).
     */
    private function sendViaBeem($phoneNumber, $message)
    {
        $apiKey = config('services.beem.api_key');
        $secretKey = config('services.beem.secret_key');
        $senderId = config('services.beem.sender_id', 'GasPOA');
        
        // Format phone number (remove +255 prefix if needed)
        $phoneNumber = str_replace('+255', '', $phoneNumber);
        $phoneNumber = ltrim($phoneNumber, '0');
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apisms.beem.africa/v1/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'source_addr' => $senderId,
                'encoding' => 0,
                'schedule_time' => '',
                'message' => $message,
                'recipients' => [
                    ['recipient_id' => 1, 'dest_addr' => $phoneNumber]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($apiKey . ':' . $secretKey)
            ],
        ]);
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        Log::info('Beem SMS Response', ['response' => $response]);
        return $response;
    }
}
