<?php

namespace McArrowsmithPackages\SmtpDebug\Laravel;

use Illuminate\Console\Command;
use McArrowsmithPackages\SmtpDebug\SmtpDebugger;

class SmtpDebugCommand extends Command
{
    protected $signature = 'smtp:debug
        {to : Recipient email address}
        {--mailer= : Mailer from config/mail.php to use (default: mail.default)}
        {--level=3 : Debug level 0-4}';

    protected $description = 'Send a test email using the configured SMTP credentials and show the full debug output';

    public function handle(): int
    {
        $name = $this->option('mailer') ?? config('mail.default');
        $config = config("mail.mailers.{$name}");

        if (! $config || ($config['transport'] ?? null) !== 'smtp') {
            $this->error("Mailer [{$name}] is not configured with the smtp transport.");

            return self::FAILURE;
        }

        $port = (int) ($config['port'] ?? 587);

        $this->info("Using mailer [{$name}]: {$config['host']}:{$port}");

        $ok = SmtpDebugger::send(
            host: (string) $config['host'],
            port: $port,
            username: (string) ($config['username'] ?? ''),
            password: (string) ($config['password'] ?? ''),
            from: (string) config('mail.from.address'),
            to: (string) $this->argument('to'),
            encryption: $this->encryption($config, $port),
            debugLevel: (int) $this->option('level'),
            output: fn (string $line) => $this->line($line),
        );

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Older Laravel uses "encryption" (tls/ssl); Laravel 11+ uses "scheme" (smtp/smtps).
     */
    protected function encryption(array $config, int $port): string
    {
        if (! empty($config['encryption'])) {
            return $config['encryption'];
        }

        if (($config['scheme'] ?? null) === 'smtps' || $port === 465) {
            return 'ssl';
        }

        return 'tls';
    }
}
