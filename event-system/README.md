<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
</p>

# Mini Event Ticketing System

This repository contains a small event ticketing system built with Laravel (API) and a Vue 3 SPA frontend (Vite). It supports:

- User and admin roles (RBAC)
- Event creation / editing (admin)
- Seat management (block, book)
- RSVP flow for authenticated users and guests
- Email notifications for RSVP confirmation/cancellation

This README explains how to run the app locally and includes the development test accounts.

## Local development quickstart

Prerequisites:

- PHP 8.x, Composer
- Node.js & npm
- MySQL (or another DB supported by Laravel)

1. Install PHP dependencies

	composer install

2. Copy and configure environment

	cp .env.example .env
	# edit .env and set DB_* and APP_URL as needed

3. Generate app key

	php artisan key:generate

4. Run migrations and seeders

	php artisan migrate --seed

	The database seeders will create test users and data used in development.

5. Install frontend dependencies and run Vite

	npm install
	npm run dev

6. Serve the Laravel app (in another terminal)

	php artisan serve

Open the SPA URL from Vite (usually http://localhost:5173) or the Laravel served URL (http://127.0.0.1:8000) depending on your setup.

## Test accounts (development)

- Admin
  - Email: a1@gmail.com
  - Password: 12345678

- Regular user
  - Email: u1@gmail.com
  - Password: 12345678

Note: these accounts are created by the database seeders in development. If the password doesn't work, re-run the seeders or check `database/seeders` to see the exact credentials.

## Important developer notes

- Authentication: the SPA uses Laravel Sanctum for cookie-based auth. The frontend axios instance is configured to use the Laravel root and send credentials.
- API prefix: the backend API is exposed under `/api/*` routes. Frontend calls should use relative paths like `/api/events` so the configured axios baseURL is applied.
- Mail: in local development use the `log` or Mailtrap/Mailpit driver to capture emails instead of sending real mail.

## Troubleshooting

- Refreshing client routes returns 404: ensure Laravel serves the SPA entry (catch-all route to `resources/views/welcome.blade.php`) or configure your webserver to fallback to `index.php`.
- Duplicate RSVP / seat issues: the system enforces one RSVP per user per event and unique guest email per event. If you hit a DB unique constraint, check the RSVP status (canceled vs confirmed) and the migrations.
- If an API request returns 401/403, confirm you are logged in and that axios is using credentials/token.

If you want, I can add a short 'How to run tests' and 'Common fixes' section next.

## API documentation

A short API reference and a Postman collection are included in the repository under `docs/`.

- `docs/api.md` — human-readable API reference (endpoints, auth notes, examples).
- `docs/postman_collection.json` — a minimal Postman (v2.1) collection you can import into Postman for quick testing.

Authentication notes (quick):

- The SPA uses Laravel Sanctum cookie-based auth. Before calling `/api/login` or other protected endpoints from the SPA, the frontend should request the CSRF cookie via `GET /sanctum/csrf-cookie` and use `axios` with `withCredentials: true` so the session cookie is sent.
- API endpoints are namespaced under `/api/*`. Use relative paths like `/api/events` so the configured axios baseURL is applied.

## SQL schema

Below is the SQL dump of the development database (same content as `event_system.sql`). You can copy/paste this into your local MySQL/MariaDB to recreate the exact schema and seed data used in development.

```sql
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 10:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
	`id` bigint(20) UNSIGNED NOT NULL,
	`title` varchar(255) NOT NULL,
	`description` text DEFAULT NULL,
	`date` date NOT NULL,
	`location` varchar(255) DEFAULT NULL,
	`capacity` int(11) NOT NULL DEFAULT 100,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
	`id` bigint(20) UNSIGNED NOT NULL,
	`uuid` varchar(255) NOT NULL,
	`connection` text NOT NULL,
	`queue` text NOT NULL,
	`payload` longtext NOT NULL,
	`exception` longtext NOT NULL,
	`failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
	`id` int(10) UNSIGNED NOT NULL,
	`migration` varchar(255) NOT NULL,
	`batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
	`email` varchar(255) NOT NULL,
	`token` varchar(255) NOT NULL,
	`created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
	`id` bigint(20) UNSIGNED NOT NULL,
	`tokenable_type` varchar(255) NOT NULL,
	`tokenable_id` bigint(20) UNSIGNED NOT NULL,
	`name` varchar(255) NOT NULL,
	`token` varchar(64) NOT NULL,
	`abilities` text DEFAULT NULL,
	`last_used_at` timestamp NULL DEFAULT NULL,
	`expires_at` timestamp NULL DEFAULT NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rsvps`
--

CREATE TABLE `rsvps` (
	`id` bigint(20) UNSIGNED NOT NULL,
	`event_id` bigint(20) UNSIGNED NOT NULL,
	`user_id` bigint(20) UNSIGNED DEFAULT NULL,
	`email` varchar(255) DEFAULT NULL,
	`guest_name` varchar(255) DEFAULT NULL,
	`guest_email` varchar(255) DEFAULT NULL,
	`status` enum('confirmed','canceled') NOT NULL DEFAULT 'confirmed',
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
	`seat_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
	`id` bigint(20) UNSIGNED NOT NULL,
	`seat_number` varchar(255) DEFAULT NULL,
	`event_id` bigint(20) UNSIGNED NOT NULL,
	`label` varchar(255) NOT NULL,
	`status` enum('available','processing','booked','blocked') DEFAULT 'available',
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
	`id` bigint(20) UNSIGNED NOT NULL,
	`name` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`email_verified_at` timestamp NULL DEFAULT NULL,
	`password` varchar(255) NOT NULL,
	`remember_token` varchar(100) DEFAULT NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
	`role` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Indexes, AUTO_INCREMENT and foreign key constraints are included in the full SQL dump file `event_system.sql` in the project root.

COMMIT;
```

---

If you'd like, I can also:

- trim this SQL to only the CREATE TABLE statements for a compact "schema" section, or
- add a small SQL file under `database/` with only migrations-friendly DDL for easier local import.

</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
