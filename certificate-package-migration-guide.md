# Certificate Package Migration Guide

This repo now uses the `david-oghi/certificate-generation` package through a local compatibility fork so the certificate flow can run on Laravel 10.
It also keeps the legacy certificate generator in place for programs that have not been migrated yet.

## What was changed

1. Added the package repository to Composer.
2. Installed the package as `david-oghi/certificate-generation:^1.0`.
3. Published the package config and migrations.
4. Ran the migration for the new package tables.
5. Rewired the certificate helper so new-design programs use the package renderer while older programs still use the legacy generator.
6. Added a local package fork under `packages/certificate-generation` so the package can run on this Laravel 10 codebase.
7. Added a new top-level admin menu called `Certificate Designer` for the package-driven template workflow.
8. Added program assignment to the new certificate template flow so each program can explicitly opt into the new generator.
9. Moved the package management routes under the admin URL space at `/admin/certificates/manage`.
10. Backfilled existing admin accounts so the new designer routes are available immediately.

## Successful commands

```bash
composer config repositories.certificate-generation vcs https://github.com/davsong01/certificate-generation.git
composer require david-oghi/certificate-generation:^1.0
php artisan vendor:publish --tag=certificates-config
php artisan vendor:publish --tag=certificates-migrations
php artisan migrate
```

## Compatibility adjustments made in this repo

- The upstream package requires Laravel 11+, but this app is on Laravel 10.
- The local fork relaxes the package framework constraints to support Laravel 10.
- The local fork also keeps certificate rendering on Intervention Image v2 and Simple QrCode, which match the existing app stack.
- The package config was updated to use the repo’s legacy font files in `public/certificate_fonts/`.
- The package rendering format was set to JPG to stay compatible with the existing certificate file flow.
- The package admin UI is mounted under the new `Certificate Designer` menu so it does not conflict with the existing legacy certificates area.
- The package management screens now live under the admin route group at `/admin/certificates/manage`.
- Programs with a `certificate_template_id` now use the package renderer; programs without one continue using the legacy generator.
- Existing admin accounts receive the new certificate designer route permissions automatically through a migration.

## Smoke test

The package renderer was verified by creating a sample certificate file through the package service and confirming the output file existed.

## If you are doing this in a newer project

- If the target app is Laravel 11 or newer, you can usually use the upstream package directly.
- If you use the upstream package directly, restore the package’s original dependencies and renderer API:
  - `illuminate/*` `^11.0|^12.0|^13.0`
  - `intervention/image` `^3.11`
  - `endroid/qr-code` `^6.0`
- If your project uses different fonts, update `config/certificates.php` to point at the correct TTF paths before rendering.

## Notes for package maintenance

- The upstream package is a good fit for Laravel 11+ projects.
- This repo-specific fork only exists to keep the package usable on the current Laravel 10 application without a framework upgrade.
- When the host app upgrades to Laravel 11+, the local compatibility fork can be dropped and the package can be consumed directly again.
- In a future upstream version, you would likely want to restore the package’s original Laravel constraint and rendering stack, then keep only the app-side menu and program-assignment integration if the app still needs the hybrid workflow.
