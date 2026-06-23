<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $smtpSettings = [
            ['key'=>'mail_host',         'value'=>'127.0.0.1',          'label'=>'SMTP Host',           'description'=>'Your mail server hostname'],
            ['key'=>'mail_port',         'value'=>'587',                 'label'=>'SMTP Port',           'description'=>'Typically 587 (TLS) or 465 (SSL)'],
            ['key'=>'mail_username',     'value'=>'',                    'label'=>'SMTP Username',       'description'=>'Your SMTP account username or email'],
            ['key'=>'mail_password',     'value'=>'',                    'label'=>'SMTP Password',       'description'=>'Your SMTP account password'],
            ['key'=>'mail_encryption',   'value'=>'tls',                 'label'=>'Encryption',          'description'=>'tls or ssl'],
            ['key'=>'mail_from_address', 'value'=>'hello@example.com',   'label'=>'From Address',        'description'=>'The email address all mail is sent from'],
            ['key'=>'mail_from_name',    'value'=>config('app.name','Geni'), 'label'=>'From Name',       'description'=>'The sender name shown in the inbox'],
        ];

        foreach ($smtpSettings as $row) {
            DB::table('settings')->updateOrInsert(
                ['key' => $row['key']],
                array_merge($row, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'mail_host','mail_port','mail_username','mail_password',
            'mail_encryption','mail_from_address','mail_from_name',
        ])->delete();
    }
};
