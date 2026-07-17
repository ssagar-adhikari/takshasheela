# Takshasheela Admin

Laravel 13 backend with a single `admin` role and administrator-managed user accounts.

## Included

- Session-based admin login and logout
- Admin-only middleware on every `/admin` route
- User list, create, edit, password update, and delete
- Automatic `admin` role assignment for all accounts created in the backend
- Self-deletion protection for the logged-in administrator
- Seeded initial administrator configured through environment variables
- Feature tests for authentication, authorization, and user management

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000/login`.

Before seeding, set secure initial administrator values in `.env`:

```dotenv
ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=use-a-strong-password
```

The current local development database has already been migrated and seeded. Its local-only credentials are `admin@example.com` / `Admin@12345`; change them immediately from the Users screen.

## Verification

```bash
php artisan test
vendor/bin/pint --test
```
