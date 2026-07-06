<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\Setting;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public const OTP_EXPIRY_MINUTES = 5;

    /**
     * Send OTP to customer.
     * Reuses an active (unexpired, unused) OTP if one exists,
     * otherwise generates a new one.
     *
     * Returns ['sent_via' => 'email'|'phone'|'log', 'channel' => string, 'otp_id' => int]
     */
    public function send(Customer $customer): array
    {
        $type   = $this->resolveType($customer);
        $record = $this->getActiveOtp($customer, $type);

        if (! $record) {
            $record = $this->createOtp($customer, $type);
        }

        $result = $this->dispatch($customer, $record);

        Log::info('OTP dispatched', [
            'customer_id' => $customer->id,
            'otp_id'      => $record->id,
            'via'         => $result['sent_via'],
            'reused'      => $result['reused'],
        ]);

        return $result;
    }

    public function verify(Customer $customer, string $code): bool
    {
        $record = CustomerOtp::where('customer_id', $customer->id)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || ! $record->isValid($code)) {
            return false;
        }

        $record->update(['used' => true]);

        return true;
    }

    public function clearOtps(Customer $customer): void
    {
        CustomerOtp::where('customer_id', $customer->id)->delete();
    }

    // ── Private helpers ───────────────────────────────────────────

    private function resolveType(Customer $customer): string
    {
        if ($customer->email) {
            return 'email';
        }

        if ($customer->phone_number && Setting::isPhoneOtpEnabled()) {
            return 'phone';
        }

        return 'email'; // fallback — dispatch will log warning
    }

    private function getActiveOtp(Customer $customer, string $type): ?CustomerOtp
    {
        return CustomerOtp::where('customer_id', $customer->id)
            ->where('type', $type)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    private function createOtp(Customer $customer, string $type): CustomerOtp
    {
        return CustomerOtp::create([
            'customer_id' => $customer->id,
            'code'        => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'type'        => $type,
            'expires_at'  => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'used'        => false,
        ]);
    }

    private function dispatch(Customer $customer, CustomerOtp $record): array
    {
        $reused = $record->created_at->lt(now()->subSeconds(2)); // true if not just created

        if ($record->type === 'email' && $customer->email) {
            $customer->notify(new SendOtpNotification($record->code, $customer->full_name));
            return ['sent_via' => 'email', 'channel' => $customer->email, 'reused' => $reused, 'otp_id' => $record->id];
        }

        if ($record->type === 'phone' && $customer->phone_number && Setting::isPhoneOtpEnabled()) {
            $this->sendViaSms($customer->phone_number, $record->code);
            return ['sent_via' => 'phone', 'channel' => $customer->phone_number, 'reused' => $reused, 'otp_id' => $record->id];
        }

        Log::warning('OTP generated but no delivery channel available', [
            'customer_id'  => $customer->id,
            'otp_id'       => $record->id,
            'type'         => $record->type,
            'has_email'    => (bool) $customer->email,
            'has_phone'    => (bool) $customer->phone_number,
            'phone_otp_on' => Setting::isPhoneOtpEnabled(),
        ]);

        return ['sent_via' => 'log', 'channel' => 'none', 'reused' => false, 'otp_id' => $record->id];
    }

    private function sendViaSms(string $phone, string $code): void
    {
        // TODO: integrate SMS provider (Twilio, Vonage, AWS SNS, etc.)
        // Example:
        // \Twilio\Rest\Client::messages->create($phone, [
        //     'from' => config('services.twilio.from'),
        //     'body' => "Your verification code is: {$code}",
        // ]);
        Log::info("[SMS STUB] Would send OTP to {$phone} — integrate SMS provider here.");
    }
}
