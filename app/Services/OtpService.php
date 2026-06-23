<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Setting;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public const OTP_EXPIRY_MINUTES = 10;

    public function generate(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate, store, and dispatch OTP for a customer.
     * Returns ['sent_via' => 'email'|'phone'|'log', 'channel' => string]
     */
    public function send(Customer $customer): array
    {
        $otp = $this->generate();

        $customer->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        $result = $this->dispatch($customer, $otp);

        Log::info("OTP dispatched", [
            'customer_id' => $customer->id,
            'via'         => $result['sent_via'],
            'channel'     => $result['channel'],
        ]);

        return $result;
    }

    private function dispatch(Customer $customer, string $otp): array
    {
        // Always try email first if available
        if ($customer->email) {
            $customer->notify(new SendOtpNotification($otp, $customer->full_name));
            return ['sent_via' => 'email', 'channel' => $customer->email];
        }

        // Phone OTP — only if enabled in admin settings
        if ($customer->phone_number && Setting::isPhoneOtpEnabled()) {
            $this->sendViaSms($customer->phone_number, $otp);
            return ['sent_via' => 'phone', 'channel' => $customer->phone_number];
        }

        // Fallback: log only (phone provided but phone OTP disabled)
        Log::warning("OTP generated but no delivery channel available", [
            'customer_id'  => $customer->id,
            'otp_preview'  => substr($otp, 0, 2) . '****',
            'has_email'    => (bool) $customer->email,
            'has_phone'    => (bool) $customer->phone_number,
            'phone_otp_on' => Setting::isPhoneOtpEnabled(),
        ]);

        return ['sent_via' => 'log', 'channel' => 'none'];
    }

    /**
     * Stub for SMS delivery — wire up your SMS provider here.
     * Return true on success, false on failure.
     */
    private function sendViaSms(string $phone, string $otp): bool
    {
        // TODO: integrate SMS API (e.g. Twilio, Vonage, AWS SNS)
        // Example:
        // TwilioClient::messages->create($phone, [
        //     'from' => config('services.twilio.from'),
        //     'body' => "Your verification code is: {$otp}",
        // ]);

        Log::info("[SMS STUB] Would send OTP to {$phone} — integrate SMS provider here.");
        return true;
    }

    public function verify(Customer $customer, string $inputOtp): bool
    {
        return $customer->isOtpValid($inputOtp);
    }

    public function clearOtp(Customer $customer): void
    {
        $customer->update(['otp' => null, 'otp_expires_at' => null]);
    }
}
