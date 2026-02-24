# Wise HRM – Phase 1: Core Foundation

Modular HR Management System. Laravel 10+, MySQL, TailwindCSS, Blade. Single-tenant (multi-tenant ready).

**Developer:** M H Morshed  
**Copyright:** © M H Morshed. Built with Laravel.

**New to Laravel or setting up the project?**  
- **Easiest (WAMP/XAMPP, installer creates DB):** **[SETUP-GUIDE-WAMP-XAMPP.md](SETUP-GUIDE-WAMP-XAMPP.md)**  
- **Step-by-step (PHP + Composer + MySQL separately):** **[SETUP-GUIDE-FOR-BEGINNERS.md](SETUP-GUIDE-FOR-BEGINNERS.md)**

**Deploying to a server?**  
- **cPanel hosting** → **[DEPLOY-CPANEL.md](DEPLOY-CPANEL.md)**  
- **AWS (EC2, RDS, optional S3)** → **[DEPLOY-AWS.md](DEPLOY-AWS.md)**

## Requirements

- PHP 8.1+
- Composer 2.x
- MySQL 5.7+ / 8.x
- Node.js 18+ (optional, for Tailwind build; CDN is used by default)

## Install

1. **Clone and install dependencies**
   ```bash
   composer install
   ```

2. **Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run the web installer**
   - Point your web server to the `public` directory.
   - Visit `http://your-domain/install` (or `http://your-domain/` when not installed).
   - Steps: Database → Admin account → Company → Finalize. The installer creates the DB, runs migrations, creates the first admin user and company, then locks itself.

4. **Storage link (for logo uploads)**
   ```bash
   php artisan storage:link
   ```

5. **Optional: run migrations manually (e.g. if not using installer)**
   ```bash
   php artisan migrate
   ```
   Then create an admin user and create `storage/wise_hrm_installed.lock` to lock the installer.

## Structure

- **Modules/Core** – Auth, dashboard, installer, activity logs, layout, role middleware, Companies & Branches CRUD (admin), company logo route.
- **Modules/Employee** – Employee CRUD (role hidden from employee views).
- **Modules/Settings** – Company, logo, primary/secondary/accent colors, font selector, SMTP, timezone, date format, currency.
- **Modules/Attendance, Leave, Payroll** – Phase 2 modules (scaffolded; Attendance & Leave for HR, Payroll for Accounts).

## Roles (access control only; employees do not see their role)

- **admin** – Full access, settings, installer lock.
- **hr** – Employees, HR features.
- **manager** – Manager features.
- **accounts** – Accounts/payroll.
- **employee** – Basic access; role is hidden in UI.

## Security

- Installer is disabled after first run (`storage/wise_hrm_installed.lock`).
- Login/logout are logged in `activity_logs`.
- Keep `.env` out of version control and restrict file permissions in production.

## Progress tracking

See **WISE-HRM-PROGRESS.md** for Phase 1 scope, schema, and progress. Use it to resume work if the session is lost.
