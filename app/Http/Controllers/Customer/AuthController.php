<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Setting;
use App\Services\CustomerAuthService;
use App\Services\OtpService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly CustomerAuthService $authService,
        private readonly OtpService $otpService,
    ) {}

    // ── Step 1: Enter email or phone ─────────────────────────────
    public function showStart()
    {
        return view('customer.auth.start');
    }

    public function processStart(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
        ], [
            'identifier.required' => 'Please enter your email address or phone number.',
        ]);

        $identifier = trim($request->identifier);
        $type       = $this->authService->identifierType($identifier);

        // Phone entered but SMS disabled and no email fallback possible for new user
        // We still allow it — they'll be prompted for email on register if needed

        session([
            'customer_auth.identifier'      => $identifier,
            'customer_auth.identifier_type' => $type,
        ]);

        $customer = $this->authService->findByIdentifier($identifier);

        if ($customer && $customer->is_verified) {
            // Existing verified customer → send OTP to log in
            $dispatch = $this->otpService->send($customer);
            session(['customer_auth.customer_id' => $customer->id]);

            return redirect()->route('customer.auth.verify')
                             ->with('info', $this->sentMessage($dispatch));
        }

        // New customer or unverified → create stub and send OTP
        if (! $customer) {
            // Phone-only signup when SMS is disabled: require email instead
            if ($type === 'phone' && ! Setting::isPhoneOtpEnabled()) {
                return redirect()->route('customer.auth.register');
            }

            $customer = $this->authService->createStub($identifier);
        }

        session(['customer_auth.customer_id' => $customer->id]);

        $dispatch = $this->otpService->send($customer);

        return redirect()->route('customer.auth.verify')
                         ->with('info', $this->sentMessage($dispatch));
    }

    // ── Step 2: Register (only when phone entered + SMS disabled) ─
    // Collects an email so OTP can be sent via email instead
    public function showRegister()
    {
        if (! session('customer_auth.identifier')) {
            return redirect()->route('customer.auth.start');
        }

        return view('customer.auth.register', [
            'identifier'     => session('customer_auth.identifier'),
            'identifierType' => session('customer_auth.identifier_type'),
        ]);
    }

    public function processRegister(Request $request)
    {
        if (! session('customer_auth.identifier')) {
            return redirect()->route('customer.auth.start');
        }

        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
        ], [
            'email.required' => 'An email address is required to receive your verification code.',
            'email.unique'   => 'This email is already registered.',
        ]);

        $identifier = session('customer_auth.identifier');

        // Create stub with both phone and email
        $customer = Customer::create([
            'phone_number' => $identifier,
            'email'        => $request->email,
            'is_verified'  => false,
        ]);

        session(['customer_auth.customer_id' => $customer->id]);

        $dispatch = $this->otpService->send($customer);

        return redirect()->route('customer.auth.verify')
                         ->with('info', $this->sentMessage($dispatch));
    }

    // ── Step 3: OTP Verification ─────────────────────────────────
    public function showVerify()
    {
        if (! session('customer_auth.customer_id')) {
            return redirect()->route('customer.auth.start');
        }

        $customer = Customer::find(session('customer_auth.customer_id'));

        if (! $customer) {
            return redirect()->route('customer.auth.start');
        }

        return view('customer.auth.verify', compact('customer'));
    }

    public function processVerify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the 6-digit code.',
            'otp.size'     => 'The code must be exactly 6 digits.',
        ]);

        $customer = Customer::find(session('customer_auth.customer_id'));

        if (! $customer) {
            return redirect()->route('customer.auth.start')
                             ->with('error', 'Session expired. Please start again.');
        }

        if (! $this->otpService->verify($customer, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired code. Please try again.']);
        }

        $this->authService->markVerified($customer, $this->otpService);
        $this->authService->login($customer);

        session()->forget(['customer_auth.identifier', 'customer_auth.identifier_type', 'customer_auth.customer_id']);

        return redirect()->route('customer.dashboard')
                         ->with('success', 'Welcome! You are now signed in.');
    }

    // ── Resend OTP ───────────────────────────────────────────────
    public function resendOtp()
    {
        $customer = Customer::find(session('customer_auth.customer_id'));

        if (! $customer) {
            return redirect()->route('customer.auth.start');
        }

        $dispatch = $this->otpService->send($customer);

        return redirect()->route('customer.auth.verify')
                         ->with('info', $this->sentMessage($dispatch));
    }

    // ── Dashboard ────────────────────────────────────────────────
    public function dashboard()
    {
        $customer = $this->authService->currentCustomer();
        return view('customer.dashboard', compact('customer'));
    }

    // ── Logout ───────────────────────────────────────────────────
    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('customer.auth.start')
                         ->with('success', 'You have been signed out.');
    }

    private function sentMessage(array $dispatch): string
    {
        return match ($dispatch['sent_via']) {
            'email' => "A 6-digit code was sent to {$dispatch['channel']}.",
            'phone' => "A 6-digit code was sent via SMS to {$dispatch['channel']}.",
            default => 'Code generated — check application logs (no delivery channel configured).',
        };
    }
}
