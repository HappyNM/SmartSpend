<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MpesaService
{
    public function accessToken(): string
    {
        $key = config('services.mpesa.consumer_key');
        $secret = config('services.mpesa.consumer_secret');
        $baseUrl = rtrim((string) config('services.mpesa.base_url'), '/');

        $response = Http::timeout((int) config('services.mpesa.timeout', 30))
            ->withBasicAuth($key, $secret)
            ->get($baseUrl.'/oauth/v1/generate?grant_type=client_credentials');

        if (! $response->successful() || ! $response->json('access_token')) {
            throw new RuntimeException('Unable to fetch M-Pesa access token.');
        }

        return (string) $response->json('access_token');
    }

    public function stkPush(string $phoneNumber, float $amount, string $accountReference, string $transactionDesc): array
    {
        $timestamp = now()->format('YmdHis');
        $shortcode = (string) config('services.mpesa.shortcode');
        $passkey = (string) config('services.mpesa.passkey');
        $password = base64_encode($shortcode.$passkey.$timestamp);
        $baseUrl = rtrim((string) config('services.mpesa.base_url'), '/');

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) round($amount),
            'PartyA' => $phoneNumber,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => (string) config('services.mpesa.callback_url'),
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];

        $response = Http::timeout((int) config('services.mpesa.timeout', 30))
            ->withToken($this->accessToken())
            ->post($baseUrl.'/mpesa/stkpush/v1/processrequest', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to initiate STK push.');
        }

        return $response->json();
    }
}