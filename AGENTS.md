# Repository Guidelines

## Project Structure and Architecture

This repository currently contains the product specification in `PRD.md`. Build the application as a Laravel backend with a TypeScript frontend under `resources/js/`:

- `app/` holds domain logic, policies, jobs, requests, and services.
- `database/migrations/` defines schema changes; `database/seeders/` contains repeatable development data.
- `resources/js/pages/public/` contains customer order and invoice pages.
- `resources/js/pages/admin/` contains authenticated dashboard, order, report, and settings pages.
- `resources/js/components/{public,admin,orders,reports,forms,ui}/` contains reusable UI.
- `tests/Feature/` covers HTTP flows and authorization; `tests/Unit/` covers pricing and Apriori rules.

Keep public and admin concerns separate. Public invoices must use `public_token`, never a sequential database ID. Final pricing, status changes, and Apriori eligibility are backend rules.

## Build, Test, and Development Commands

After Laravel is scaffolded and dependencies are installed:

```bash
composer install                 # Install PHP dependencies
npm install                      # Install frontend dependencies
php artisan migrate --seed       # Create local schema and seed data
php artisan test                 # Run PHP tests
npm run build                    # Type-check/build frontend assets
php artisan queue:work           # Process WhatsApp notification jobs
```

Use `php artisan serve` and the configured frontend dev command for local development. Do not make order creation depend on a successful WhatsApp delivery; notifications run through queued jobs.

## Coding Style and Naming

Use PSR-12, `declare(strict_types=1);`, typed properties/returns, early returns, Laravel Form Requests, Policies, Enums, and database transactions for order creation. Name PHP classes in `StudlyCase`; methods, variables, columns, and routes use `camelCase`, `snake_case`, and kebab-case respectively. Use PascalCase for React components and pages, and camelCase for TypeScript functions.

Avoid pricing calculations in the browser beyond display estimates. Normalize WhatsApp numbers server-side and validate uploaded files by MIME type and size.

## Testing Guidelines

Name tests by behavior, for example `PublicOrderTest::test_order_is_saved_when_notification_fails`. Cover price recalculation, token access isolation, admin authorization, order-status transitions, and Apriori exclusion of cancelled/deleted/incomplete orders. Run the relevant tests before opening a pull request.

## Commits and Pull Requests

No Git history exists yet, so use Conventional Commit-style messages: `feat: add public invoice token` or `fix: recalculate delivery address validation`. Keep commits focused. Pull requests need a concise problem/solution description, migration or environment notes, test evidence, linked issue when available, and screenshots for UI changes.
