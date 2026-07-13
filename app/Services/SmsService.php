<?php

namespace App\Services;

use App\Models\AdminSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?AdminSetting $settings;

    public function __construct()
    {
        $this->settings = AdminSetting::query()->first();
    }

    public function send(string $to, string $message): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('SMS was not sent because the gateway is disabled or incomplete.', [
                'phone' => $to,
            ]);

            return false;
        }

        $phone = $this->normalizeBangladeshNumber($to);
        if ($phone === null) {
            Log::warning('SMS was not sent because the phone number is invalid.', ['phone' => $to]);

            return false;
        }

        try {
            $response = Http::timeout(15)
                ->retry(2, 250, throw: false)
                ->get($this->settings->sms_api_url, [
                    'ApiKey' => $this->settings->sms_api_key,
                    'ClientId' => $this->settings->sms_client_id,
                    'SenderId' => $this->settings->sms_sender_id,
                    'Message' => $message,
                    'MobileNumbers' => $phone,
                    'Is_Unicode' => preg_match('/[^\x00-\x7F]/u', $message) ? 'true' : 'false',
                    'Is_Flash' => 0,
                    'DataCoding' => 0,
                ]);

            if (! $response->successful()) {
                Log::error('SMS gateway returned an HTTP error.', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return false;
            }

            $payload = $response->json();
            if (! is_array($payload)) {
                Log::error('SMS gateway returned an invalid response.', [
                    'phone' => $phone,
                    'response' => $response->body(),
                ]);

                return false;
            }

            if ((int) ($payload['ErrorCode'] ?? 0) !== 0) {
                Log::error('SMS gateway rejected the message.', [
                    'phone' => $phone,
                    'error' => $payload['ErrorDescription'] ?? 'Unknown SMS gateway error',
                    'response' => $payload,
                ]);

                return false;
            }

            Log::info('SMS sent successfully.', ['phone' => $phone]);

            return true;
        } catch (ConnectionException $exception) {
            Log::error('SMS gateway connection failed.', [
                'phone' => $phone,
                'error' => $exception->getMessage(),
            ]);

            return false;
        } catch (\Throwable $exception) {
            report($exception);

            Log::error('Unexpected SMS sending failure.', [
                'phone' => $phone,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendOtp(string $otp, string $to, string $name): bool
    {
        $appName = $this->settings?->site_name ?: config('app.name');
        $template = $this->settings?->sms_message_template
            ?: 'Dear {name}, your One-Time Password (OTP) is {otp}. This OTP will expire in 5 minutes. - {app_name}';

        return $this->send($to, str_replace(
            ['{name}', '{otp}', '{app_name}'],
            [$name, $otp, $appName],
            $template,
        ));
    }

    private function isConfigured(): bool
    {
        return (bool) ($this->settings?->sms_status
            && filled($this->settings->sms_api_url)
            && filled($this->settings->sms_api_key)
            && filled($this->settings->sms_client_id)
            && filled($this->settings->sms_sender_id));
    }

    private function normalizeBangladeshNumber(string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', $number) ?: '';

        if (str_starts_with($digits, '8801') && strlen($digits) === 13) {
            return $digits;
        }

        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            return '88'.$digits;
        }

        if (str_starts_with($digits, '1') && strlen($digits) === 10) {
            return '880'.$digits;
        }

        return null;
    }
}
