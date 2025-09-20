<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\SmsHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * @var AdminSetting|null Holds SMS and site settings from DB
     */
    protected ?AdminSetting $settings;

    /**
     * SmsService constructor.
     * Fetches SMS configuration from database for later use.
     */
    public function __construct()
    {
        $this->settings = AdminSetting::first();
    }

    /**
     * Send a raw SMS to a specific phone number.
     *
     * @param string $to Recipient phone number (without country code)
     * @param string $message Message body to send
     * @return bool True if SMS sent successfully, false otherwise
     */
    public function send(string $to, string $message): bool
    {
        // -----------------------------
        // 1. Check if SMS system is enabled
        // -----------------------------
        if (!$this->settings || !$this->settings->sms_status) {
            Log::info("SMS system is disabled; message not sent.", [
                'phone' => $to,
                'message' => $message,
            ]);
            return false;
        }

        try {
            // -----------------------------
            // 2. Detect Unicode messages (Bangla/UTF)
            // -----------------------------
            $isUnicode = preg_match('/[\x80-\xFF]/', $message) ? 'true' : 'false';

            // -----------------------------
            // 3. Send SMS via SoftClever API
            // -----------------------------
            $response = Http::get($this->settings->sms_api_url, [
                'ApiKey' => $this->settings->sms_api_key,
                'ClientId' => $this->settings->sms_client_id,
                'SenderId' => $this->settings->sms_sender_id,
                'Message' => $message,
                'MobileNumbers' => '88' . $to, // Bangladesh country code
                'Is_Unicode' => $isUnicode,
                'Is_Flash' => 0,
                'DataCoding' => 0,
            ]);

            $body = $response->json();

            // -----------------------------
            // 4. Handle API response
            // -----------------------------
            if (isset($body['ErrorCode']) && $body['ErrorCode'] != 0) {
                $errorMessage = $body['ErrorDescription'] ?? 'Unexpected API error';
                Log::error("SMS sending failed", [
                    'phone' => $to,
                    'message' => $message,
                    'response' => $body,
                ]);
                $status = 'Failed';
                return false;
            } else {
                $status = 'Success';
            }

            // -----------------------------
            // 5. Store SMS history for tracking
            // -----------------------------
            // SmsHistory::create([
            //     'sender' => $this->settings->sms_sender_id,
            //     'message' => $message,
            //     'phone_number' => $to,
            //     'unicode' => $isUnicode,
            //     'status' => $status,
            // ]);

            // -----------------------------
            // 6. Log success
            // -----------------------------
            Log::info("SMS sent successfully", [
                'phone' => $to,
                'message' => $message,
                'response' => $body,
            ]);

            return true;

        } catch (\Exception $e) {
            // -----------------------------
            // 7. Handle unexpected exceptions
            // -----------------------------
            Log::error("SMS sending exception", [
                'phone' => $to,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send an OTP SMS using template.
     *
     * @param string $otp Generated one-time password
     * @param string $to Recipient phone number
     * @param string $name Recipient name for personalization
     * @return bool True if OTP sent successfully, false otherwise
     */
    public function sendOtp(string $otp, string $to, string $name): bool
    {
        // -----------------------------
        // 1. Fetch app/site name for template
        // -----------------------------
        $appName = $this->settings?->site_name ?? config('app.name');

        // -----------------------------
        // 2. Build message from template
        // -----------------------------
        $template = $this->settings->sms_message_template ??
            "Dear {name}, your One-Time Password (OTP) is {otp}. This OTP will expire in 5 minutes. - {app_name}";

        $message = str_replace(
            ['{name}', '{otp}', '{app_name}'],
            [$name, $otp, $appName],
            $template
        );

        // -----------------------------
        // 3. Send OTP using main send() method
        // -----------------------------
        return $this->send($to, $message);
    }
}
