# Wise HRM – Setup Guide (Step by Step for Beginners)

This guide walks you through setting up Wise HRM on your computer. Do each step in order.

---

## Easiest way: use WAMP or XAMPP (no manual database creation)

If you use **WAMP** or **XAMPP**, you get PHP + MySQL + web server in one install, and the **web installer creates the database for you** (like WordPress). You don’t create the database manually in phpMyAdmin.

→ **See [SETUP-GUIDE-WAMP-XAMPP.md](SETUP-GUIDE-WAMP-XAMPP.md)** for the WAMP/XAMPP steps.

The rest of this guide is for when you install **PHP, MySQL, and Composer** separately (no WAMP/XAMPP).

---

## What You Need First (if not using WAMP/XAMPP)

1. **PHP** (version 8.1 or higher) – so your computer can run Laravel.
2. **Composer** – a tool that downloads PHP libraries (like Laravel).
3. **MySQL** – the database where Wise HRM stores data (companies, employees, etc.).
4. **A terminal** – to type commands. On Windows you can use **PowerShell** or **Command Prompt**. In Cursor/VS Code, use the built-in terminal (Terminal → New Terminal).

---

## Step 1: Install PHP (if you don’t have it)

1. Download PHP for Windows: https://windows.php.net/download/
2. Pick the latest **PHP 8.1** or **8.2** “VS16 x64 Non Thread Safe” ZIP.
3. Extract the ZIP to a folder, e.g. `C:\php`.
4. Add PHP to your system PATH:
   - Press **Win + R**, type `sysdm.cpl`, press Enter.
   - Go to **Advanced** → **Environment Variables**.
   - Under “System variables”, select **Path** → **Edit** → **New**.
   - Add the folder where you put PHP (e.g. `C:\php`).
   - OK out of all windows.
5. **Close and reopen** your terminal, then run:
   ```bash
   php -v
   ```
   You should see something like `PHP 8.1.x` or `PHP 8.2.x`. If you see “command not found”, PHP is not in your PATH yet—check the folder you added.

---

## Step 2: Install Composer

1. Go to https://getcomposer.org/download/
2. Download **Composer-Setup.exe** for Windows and run it.
3. When it asks for “PHP executable”, point it to your `php.exe` (e.g. `C:\php\php.exe`).
4. Finish the installer.
5. **Close and reopen** your terminal, then run:
   ```bash
   composer -V
   ```
   You should see a version number like `Composer version 2.x.x`. If not, Composer is not in your PATH—the installer usually adds it; try restarting your computer.

---

## Step 3: Install MySQL (if you don’t have it)

1. Download MySQL: https://dev.mysql.com/downloads/installer/
2. Run the installer. Choose **MySQL Server** and complete the setup.
3. Remember the **root password** you set for MySQL (you’ll use it later).
4. Optional: install **MySQL Workbench** from the same installer—it helps you create databases and see tables.

---

## Step 4: Open the project folder in the terminal

