# Laravel Recon & eColl Payment Gateways

Laravel 8 reference application for integrating **Recon Payment** (redirect + SHA-256) and **eColl 2.0** (redirect + SHA-512 + server webhook). Includes guest checkout for eColl, transaction persistence, and Sanctum API authentication.

## Features

- **Recon Payment** — Build signed redirect URLs from environment config; notify and return callback routes
- **eColl 2.0** — Guest checkout (name, email, amount), transaction records, gateway redirect, browser return URLs, and webhook with hash verification and idempotency for completed payments
- **API auth** — Sanctum token-based `login` and `register` endpoints
- **Laravel UI** — Session-based web authentication (`Auth::routes()`)

## Requirements

- PHP 7.3+ or 8.x
- Composer
- MySQL (or compatible database)
- Node.js & npm (optional, for frontend assets)

## Installation

```bash
git clone <your-repo-url>
cd laravel-payment

composer install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`, then run migrations:

```bash
php artisan migrate
```

Start the development server:

```bash
php artisan serve
```

## Environment variables

Copy values from your payment provider dashboards into `.env`. See [`.env.example`](.env.example) for the full list.

### Recon Payment

| Variable | Description |
|----------|-------------|
| `RECON_SECRET_KEY` | Merchant signing secret |
| `RECON_MERCHANT_CODE` | Merchant code |
| `RECON_RETURN_URL` | Browser return URL after payment |
| `RECON_NOTIFY_URL` | Server notify URL |
| `RECON_CURRENCY` | Currency code (default: `HKD`) |
| `RECON_AMOUNT` | Default payment amount |
| `RECON_DESCRIPTION` | Payment description shown to gateway |
| `RECON_USE_PRODUCTION` | `true` for production URL, `false` for UAT |

### eColl 2.0

| Variable | Description |
|----------|-------------|
| `ECOLL_BASE_URI` | Gateway redirect endpoint |
| `ECOLL_HASH_SECRET` | Shared hash secret |
| `ECOLL_TRAN_TYPE` | Transaction type code |
| `ECOLL_DEPT_CODE` | Department code (TranRefNo) |
| `ECOLL_MAIN_ACTIVITY_CODE` | Activity code (TranRefNo) |
| `ECOLL_APP_SYSTEM_CODE` | Application code (TranRefNo) |
| `ECOLL_RETURN_URI_*` | Success, failed, and cancelled return URLs (configure in eColl portal) |

Never commit real secrets. Use `.env` locally and secure variables in production.

## Routes

### Recon (web)

| Method | Path | Name | Description |
|--------|------|------|-------------|
| GET | `/payment` | `payment` | Payment page with signed Recon URL |
| GET | `/recon` | `recon` | Alternate Recon entry view |
| GET | `/notify` | `notify` | Notify callback view |
| POST | `/return` | `return` | Return callback (displays gateway payload) |

### eColl (web)

| Method | Path | Name | Description |
|--------|------|------|-------------|
| GET | `/ecoll/payment` | `ecoll.payment.create` | Guest payment form |
| POST | `/ecoll/payment/redirect` | `ecoll.payment.redirect` | Create transaction and redirect to gateway |
| GET | `/ecoll/payment/success` | `ecoll.payment.success` | Success return (`?TranRefNo=`) |
| GET | `/ecoll/payment/failed` | `ecoll.payment.failed` | Failed return |
| GET | `/ecoll/payment/cancelled` | `ecoll.payment.cancelled` | Cancelled return |
| POST | `/ecoll/webhook` | `ecoll.webhook` | Server-to-server status update (CSRF exempt) |

### API

| Method | Path | Description |
|--------|------|-------------|
| POST | `/api/login` | Sign in, returns Sanctum token |
| POST | `/api/register` | Register, returns Sanctum token |
| GET | `/api/user` | Current user (requires `auth:sanctum`) |

## Project structure

```
app/
├── Http/Controllers/
│   ├── PaymentController.php      # Recon views and callbacks
│   ├── EcollPaymentController.php # eColl guest checkout and returns
│   ├── EcollWebhookController.php # eColl webhook handler
│   └── Api/AuthController.php     # Sanctum login/register
├── Models/Transaction.php           # Payment records
└── Services/
    ├── ReconPaymentService.php      # Recon URL signing
    ├── EcollPaymentService.php      # eColl transaction + redirect
    └── EcollHashService.php         # eColl SHA-512 hash build/validate
config/
├── payment.php                      # Recon configuration
└── ecoll.php                        # eColl configuration
```

## Payment flows

**Recon:** `PaymentController` delegates to `ReconPaymentService`, which reads `config/payment.php`, builds a SHA-256 signed redirect URL, and passes it to the view.

**eColl:** User submits name, email, and amount → `EcollPaymentService` creates a `transactions` row and redirects to the gateway with an inward hash → eColl calls `POST /ecoll/webhook` → `EcollWebhookController` validates the outward hash and updates transaction status.

## Testing

Tests use in-memory SQLite (see `phpunit.xml`).

```bash
./vendor/bin/phpunit
```

Coverage includes eColl hash signing/validation, Recon URL building, webhook handling, and redirect form validation.

## Security notes

- Keep `RECON_SECRET_KEY` and `ECOLL_HASH_SECRET` out of version control
- The eColl webhook route is excluded from CSRF verification in `VerifyCsrfToken` — protect it with network rules or provider IP allowlists where possible
- eColl payment routes support **guest checkout** (no login required)
- Configure return and webhook URLs in your gateway portal to match your deployed `APP_URL`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
