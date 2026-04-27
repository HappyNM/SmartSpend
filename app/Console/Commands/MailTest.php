<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Signature('mail:test {to? : Recipient email address}')]
#[Description('Send a test email and print SMTP diagnostics')]
class MailTest extends Command
{
    public function handle(): int
    {
        $to = (string) ($this->argument('to') ?: config('mail.from.address'));

        if ($to === '') {
            $this->error('No recipient provided and MAIL_FROM_ADDRESS is empty.');

            return self::FAILURE;
        }

        $this->line('Mailer: ' . (string) config('mail.default'));
        $this->line('Host: ' . (string) config('mail.mailers.smtp.host'));
        $this->line('Port: ' . (string) config('mail.mailers.smtp.port'));
        $this->line('Scheme: ' . (string) config('mail.mailers.smtp.scheme'));
        $this->line('Timeout: ' . (string) config('mail.mailers.smtp.timeout'));
        $this->line('From: ' . (string) config('mail.from.address'));
        $this->line('To: ' . $to);

        try {
            Mail::raw('SmartSpend SMTP test at ' . now()->toDateTimeString(), function ($message) use ($to): void {
                $message->to($to)->subject('SmartSpend SMTP Test');
            });

            $this->info('Test email sent successfully.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Mail send failed: ' . $e->getMessage());
            $this->line('Exception: ' . $e::class);

            report($e);

            return self::FAILURE;
        }
    }
}
