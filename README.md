# WordPress + Mailexam

Minimal [WordPress](https://wordpress.org/) example that sends test mail through [Mailexam](https://mailexam.io/) SMTP via `wp_mail()` and a must-use plugin.

Based on the [Mailexam WordPress guide](https://wiki.mailexam.ru/en/examples/wordpress/).

## What you need

- A Mailexam account and a project with SMTP credentials.
- WordPress 6+ and PHP 8.0+ (for an existing site on the host), or Docker for local debugging.

From your Mailexam welcome email or dashboard:

| Variable | Description |
|----------|-------------|
| `MAILEXAM_LOGIN` | SMTP login (for example, `xxxxx`) |
| `MAILEXAM_PASSWORD` | SMTP password (paired with the login) |
| Host | `{MAILEXAM_LOGIN}.mailexam.io` (set in the MU plugin) |

## Quick start (host)

Use this on an **existing** WordPress installation.

1. Copy must-use plugins into your site:

```bash
cp -R wp-content/mu-plugins/* /path/to/wordpress/wp-content/mu-plugins/
```

2. Add Mailexam constants to `wp-config.php` before `/* That's all, stop editing! */`:

```php
define('MAILEXAM_LOGIN', 'YOUR_LOGIN');
define('MAILEXAM_PASSWORD', 'YOUR_PASSWORD');
define('MAILEXAM_PORT', 587);
define('MAIL_FROM', 'noreply@example.test');
```

Do not commit real passwords to git.

3. Send a test message via the REST endpoint:

```bash
curl -X POST https://your-site.test/wp-json/mailexam/v1/mail/test \
  -H 'Content-Type: application/json' \
  -d '{"to":"user@example.test","subject":"Test","body":"Hello"}'
```

The message appears in the Mailexam dashboard → your project → inbox.

### WP-CLI alternative

```bash
wp eval "var_export(wp_mail('user@example.test', 'Check', 'Hello from WordPress'));"
```

## Environment variables

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `MAILEXAM_LOGIN` | yes | — | SMTP login; host becomes `{login}.mailexam.io` |
| `MAILEXAM_PASSWORD` | yes | — | SMTP password |
| `MAILEXAM_PORT` | no | `587` | SMTP port (`587`, `2525`, `465`, or `25`) |
| `MAIL_FROM` | no | `noreply@example.test` | Sender address |

## Project layout

```
.
├── wp-content/mu-plugins/
│   ├── mailexam-smtp.php       # Mailexam SMTP via phpmailer_init
│   └── mailexam-mail-test.php  # POST /wp-json/mailexam/v1/mail/test
├── .env.example
└── docker-compose.yml          # for local debugging only
```

## Docker (debugging)

Docker is provided for local debugging. For production or an existing site, use the host steps above.

```bash
cp .env.example .env
# edit .env with your Mailexam credentials

docker compose up -d
```

Open http://127.0.0.1:8080 and complete the one-time WordPress installation wizard.

Then send a test message:

```bash
curl -X POST http://127.0.0.1:8080/wp-json/mailexam/v1/mail/test \
  -H 'Content-Type: application/json' \
  -d '{"to":"user@example.test","subject":"Test","body":"Hello"}'
```

Must-use plugins are mounted from `./wp-content/mu-plugins`. Mailexam constants are injected via `WORDPRESS_CONFIG_EXTRA` from `.env`.

## CI

Set these secrets in your CI environment:

```yaml
variables:
  MAILEXAM_LOGIN: $MAILEXAM_LOGIN
  MAILEXAM_PASSWORD: $MAILEXAM_PASSWORD
  MAILEXAM_PORT: "587"
  MAIL_FROM: "noreply@example.test"
```

After sending a message in a test, verify delivery via the [Mailexam API](https://mailexam.io/api).

## Troubleshooting

**Mail does not use SMTP**

- Plugins must be in `wp-content/mu-plugins/`, not `plugins/`.
- Check that another SMTP plugin does not override `phpmailer_init`.

**TLS or authentication failed**

- Host must be `{login}.mailexam.io`, username the same login from the email.
- Login and password must come from the same Mailexam project.

**REST endpoint returns 404**

- Complete WordPress installation first.
- Try `/index.php?rest_route=/mailexam/v1/mail/test` if permalinks are not configured.

**Message not in the dashboard**

- Open the inbox of the same Mailexam project.
- Enable `WP_DEBUG` for diagnostics.

## See also

- [Mailexam WordPress guide (wiki)](https://wiki.mailexam.ru/en/examples/wordpress/)
- [Laravel](https://github.com/mailexam/Laravel), [Symfony](https://github.com/mailexam/Symfony), [Phalcon](https://github.com/mailexam/Phalcon) — other PHP stacks
- [wp_mail()](https://developer.wordpress.org/reference/functions/wp_mail/)
- [Mailexam API documentation](https://mailexam.io/api)