1. Open **Cursor** (or VS Code) and open the **Wise-HRM** folder (File → Open Folder).
2. Open the terminal: **Terminal → New Terminal** (or press **Ctrl + `**).
3. Make sure the terminal is in the Wise-HRM folder. You should see the path ending in `Wise-HRM`. If not, type:
   ```bash
   cd "c:\Users\MohammadMorshed\OneDrive - sampreciousmetals.com\Wise-HRM"
   ```
   (Use your actual path if it’s different.)

---

## Step 5: Run `composer install`

This downloads all PHP libraries the project needs (Laravel, etc.).

1. In the terminal, run:
   ```bash
   composer install
   ```
2. Wait until it finishes (it may take 1–2 minutes).
3. When it’s done you should see a `vendor` folder in your project. If you see errors about PHP version or missing extensions, fix those first (PHP 8.1+ and common extensions like `mbstring`, `openssl`, `pdo_mysql`).

---

## Step 6: Copy `.env.example` to `.env`

The `.env` file holds your app’s secret key and database settings. We create it from the example.

**Option A – Using the terminal (recommended):**

```bash
copy .env.example .env
```

**Option B – Using File Explorer:**

1. In the Wise-HRM folder, find `.env.example`.
2. Copy it (Ctrl+C) and paste (Ctrl+V) in the same folder.
3. Rename the copy to `.env` (no name before the dot, just `.env`).

---

## Step 7: Generate the application key

Laravel needs a random key in `.env` for encryption.

In the terminal, run:

```bash
php artisan key:generate
```

You should see: “Application key set successfully.” Your `.env` file will now contain a line like `APP_KEY=base64:...`.

---

## Step 8: Database – let the installer create it (no manual step)

You **do not** need to create the database manually. The web installer (Step 10) will **create the database for you** when you enter the database name and MySQL username/password — just like WordPress.

If you prefer to create it yourself: open MySQL Workbench or command line, log in as `root`, and run `CREATE DATABASE wise_hrm;`. Then use that name in the installer. Otherwise, skip to Step 9.

---

## Step 9: Put your database details in `.env` (optional if you use the installer)

You can leave `.env` with default DB values (e.g. `wise_hrm`, `root`, and your MySQL password). The **installer** will overwrite these when you complete the “Database” step and will **create the database** if it doesn’t exist. So you only need to ensure MySQL is running and you know the MySQL username and password (e.g. `root` with no password for XAMPP).

If you want to set them now: edit `.env` and set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` to match your MySQL. Save the file.

---

## Step 10: Run the installer in the browser (easiest way)

This will create all tables and the first admin user for you.

1. Start Laravel’s built-in web server. In the terminal (in the Wise-HRM folder), run:
   ```bash
   php artisan serve
   ```
2. Leave this terminal window open. You should see something like: “Server running on [http://127.0.0.1:8000]”.
3. Open your browser and go to: **http://127.0.0.1:8000** or **http://127.0.0.1:8000/install**
4. Follow the installer:
   - **Step 1:** Welcome – click “Start Installation”.
   - **Step 2:** Database – enter **Host** (127.0.0.1), **Port** (3306), **Database** (e.g. `wise_hrm`), **Username** (e.g. `root`), **Password** (your MySQL password, or leave empty for XAMPP). The installer **creates the database for you** if it doesn’t exist. Click “Next”.
   - **Step 3:** Admin account – enter your name, email, and password (twice). Click “Next”.
   - **Step 4:** Company – enter your company name and optional address/phone/email. Click “Next”.
   - **Step 5:** Finalize – click “Complete Installation”.
5. After that, you’ll be redirected to the **login** page. Log in with the email and password you just set.
6. When you’re done testing, you can stop the server by going to the terminal where `php artisan serve` is running and pressing **Ctrl + C**.

---

## Step 11: Create the storage link (for logo uploads)

So that uploaded logos (e.g. company logo in Settings) are available to the app:

1. In the terminal (in the Wise-HRM folder), run:
   ```bash
   php artisan storage:link
   ```
2. You should see: “The [public/storage] link has been created.”  
   There will now be a shortcut in `public/storage` that points to `storage/app/public`. Uploaded logos go there.

---

## Quick reference – all commands in order

Run these from the **Wise-HRM** folder in the terminal:

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Then start the server and use the **web installer** (it will create the database for you):

```bash
php artisan serve
```

Visit **http://127.0.0.1:8000/install**. On the database step, enter your MySQL username and password and a database name (e.g. `wise_hrm`) — the installer creates the database. Complete the wizard, then:

```bash
php artisan storage:link
```

---

## If something goes wrong

- **“composer: command not found”**  
  Composer is not installed or not in your PATH. Install it (Step 2) and restart the terminal (or PC).

- **“php: command not found”**  
  PHP is not installed or not in your PATH. Install PHP (Step 1) and add it to PATH.

- **“Access denied for user” or “could not connect to database”**  
  Check `.env`: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` must match your MySQL setup. Make sure the database exists (Step 8).

- **“No application encryption key has been specified”**  
  You didn’t run `php artisan key:generate` or `.env` doesn’t have `APP_KEY`. Run the command again and ensure `.env` exists.

- **Installer says “Could not connect to database”**  
  Fix `.env` and ensure MySQL is running and the database is created. Then try the installer again.

If you’re stuck, note the **exact error message** and which step you were on—that helps when asking for help.
