<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerAuthService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $customer   = $this->authService->findByIdentifier($identifier);

        // Store in session for next steps
        session([
            'customer_auth.identifier'      => $identifier,
            'customer_auth.identifier_type' => $this->authService->identifierType($identifier),
        ]);

        if ($customer && $customer->isRegistrationComplete()) {
            // Existing customer → send OTP for login
            $dispatch = $this->otpService->send($customer);
            session(['customer_auth.customer_id' => $customer->id]);

            return redirect()->route('customer.auth.verify')
                             ->with('otp_sent_via', $dispatch['sent_via'])
                             ->with('otp_channel', $dispatch['channel'])
                             ->with('info', $this->sentMessage($dispatch));
        }

        // New customer (or incomplete profile) → registration
        if ($customer) {
            session(['customer_auth.customer_id' => $customer->id]);
        }

        return redirect()->route('customer.auth.register');
    }

    // ── Step 2a: Registration (new customers only) ───────────────
    public function showRegister()
    {
        if (! session('customer_auth.identifier')) {
            return redirect()->route('customer.auth.start');
        }

        return view('customer.auth.register', [
            'identifier'      => session('customer_auth.identifier'),
            'identifierType'  => session('customer_auth.identifier_type'),
        ]);
    }

    public function processRegister(Request $request)
    {
        if (! session('customer_auth.identifier')) {
            return redirect()->route('customer.auth.start');
        }

        $identifierType = session('customer_auth.identifier_type');

        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
        ];

        // Only require the field not already captured
        if ($identifierType === 'phone') {
            $rules['email'] = ['required', 'email', 'max:255', 'unique:customers,email'];
        } else {
            $rules['phone_number'] = ['nullable', 'string', 'max:30'];
        }

        $validated = $request->validate($rules, [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.required'      => 'An email address is required for OTP delivery.',
            'email.unique'        => 'This email is already registered.',
        ]);

        // Get or create the customer stub
        $customerId = session('customer_auth.customer_id');
        $customer   = $customerId
            ? Customer::find($customerId)
            : $this->authService->createStub(session('customer_auth.identifier'));

        $customer = $this->authService->completeProfile($customer, array_merge(
            $validated,
            [$identifierType === 'email' ? 'email' : 'phone_number' => session('customer_auth.identifier')],
        ));

        session(['customer_auth.customer_id' => $customer->id]);

        // Send OTP
        $dispatch = $this->otpService->send($customer);

        return redirect()->route('customer.auth.verify')
                         ->with('otp_sent_via', $dispatch['sent_via'])
                         ->with('otp_channel', $dispatch['channel'])
                         ->with('info', $this->sentMessage($dispatch));
    }

    // ── Step 2b / 3: OTP Verification ───────────────────────────
    public function showVerify()
    {
        if (! session('customer_auth.customer_id')) {
            return redirect()->route('customer.auth.start');
        }

        $customer = Customer::find(session('customer_auth.customer_id'));
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

        // Mark verified & log in
        $this->authService->markVerified($customer, $this->otpService);
        $this->authService->login($customer);

        // Clear auth session data
        session()->forget(['customer_auth.identifier', 'customer_auth.identifier_type', 'customer_auth.customer_id']);

        return redirect()->route('customer.dashboard')
                         ->with('success', "Welcome, {$customer->first_name}!");
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

    // ── Helpers ──────────────────────────────────────────────────
    private function sentMessage(array $dispatch): string
    {
        return match ($dispatch['sent_via']) {
            'email' => "A 6-digit code was sent to {$dispatch['channel']}.",
            'phone' => "A 6-digit code was sent via SMS to {$dispatch['channel']}.",
            default => 'Code generated — check application logs (no delivery channel available).',
        };
    }
}
