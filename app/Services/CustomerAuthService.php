<?php

namespace App\Services;

use App\Models\Customer;

class CustomerAuthService
{
    public function identifierType(string $identifier): string
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    public function findByIdentifier(string $identifier): ?Customer
    {
        $type = $this->identifierType($identifier);

        return $type === 'email'
            ? Customer::where('email', $identifier)->first()
            : Customer::where('phone_number', $identifier)->first();
    }

    public function createStub(string $identifier): Customer
    {
        $type = $this->identifierType($identifier);

        return Customer::create([
            'email'        => $type === 'email' ? $identifier : null,
            'phone_number' => $type === 'phone' ? $identifier : null,
            'is_verified'  => false,
        ]);
    }

    public function completeProfile(Customer $customer, array $data): Customer
    {
        $customer->update([
            'first_name'   => $data['first_name'] ?? $customer->first_name,
            'last_name'    => $data['last_name'] ?? $customer->last_name,
            'email'        => $data['email'] ?? $customer->email,
            'phone_number' => $data['phone_number'] ?? $customer->phone_number,
        ]);

        return $customer->fresh();
    }

    public function markVerified(Customer $customer, OtpService $otpService): void
    {
        $customer->update(['is_verified' => true]);
        $otpService->clearOtps($customer);
    }

    public function login(Customer $customer): void
    {
        session(['customer_id' => $customer->id]);
        session()->regenerate();
    }

    public function logout(): void
    {
        session()->forget('customer_id');
        session()->regenerate();
    }

    public function currentCustomer(): ?Customer
    {
        $id = session('customer_id');
        return $id ? Customer::find($id) : null;
    }
}
