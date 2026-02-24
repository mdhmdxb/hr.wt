# Wise HRM – Put It on Your Website (Simple Steps)

Think of it like this: **your PC has the app. Your website (cPanel) is another computer.** We will copy the app to that computer and turn it on. Then, when you change something on your PC, we will copy only what changed to the website.

---

## Part 1: First Time – Install on Your Website

### Step 1: Make a ZIP of the project on your PC (but leave some things out)

1. Open your **Wise-HRM** folder (the one you use with WAMP).
2. **Do not put these in the ZIP:**  
   - the folder named **vendor**  
   - the file named **.env**
3. Select everything else (all other folders and files) and create a ZIP file.  
   - Name it something like **wise-hrm-upload.zip**.

**Why?** The website will build its own **vendor** and create its own **.env**. We don’t copy yours.

---

### Step 2: Create a database on cPanel

1. Log in to **cPanel** (your hosting panel).
2. Open **MySQL® Databases** (or “MySQL Databases”).
3. Create a **new database**. Write down its name (e.g. `myuser_wisehrm`).
4. Create a **new user**. Write down **username** and **password**.
5. **Add the user to the database** and give it “All Privileges”.  
   So now the database has a name, a username, and a password. Keep these safe.

---

### Step 3: Upload the ZIP and extract it

1. In cPanel open **File Manager**.
2. Go to your **home** folder (often `public_html`’s parent or “home”).
3. Click **Upload**.
4. Upload **wise-hrm-upload.zip**.
5. After upload, **right‑click the ZIP** → **Extract**.
6. Extract into a **new folder** named **wise-hrm**.  
   So you have a folder: **wise-hrm** with all the app files inside.

---

### Step 4: Tell your domain to use the “public” folder

Your app has a small **public** folder inside **wise-hrm**. The website must open that folder, not the big one.

1. In cPanel go to **Domains** (or “Domains” / “Subdomains”).
2. Click your **domain** (the one you want to use for Wise HRM).
3. Find **Document Root** (or “Root Directory”).
4. Change it from whatever it is now to:  
   **wise-hrm/public**  
   (or the full path, e.g. **/home/yourusername/wise-hrm/public**)
5. Save.

Now when someone opens **https://yourdomain.com**, the server will use the **public** folder. Good.

---

### Step 5: Set PHP version

1. In cPanel open **MultiPHP Manager** (or “Select PHP Version”).
2. Select your **domain**.
3. Choose **PHP 8.1** or **8.2**.
4. Save.

---

### Step 6: Create the .env file on the server

1. In **File Manager** go inside the **wise-hrm** folder.
2. Find the file **.env.example**.  
   (If you don’t see it, turn on “Show Hidden Files”.)
3. **Copy** it. Name the copy **.env**.
4. **Edit** the **.env** file. Change these lines to match your database and site:

- **APP_URL** = your real website address, e.g. `https://yourdomain.com`
- **APP_DEBUG** = `false`
- **DB_DATABASE** = the database name you wrote down
- **DB_USERNAME** = the database username you wrote down
- **DB_PASSWORD** = the database password you wrote down

Leave **APP_KEY** empty for now. Save the file.

---

### Step 7: Run the “install” commands on the server

The app needs to run a few commands **on the server**. You need one of these:

- **Terminal / SSH** in cPanel, or  
- A way to run **PHP** and **Composer** (some hosts have “Setup PHP App” or “Composer”).

**If you have Terminal (SSH):**

1. Open **Terminal** in cPanel.
2. Type (replace with your real folder name if different):

```text
cd wise-hrm
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
```

3. Then set permissions:  
   In File Manager, right‑click the **storage** folder → **Change Permissions** → set to **755** (and “Recurse” into subdirectories).  
   Do the same for **bootstrap/cache** if that folder exists.

**If you don’t have Terminal:**  
Your host may have a “Run script” or “Composer” button. Use that to run the same commands in the **wise-hrm** folder. If you’re not sure, ask your hosting support: “How do I run `composer install` and `php artisan` in my project folder?”

---

### Step 8: Open the installer in your browser

1. In your browser go to: **https://yourdomain.com/install**
2. Follow the steps on the screen (database is already in .env, so it may just ask for admin user and company).
3. When it says “Installation complete”, you’re done.

Your Wise HRM is now **live** on your website.

---

## Part 2: When You Change Something on Your PC – How to Update the Website

You fixed a bug or added a feature **on your PC**. Now you want the **website** to have the same change.

### What “update” means (simple)

1. **Copy** the files you changed from your PC to the server (into the **wise-hrm** folder), **without** overwriting the server’s **.env** or the **storage** uploads/logs.
2. On the server, run a few short commands so the site uses the new code.

---

### Update – Step 1: Copy only what changed

- **Do copy (overwrite on server):**  
  The folders and files you actually changed (e.g. a Blade file, a PHP file, a new migration).  
  Examples: **Modules/** (if you changed something there), **app/**, **routes/**, **config/**, **database/migrations/** (if you added one), **public/** (if you changed CSS/JS), etc.
- **Do NOT overwrite on the server:**  
  - **.env** (the server’s .env has the real database and URL; keep it.)  
  - **storage/** inside the app (that’s where uploads and logs are; don’t replace with your PC’s storage.)

**Easy way:** Use FTP or File Manager. Upload only the **changed** files into the same path inside **wise-hrm**. Skip **.env** and don’t replace the whole **storage** folder with your PC’s.

---

### Update – Step 2: Run these on the server (in the wise-hrm folder)

Open **Terminal** (or your host’s “Run script”) in the **wise-hrm** folder and run:

```text
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan view:clear
```

- If you **did not** add or change any PHP packages, you can skip the first line.  
- **migrate** updates the database if you added new migrations.  
- The last two lines clear caches so the site uses the new code.

---

### Update – Short checklist

1. Upload changed files (no .env, don’t overwrite server storage).
2. In **wise-hrm** on the server run:  
   `composer install --no-dev --optimize-autoloader`  
   (if you changed dependencies)
3. Run:  
   `php artisan migrate --force`  
   `php artisan config:cache`  
   `php artisan view:clear`
4. Open your site in the browser and test.

That’s it. You’re “teaching the server” the new version of your app.

---

## Quick reminder

| I want to…              | Do this… |
|-------------------------|----------|
| Install for the first time | Part 1: ZIP (no vendor, no .env) → upload → extract → set document root to **wise-hrm/public** → create DB → create .env → run composer + artisan → open /install |
| Update after I changed code on my PC | Part 2: Upload only changed files (no .env) → on server run: composer install (if needed), migrate --force, config:cache, view:clear |

If something doesn’t work, check: (1) Document root is **wise-hrm/public**. (2) PHP is 8.1 or 8.2. (3) .env has the correct database name, user, password, and APP_URL.
