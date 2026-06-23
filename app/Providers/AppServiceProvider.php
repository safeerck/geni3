<?php

namespace App\Providers;

use App\Listeners\LogSuccessfulLogin;
use App\Models\Setting;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(Login::class, LogSuccessfulLogin::class);
        $this->loadMailConfigFromSettings();
    }

    private function loadMailConfigFromSettings(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) return;

            $map = [
                'mail_host'         => 'mail.mailers.smtp.host',
                'mail_port'         => 'mail.mailers.smtp.port',
                'mail_username'     => 'mail.mailers.smtp.username',
                'mail_password'     => 'mail.mailers.smtp.password',
                'mail_encryption'   => 'mail.mailers.smtp.encryption',
                'mail_from_address' => 'mail.from.address',
                'mail_from_name'    => 'mail.from.name',
            ];

            $settings = Setting::whereIn('key', array_keys($map))->get()->keyBy('key');

            foreach ($map as $settingKey => $configKey) {
                if ($settings->has($settingKey) && $settings[$settingKey]->value !== '') {
                    config([$configKey => $settings[$settingKey]->value]);
                }
            }

            // Switch to SMTP if all core fields are present
            if ($settings->has('mail_host') && $settings['mail_host']->value !== '' &&
                $settings->has('mail_username') && $settings['mail_username']->value !== '') {
                config(['mail.default' => 'smtp']);
            }
        } catch (\Throwable) {
            // Silently skip — happens during migrations before table exists
        }
    }
}
