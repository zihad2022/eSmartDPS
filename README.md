# eSmartDPS

eSmartDPS is a multi-tenant Laravel SaaS for cooperative savings/DPS organizations. It manages organization users, members, shares, recurring payments, investment projects, ledgers, packages, subscriptions, invoices, support tickets, and audit activity.

## Main portals

- **Platform admin:** clients, packages, invoices, administrators, settings, permissions, and support.
- **Client organization:** members, payments, projects, accounting ledgers, child users, organization settings, and tickets.
- **Member:** account access and payment-proof submission.

## Local setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate:fresh --seed
npm install
npm run dev
php artisan serve
```

Configure database, mail, bKash, SSLCommerz, and SMS values in `.env`. Real credentials must not be committed.

## Architecture and database report

See [`docs/DATABASE_MODEL_REFACTOR.md`](docs/DATABASE_MODEL_REFACTOR.md) for the entity map, refactor details, schema conventions, validation results, and production migration warning.

## Admin architecture

Admin routes live in `routes/admin.php`. Controllers under `app/Http/Controllers/Admin` are intentionally thin: validation is handled by dedicated Form Requests and business/database behavior is handled by classes under `app/Actions/Admin` or the appropriate domain Action namespace.

The admin panel includes permission-scoped modules for dashboard, clients and impersonation, packages, invoices and email delivery, support tickets and Livewire chat, administrators, roles, activity history, exports, settings, and compressed database backups.

Before seeding admin users outside `local` or `testing`, set all `SEED_*_PASSWORD` values in `.env`. Production seeding stops rather than creating an administrator with a known default password.

## Validation commands

Run these in an environment with the required PHP extensions and a configured test database:

```bash
composer install
npm ci
npm run build
php artisan optimize:clear
php artisan migrate --seed
php artisan test
./vendor/bin/pint --test
```

Required PHP extensions include PDO for the selected database, mbstring, DOM/XML, fileinfo, OpenSSL, and zlib.

## Production database warning

Do not run `php artisan migrate:fresh` on production. Back up the existing database, review forward migrations, run `php artisan migrate`, and verify admin roles/permissions after deployment.

# eSmartDPS
