<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    public static function isPhoneOtpEnabled(): bool
    {
        return static::get('phone_otp_enabled', '0') === '1';
    }

    public static function getSmtpSettings(): array
    {
        return [
            'host'       => static::get('mail_host', '127.0.0.1'),
            'port'       => static::get('mail_port', '587'),
            'username'   => static::get('mail_username', ''),
            'password'   => static::get('mail_password', ''),
            'encryption' => static::get('mail_encryption', 'tls'),
            'from'       => [
                'address' => static::get('mail_from_address', 'hello@example.com'),
                'name'    => static::get('mail_from_name', config('app.name', 'Geni')),
            ],
        ];
    }
}
