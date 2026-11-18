# mcarrowsmith-packages/smtp-debug

A trivial SMTP connectivity tester built on PHPMailer. Sends a test email and dumps the full SMTP conversation.

## Install

```bash
composer require --dev mcarrowsmith-packages/smtp-debug
```

## Laravel

The service provider is auto-discovered. The command uses the SMTP credentials already configured in `config/mail.php` / `.env` (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, ...):

```bash
php artisan smtp:debug you@example.com
```

Use a different configured mailer, or change verbosity:

```bash
php artisan smtp:debug you@example.com --mailer=ses-smtp --level=4
```

## Standalone CLI

```bash
vendor/bin/smtp-debug --host=smtp.example.com --port=587 \
  --username=user --password=secret \
  --from=me@example.com --to=you@example.com --encryption=tls --level=3
```

## In code

```php
use McArrowsmithPackages\SmtpDebug\SmtpDebugger;

SmtpDebugger::send('smtp.example.com', 587, 'user', 'secret', 'me@example.com', 'you@example.com');
```

### Options

| Option       | Values                      | Default |
|--------------|-----------------------------|---------|
| `encryption` | `tls` (STARTTLS), `ssl`, `none` | `tls` |
| `level`      | `0`–`4`                     | `3`     |
