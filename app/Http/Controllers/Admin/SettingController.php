<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    public function index()
    {
        if (! auth()->user()->isAdmin()) abort(403);
        $settings = Setting::orderBy('key')->get()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (! auth()->user()->isAdmin()) abort(403);

        $section = $request->input('section', 'otp');

        if ($section === 'otp') {
            Setting::set('phone_otp_enabled', $request->has('phone_otp_enabled') ? '1' : '0');
            return redirect()->route('admin.settings.index')
                             ->with('success', 'OTP settings saved.');
        }

        if ($section === 'smtp') {
            $request->validate([
                'mail_host'         => ['required', 'string', 'max:255'],
                'mail_port'         => ['required', 'integer', 'min:1', 'max:65535'],
                'mail_username'     => ['nullable', 'string', 'max:255'],
                'mail_password'     => ['nullable', 'string', 'max:255'],
                'mail_encryption'   => ['nullable', 'in:tls,ssl,starttls,'],
                'mail_from_address' => ['required', 'email'],
                'mail_from_name'    => ['required', 'string', 'max:100'],
            ]);

            $fields = ['mail_host', 'mail_port', 'mail_username', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
            foreach ($fields as $field) {
                Setting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $request->input($field, ''), 'label' => ucwords(str_replace('_', ' ', $field)), 'description' => '']
                );
            }

            if ($request->filled('mail_password')) {
                Setting::updateOrCreate(
                    ['key' => 'mail_password'],
                    ['value' => $request->mail_password, 'label' => 'SMTP Password', 'description' => '']
                );
            }

            $this->applyMailConfig();

            return redirect()->route('admin.settings.index')
                             ->with('success', 'Email (SMTP) settings saved.');
        }

        if ($section === 'test_email') {
            $request->validate(['test_email' => ['required', 'email']]);
            $this->applyMailConfig();
            try {
                Mail::raw('This is a test email from ' . config('app.name') . '. Your SMTP settings are working correctly!', function ($m) use ($request) {
                    $m->to($request->test_email)->subject('SMTP Test — ' . config('app.name'));
                });
                return redirect()->route('admin.settings.index')
                                 ->with('success', 'Test email sent to ' . $request->test_email);
            } catch (\Throwable $e) {
                return redirect()->route('admin.settings.index')
                                 ->with('error', 'Failed to send test email: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.settings.index');
    }

    private function applyMailConfig(): void
    {
        $settings = Setting::getSmtpSettings();

        config([
            'mail.mailers.smtp.host'       => $settings['host'],
            'mail.mailers.smtp.port'       => $settings['port'],
            'mail.mailers.smtp.username'   => $settings['username'],
            'mail.mailers.smtp.password'   => $settings['password'],
            'mail.mailers.smtp.encryption' => $settings['encryption'],
            'mail.from.address'            => $settings['from']['address'],
            'mail.from.name'               => $settings['from']['name'],
            'mail.default'                 => 'smtp',
        ]);
    }
}
