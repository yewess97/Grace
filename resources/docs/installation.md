# Installation Guide

---

# Table of Contents

- [Introduction](#introduction)
- [System Requirements](#system-requirements)
- [Required PHP Extensions](#required-php-extensions)
- [Clone the Repository](#clone-the-repository)
- [Install PHP Dependencies](#install-php-dependencies)
- [Install Frontend Dependencies](#install-frontend-dependencies)
- [Environment Configuration](#environment-configuration)
- [Generate Application Key](#generate-application-key)
- [Database Configuration](#database-configuration)
- [Mail Configuration (Optional)](#mail-configuration-optional)
- [Stripe Configuration](#stripe-configuration)
- [Social Authentication Configuration](#social-authentication-configuration)
- [Cache Configuration](#cache-configuration)
- [Sessions Configuration](#sessions-configuration)
- [Storage Configuration](#storage-configuration)
- [Database Migrations](#database-migrations)
- [Database Seeding (Optional)](#database-seeding-optional)
- [Compile Frontend Assets](#compile-frontend-assets)
- [Start the Development Server](#start-the-development-server)
- [Verify Installation](#verify-installation)
- [Optional Configuration](#optional-configuration)
- [Production Optimization](#production-optimization)
- [Troubleshooting](#troubleshooting)

---

# Introduction

This guide explains how to install and configure Grace for local development.

The installation process follows Laravel's recommended workflow while including several additional steps required by this project, such as payment gateway configuration, social authentication, frontend asset compilation, and storage linking.

By the end of this guide, you will have a fully functional development environment ready for testing and further development.

---

# System Requirements

Before installing the project, ensure your development environment satisfies the following requirements:-

## PHP

- PHP 8.2 or later

## Composer

- Composer 2.x

## Database

- MySQL 8.x or compatible

## Node.js

- Node.js 18+ (Recommended)

## NPM

- Latest stable version

## Web Server

Any of the following:

- Laravel Development Server
- Apache
- Nginx
- Laravel Valet

---

# Required PHP Extensions

The following PHP extensions should be enabled:

- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

---

# Clone the Repository

Clone the project using Git.

```bash
git clone https://github.com/yewess97/Grace
```

Move into the project directory.

```bash
cd Grace
```

---

# Install PHP Dependencies

Install all backend dependencies using Composer.

```bash
composer install
```

Composer will download Laravel and all required third-party packages defined in `composer.json`.

---

# Install Frontend Dependencies

Install JavaScript dependencies.

```bash
npm install
```

This command downloads all packages defined in `package.json`.

These packages are used to compile CSS and JavaScript assets.

---

# Environment Configuration

Create a new environment file.

```bash
cp .env.example .env
```

The `.env` file contains environment-specific configuration values and should never be committed to version control.

---

# Generate Application Key

Generate a unique application encryption key.

```bash
php artisan key:generate
```

Laravel uses this key to secure encrypted data, sessions, and cookies.

---

# Database Configuration

Open `.env` and update the database credentials.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=grace_db
DB_USERNAME=root
DB_PASSWORD=
```

Ensure the database already exists before running migrations.

---

# Mail Configuration (Optional)

If email functionality is enabled, configure your preferred mail provider.

Example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=graceglossyfashion@gmail.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="graceglossyfashion@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

# Stripe Configuration

Provide your Stripe credentials.

```env
STRIPE_KEY=

STRIPE_SECRET=
```

Never expose secret keys in public repositories.

---

# Social Authentication Configuration

If using Socialite, configure OAuth credentials.

Examples include:

- Google
- Facebook
- GitHub

```env
LOGIN_SOCIAL_PROVIDERS=google,facebook,github

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=

GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
```

---

# Cache Configuration

The project supports multiple cache drivers.

For development:

```env
CACHE_DRIVER=file
```

For production:

```env
CACHE_DRIVER=redis
```

---

# Sessions Configuration

Example:

```env
SESSION_DRIVER=file
```

Production environments may prefer Redis.

---

# Storage Configuration

Create the symbolic link for public storage.

```bash
php artisan storage:link
```

This makes uploaded files accessible through the public directory.

---

# Database Migrations

Create the database schema.

```bash
php artisan migrate
```

Laravel executes all migration files in chronological order.

---

# Database Seeding (Optional)

Populate the database with sample data.

```bash
php artisan db:seed
```

This step is useful for development and testing.

---

# Compile Frontend Assets

Development mode:

```bash
npm run dev
```

Production build:

```bash
npm run production
```

Production assets are optimized and minified for improved performance.

---

# Start the Development Server

Launch Laravel's built-in server.

```bash
php artisan serve
```

The application will be available at:

```
http://127.0.0.1:8000
```

---

# Verify Installation

After installation, verify the following:

- Home page loads successfully.
- Products are displayed.
- User registration works.
- Login works.
- Images load correctly.
- Database connection succeeds.
- Assets are compiled.
- Storage is accessible.

If all checks pass, the installation is complete.

---

# Optional Configuration

Depending on your environment, you may also configure:

- Redis
- Horizon
- Queue Workers
- Scheduler
- Mail Providers
- Logging Channels
- Cloud Storage
- Debug Tools

These services are optional but recommended for production deployments.

---

# Production Optimization

Before deploying to production, execute the following commands.

```bash
php artisan config:cache

php artisan route:cache

php artisan view:cache

php artisan optimize

composer install --optimize-autoloader --no-dev
```

These optimizations reduce application startup time and improve overall performance.

---

# Troubleshooting

## Composer Installation Issues

Run:

```bash
composer clear-cache
composer install
```

---

## NPM Build Errors

Try:

```bash
rm -rf node_modules

npm install
```

---

## Missing Application Key

Generate a new key.

```bash
php artisan key:generate
```

---

## Database Connection Error

Verify:

- Database server is running.
- Credentials are correct.
- Database exists.

---

## Permission Issues

Ensure the following directories are writable.

- storage/
- bootstrap/cache/

---

## Storage Images Not Appearing?

Run:

```bash
php artisan storage:link
```

---

# Next Steps

After completing the installation, continue with:

➡ **[Deployment](./deployment)**

The next guide explains how to deploy Grace to a production environment, optimize performance, configure web servers, and prepare the application for real-world usage.
