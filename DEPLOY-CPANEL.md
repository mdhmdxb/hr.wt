# Wise HRM – cPanel Deployment & Update Guide

**Want the simplest step-by-step?** → See **DEPLOY-SIMPLE.md** (short sentences, no jargon).

This guide covers:
1. **First-time upload** – Install Wise HRM on your cPanel-hosted website.
2. **Updating the live site** – When you change code locally, how to push changes to cPanel safely.

---

## Part 1: First-Time Deployment to cPanel

### What you need

- A cPanel hosting account (PHP 8.1+ and MySQL).
- Your domain or subdomain pointed to this hosting.
- FTP/SFTP credentials, or cPanel File Manager, or SSH access (if your host allows it).

### Recommended folder layout on the server

Use **one folder** for the whole Laravel app and point your **domain’s document root** to the `public` folder inside it:

| On server              | Purpose                    |
|------------------------|----------------------------|
| `~/wise-hrm/`          | Full Laravel project       |
| `~/wise-hrm/public/`   | **Document root** for your domain |

So the site runs from `https://yourdomain.com` (no `/public` in the URL).

---

### Step 1: Upload the project

Upload the **entire Wise-HRM project** into a folder on the server, e.g. `wise-hrm` in your home directory.

**Do NOT upload these** (they are recreated on the server or must stay local):

- `vendor/` – will be created by `composer install` on the server
- `node_modules/` – not needed for production
- `.env` – you will create a **new** `.env` on the server
- `storage/wise_hrm_installed.lock` – installer creates this
- `storage/app/owner.enc` – created when you set owner credentials

**You CAN upload:**

- Everything else: `app/`, `bootstrap/`, `config/`, `Modules/`, `public/`, `routes/`, `storage/` (empty dirs/structure), `.env.example`, `composer.json`, `artisan`, etc.

**Ways to upload:**

- **File Manager:** Compress the project on your PC (excluding the items above), upload the ZIP, then Extract in cPanel.
- **FTP/SFTP:** Use FileZilla or similar; upload all project files into e.g. `wise-hrm/`.
- **Git (if available):** Clone your repo into `~/wise-hrm/` (see “Using Git on cPanel” below).

---

### Step 2: Set the document root to `public`

Your domain must serve files from the **`public`** folder, not from the project root.

1. In cPanel go to **Domains** (or **Domains** → **Domains** / **Subdomains**).
2. Click your domain (or subdomain).
3. Find **Document Root** (or “Root Directory”).
4. Set it to the `public` folder of the project, for example:
   - `wise-hrm/public`  
   or the full path your host uses, e.g.:
   - `/home/yourusername/wise-hrm/public`
5. Save.

After this, `https://yourdomain.com` should point at Laravel’s `public` directory.

---

### Step 3: Choose PHP version

1. In cPanel open **MultiPHP Manager** (or **Select PHP Version**).
2. Select the **domain** (or the folder that runs Wise HRM).
3. Set PHP to **8.1** or **8.2** (required for Laravel).
4. Save.

---

### Step 4: Create `.env` on the server

1. In File Manager go to the project root (e.g. `wise-hrm/`).
2. Copy `.env.example` to a new file named `.env`.
3. Edit `.env` and set at least:

```env
APP_NAME="Wise HRM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=                    # leave empty for now
APP_TIMEZONE=UTC

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password
```

- Use the **MySQL database name, username and password** you created in cPanel (MySQL® Databases).
- For many cPanel setups, `DB_HOST=localhost` is correct.

Save the file.

---

### Step 5: Run Composer and Artisan (SSH or “Run Script”)

You need to run these in the **project root** (e.g. `~/wise-hrm/`), not inside `public/`.

**Option A – You have SSH access**

```bash
cd ~/wise-hrm
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

**Option B – No SSH (cPanel “Run Script” or PHP script)**

1. **Composer:**  
   In cPanel, use **Terminal** (if available) and run the same `composer` and `php artisan` commands.  
   If there is no terminal, some hosts have “Setup PHP App” or “Composer” in the dashboard – use that to run `composer install --no-dev` in the project root.

2. **Application key:**  
   Create a one-time PHP file in `public/` (e.g. `genkey.php`) that runs:
   ```php
   <?php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
   $key = 'base64:'.base64_encode(random_bytes(32));
   file_put_contents(__DIR__.'/../.env', preg_replace('/^APP_KEY=.*/m', 'APP_KEY='.$key, file_get_contents(__DIR__.'/../.env')));
   echo 'OK - Key set. Delete this file.';
   ```
   Open `https://yourdomain.com/genkey.php` once, then **delete** `genkey.php`.

3. **Storage link:**  
   If you cannot run `php artisan storage:link`, ensure `storage/app/public` exists and that your host’s “public storage” is the same as `public/storage` (some panels do this automatically).

