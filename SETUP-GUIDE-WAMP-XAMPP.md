# Wise HRM – Easiest Local Setup with WAMP or XAMPP

Using **WAMP** or **XAMPP** is the **easiest** way to run Wise HRM on your PC. You get PHP + MySQL + a web server in one install. **You do not create the database manually** — the web installer creates it for you (like WordPress).

**Your setup:** The project folder is in **WAMP's htdocs** (e.g. `C:\wamp64\www\Wise-HRM` or `C:\wamp\www\Wise-HRM`). Use the **WAMP** section below.

---

## Why WAMP/XAMPP is easier

| Without WAMP/XAMPP | With WAMP/XAMPP |
|--------------------|------------------|
| Install PHP, then MySQL, then configure PATH | One installer gives you PHP + MySQL + Apache |
| Create database manually in MySQL/phpMyAdmin | **Installer creates the database for you** — you only enter MySQL username and password |
| Run `php artisan serve` or set up a web server | Apache is already running; you put the project in `htdocs` or `www` and open it in the browser |

---

## What you need

1. **WAMP** (Windows) or **XAMPP** (Windows/Mac/Linux) — pick one.
2. **Composer** — to run `composer install` once. [Get Composer](https://getcomposer.org/download/) (install and add to PATH).

---

## Option A: WAMP (project in htdocs)

### 1. Install XAMPP

1. Download: https://www.apachefriends.org/download.html  
2. Choose **PHP 8.1** or **8.2** (required for Laravel).  
3. Install. Remember the folder (e.g. `C:\xampp`).  
4. Open **XAMPP Control Panel**. Start **Apache** and **MySQL** (green = running).

### 2. Put Wise HRM in the web folder

1. Copy your **Wise-HRM** project folder into the XAMPP web root:
   - **Windows:** `C:\xampp\htdocs\wise-hrm`  
   - So the Laravel **`public`** folder is at: `C:\xampp\htdocs\wise-hrm\public`

2. **Important:** The browser must use the `public` folder. So the correct URL will be:
   - `http://localhost/wise-hrm/public`
   - Or create a small redirect (see “Optional: use http://localhost/wise-hrm” below).

### 3. Install Composer dependencies (one time)

1. Open **Command Prompt** or **PowerShell**.
2. Go to the project:
   ```bash
   cd C:\xampp\htdocs\wise-hrm
   ```
3. Run:
   ```bash
   composer install
   ```
   (If `composer` is not found, install Composer and add it to PATH, or use `C:\path\to\composer.phar install`.)

4. Create `.env` and generate key:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   (XAMPP’s `php` is usually in `C:\xampp\php\php.exe`. If `php` is not in PATH, add `C:\xampp\php` to your system PATH, or run `C:\xampp\php\php.exe artisan key:generate`.)

### 4. Run the web installer (no manual database creation)

1. In the browser open: **http://localhost/wise-hrm/public/install**  
   (or **http://localhost/wise-hrm/public** — it will redirect to the installer.)

2. **Step 1 – Welcome:** Click “Start Installation”.

3. **Step 2 – Database:**  
   - **Host:** `127.0.0.1`  
   - **Port:** `3306`  
   - **Database:** `wise_hrm` (or any name you like)  
   - **Username:** `root`  
   - **Password:** leave empty (XAMPP default) or the password you set for MySQL  

   Click **Next**. The installer will **create the database for you** if it doesn’t exist (you do **not** need to create it in phpMyAdmin).

4. **Step 3 – Admin account:** Enter your name, email, and password. Click **Next**.

5. **Step 4 – Company:** Enter company name and optional details. Click **Next**.

6. **Step 5 – Finalize:** Click **Complete Installation**. Tables are created and the installer locks itself.

7. Log in at: **http://localhost/wise-hrm/public/login**

### 5. Storage link (for logo uploads)

In the same folder in the terminal:

```bash
cd C:\xampp\htdocs\wise-hrm
php artisan storage:link
```

---

## Option B: WAMP

### 1. Install WAMP

1. Download: https://www.wampserver.com/  
2. Install (choose PHP 8.1+ if offered).  
3. Start WAMP. The icon should be green (Apache + MySQL running).  
4. Web root is usually: `C:\wamp64\www` (or `C:\wamp\www`).

### 2. Put Wise HRM in the web folder

1. Copy the **Wise-HRM** folder to: `C:\wamp64\www\wise-hrm`  
2. So `public` is at: `C:\wamp64\www\wise-hrm\public`

### 3. Composer and .env (one time)

```bash
cd C:\wamp64\www\wise-hrm
composer install
copy .env.example .env
php artisan key:generate
```

(If `php` or `composer` is not found, add WAMP’s PHP folder to PATH, e.g. `C:\wamp64\bin\php\php8.1.x`.)

### 4. Run the web installer

1. Open: **http://localhost/wise-hrm/public/install**  
2. Database step: **Host** `127.0.0.1`, **Port** `3306`, **Database** `wise_hrm`, **Username** `root`, **Password** (empty or your MySQL password).  
3. The installer **creates the database** for you. Then complete Admin → Company → Finalize.  
4. Log in at: **http://localhost/wise-hrm/public/login**

### 5. Storage link

```bash
php artisan storage:link
```

---

## Why you don’t create the database manually anymore

Wise HRM’s installer works **like WordPress**:

- You only enter **MySQL username and password** (e.g. `root` with no password for XAMPP/WAMP).
- You enter a **database name** (e.g. `wise_hrm`).
- The installer **connects to MySQL** and runs **“CREATE DATABASE IF NOT EXISTS wise_hrm”** for you.
- Then it creates all tables (migrations) and the admin user.

So you **do not** need to:

- Open phpMyAdmin  
- Click “Create database”  
- Type the database name yourself  

You only need MySQL **running** (Apache + MySQL started in XAMPP/WAMP) and the correct **username/password** on the installer’s database step.

---

## Optional: use http://localhost/wise-hrm (without /public)

So that you can open **http://localhost/wise-hrm** instead of **http://localhost/wise-hrm/public**:

1. In **`C:\xampp\htdocs\wise-hrm`** (or WAMP’s `www\wise-hrm`) create a file **`index.php`** with:

```php
<?php
header('Location: public/index.php');
exit;
```

2. Then **http://localhost/Wise-HRM** will redirect to **http://localhost/Wise-HRM/public**.

---

## Quick summary (WAMP – project in htdocs)

1. Install WAMP (PHP 8.1+), start Apache + MySQL.  
2. Cut/copy Wise-HRM to **`C:\wamp64\www\Wise-HRM`** (or `C:\wamp\www\Wise-HRM`).  
3. In terminal: `cd C:\wamp64\www\Wise-HRM` → `composer install` → `copy .env.example .env` → `php artisan key:generate`.  
4. In browser: **http://localhost/Wise-HRM/public/install** → enter MySQL user/password and database name → installer creates the database → complete Admin and Company → log in.  
5. Run `php artisan storage:link` for uploads.

No manual database creation needed.

---

## Going live on cPanel?

To **upload Wise HRM to a cPanel website** and to **update the live site** when you change something locally, see:

- **DEPLOY-CPANEL.md** – First-time deployment and update steps.
- **UPDATE-LIVE-CHECKLIST.txt** – Short checklist for each update.
- **deploy-exclude.txt** – List of files/folders not to upload so you don’t overwrite server config or uploads.
