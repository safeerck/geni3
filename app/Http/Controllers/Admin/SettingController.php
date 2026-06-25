<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
            $request->validate(['phone_otp_enabled' => ['required', 'in:0,1']]);
            Setting::set('phone_otp_enabled', $request->phone_otp_enabled);
            return redirect()->route('admin.settings.index')
                             ->with('success', 'OTP settings saved.')
                             ->with('active_tab', 'otp');
        }

        if ($section === 'smtp') {
            $request->validate([
                'mail_host'       => ['required', 'string', 'max:255'],
                'mail_port'       => ['required', 'integer', 'min:1', 'max:65535'],
                'mail_username'   => ['nullable', 'string', 'max:255'],
                'mail_password'   => ['nullable', 'string', 'max:255'],
                'mail_encryption' => ['nullable', 'in:tls,ssl,starttls,'],
                'mail_from_address' => ['required', 'email'],
                'mail_from_name'    => ['required', 'string', 'max:100'],
            ]);

            $fields = ['mail_host','mail_port','mail_username','mail_password',
                       'mail_encryption','mail_from_address','mail_from_name'];

            foreach ($fields as $field) {
                // Don't overwrite password if left blank
                if ($field === 'mail_password' && ! $request->filled('mail_password')) {
                    continue;
                }
                Setting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $request->input($field, ''),
                     'label' => ucwords(str_replace('_', ' ', $field)),
                     'description' => '']
                );
            }

            // Override live config so it takes effect immediately
            $this->applyMailConfig();

            return redirect()->route('admin.settings.index')
                             ->with('success', 'Email (SMTP) settings saved.')
                             ->with('active_tab', 'smtp');
        }

        if ($section === 'test_email') {
            $request->validate(['test_email' => ['required', 'email']]);
            $this->applyMailConfig();
            try {
                Mail::raw('This is a test email from ' . config('app.name') . '. Your SMTP settings are working correctly!', function ($m) use ($request) {
                    $m->to($request->test_email)->subject('SMTP Test — ' . config('app.name'));
                });
                return redirect()->route('admin.settings.index')
                                 ->with('success', 'Test email sent to ' . $request->test_email)
                                 ->with('active_tab', 'smtp');
            } catch (\Throwable $e) {
                return redirect()->route('admin.settings.index')
                                 ->with('error', 'Failed to send test email: ' . $e->getMessage())
                                 ->with('active_tab', 'smtp');
            }
        }

        return redirect()->route('admin.settings.index');
    }

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];

        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }

        config(['mail.default' => 'smtp']);
    }
}

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
    }
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
    }
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
    }

    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
    }

    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
        config(['mail.default' => 'smtp']);
    }
    private function applyMailConfig(): void
    {
    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
        config(['mail.default' => 'smtp']);
    }
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
    }
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
        config(['mail.default' => 'smtp']);
    }

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
     */
    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
        config(['mail.default' => 'smtp']);
    }

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
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
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        });
    }

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
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
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
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
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
    }

    /**
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
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
        config(['mail.default' => 'smtp']);
    }
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
     */
    private function applyMailConfig(): void
    {
        $settings = Setting::getSmtpSettings();

        config([
            'mail.mailers.smtp.host'       => $settings['host'],
            'mail.mailers.smtp.port'       => $settings['port'],
            'mail.mailers.smtp.username'   => $settings['username'],
            'mail.mailers.smtp.password'   => $settings['password'],
            'mail.mailers.smtp.encryption' => $settings['encryption'],
    }

    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
            'mail.default'                 => 'smtp',
        ]);
    }
     * Apply mail configuration from settings to Laravel's config
     * This makes the settings take effect immediately
     */
     * This makes the settings take effect immediately
     */
    private function applyMailConfig(): void
    {
        $settings = Setting::getSmtpSettings();

        config([
            'mail.mailers.smtp.host'       => $settings['host'],
            'mail.mailers.smtp.port'       => $settings['port'],
            'mail.mailers.smtp.username'   => $settings['username'],
            'mail.mailers.smtp.password'   => $settings['password'],
            'mail.mailers.smtp.encryption' => $settings['encryption'],
        });
    }
            'mail.default'                 => 'smtp',
        ]);
        ]);
    }

    private function applyMailConfig(): void
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
    {
        $map = [
            'mail_host'         => 'mail.mailers.smtp.host',
            'mail_port'         => 'mail.mailers.smtp.port',
            'mail_username'     => 'mail.mailers.smtp.username',
            'mail_password'     => 'mail.mailers.smtp.password',
            'mail_encryption'   => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name'    => 'mail.from.name',
        ];
        foreach ($map as $settingKey => $configKey) {
            $val = Setting::get($settingKey);
            if ($val !== null) {
                config([$configKey => $val]);
            }
        }
        config(['mail.default' => 'smtp']);
    }
}