4. **Permissions:**  
   In File Manager, set permissions for `storage` and `bootstrap/cache` to **755** or **775** (recursive) so the web server can write logs and cache.

---

### Step 6: Run the web installer

1. In the browser open: **https://yourdomain.com/install**
2. Follow the installer: database (usually already set in `.env`), admin user, finish.
3. After installation, remove or restrict the `install` route if your app has a post-install step, and keep your `.env` and `storage/` not web-accessible.

Your Wise HRM site is now live.

---

## Part 2: Updating the Live Site When You Change Something Locally

When you fix bugs or add features on your PC, use this workflow to update the server **without** overwriting server-only files (like `.env` or uploaded files in `storage/`).

### What to upload on each update

- **Upload (overwrite):**  
  `app/`, `bootstrap/`, `config/`, `Modules/`, `public/` (except see below), `routes/`, `database/migrations/` (if you added new ones), `composer.json`, `composer.lock` (if you use it), `artisan`, and any new root files (e.g. `DEPLOY-CPANEL.md`).

- **Do not overwrite on the server:**
  - `.env` – keep the server’s version (DB, APP_URL, etc.).
  - `storage/app/*` – uploaded files, logs, and any generated files (e.g. `storage/app/owner.enc`, `storage/wise_hrm_installed.lock`).
  - `vendor/` – better to regenerate with `composer install` on the server after uploading new `composer.json`/lock.

- **Optional:**  
  If you didn’t change any PHP dependencies, you can skip re-uploading `vendor/` and skip running `composer install`; just upload code and run the Artisan commands below.

### Commands to run on the server after each update

Run these in the **project root** (e.g. `~/wise-hrm/`) after uploading files:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:clear
```

- **If you did not change `composer.json` or `composer.lock`:**  
  You can skip `composer install` and only run the `artisan` commands.

- **If you added new migrations:**  
  `php artisan migrate --force` will run them.

### Short “update checklist”

1. Upload changed files (excluding `.env`, `vendor/`, `node_modules/`, and do not overwrite server `storage/app` content).
2. SSH (or Terminal) into project root:  
   `cd ~/wise-hrm`
3. Run:  
   `composer install --no-dev --optimize-autoloader`  
   (optional if no dependency changes)
4. Run:  
   `php artisan migrate --force`  
   `php artisan config:cache`  
   `php artisan view:clear`
5. Test: open **https://yourdomain.com** and log in.

---

## Part 3: Making Updates Easier (Optional)

### Using Git on cPanel

If your host supports **SSH** and **Git**:

1. On the server:
   ```bash
   cd ~
   git clone https://github.com/yourusername/wise-hrm.git wise-hrm
   cd wise-hrm
   cp .env.example .env
   # Edit .env (DB, APP_URL, APP_KEY)
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   ```
2. Set the domain document root to `~/wise-hrm/public` and run the web installer as in Part 1.

**For future updates:**

```bash
cd ~/wise-hrm
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:clear
```

This keeps the server in sync with your repo and avoids re-uploading everything by FTP.

### Simple “update” script on the server

You can put these commands in a script so you only run one file after each update.

1. Create `~/wise-hrm/update.sh` (only if you have SSH):

```bash
#!/bin/bash
cd "$(dirname "$0")"
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:clear
echo "Update done."
```

2. Make it executable: `chmod +x update.sh`
3. After uploading changed files, run: `./update.sh`

**Security:** Do not expose this script to the web. Run it only via SSH. If your host does not allow SSH, use the manual checklist in Part 2 instead.

---

## Part 4: Quick Reference

| Task              | What to do |
|-------------------|------------|
| First install     | Upload project → set document root to `public` → create `.env` → composer install → key:generate → storage:link → chmod storage & bootstrap/cache → open /install |
| Update after edit | Upload changed files (no .env, no overwrite storage content) → run: composer install (if needed), migrate --force, config:cache, view:clear |
| Don’t overwrite   | `.env`, `storage/app/*` (user uploads, logs, lock files, owner.enc) |
| Document root     | Must be `wise-hrm/public` (or your folder’s `public`) |

---

## Troubleshooting

- **500 error:** Check `storage/logs/laravel.log` on the server; ensure `storage` and `bootstrap/cache` are writable (775).
- **Mix/manifest or assets missing:** If you use Laravel Mix/Vite, run `npm run build` locally and upload the contents of `public/build` (or the compiled assets) to the server’s `public/` as part of your update.
- **Database connection:** Confirm `.env` DB_* values match the MySQL database and user created in cPanel (often `localhost` and the username format like `cpaneluser_dbname`).

If you tell me your host name (e.g. “Hostinger”, “Namecheap”, “GoDaddy”) I can add host-specific notes (e.g. where Document Root is, or how to run Composer without SSH).
