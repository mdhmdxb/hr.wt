# Wise HRM – Deploy on AWS

This guide covers deploying Wise HRM on **Amazon Web Services (AWS)** using common options: **EC2** (your own server) and **Laravel Forge–style** manual setup. You can later add RDS (MySQL) and S3 for files if you want.

---

## What You Need

- An **AWS account**.
- Basic use of **EC2** (create instance, SSH, open ports).
- A **domain** (optional; you can use the EC2 public IP for testing).
- SSH key pair for EC2.

---

## Part A: EC2 Server (Ubuntu)

### A1. Launch an EC2 Instance

1. Log in to **AWS Console** → **EC2** → **Instances** → **Launch instance**.
2. **Name:** e.g. `wise-hrm`.
3. **AMI:** Ubuntu Server 22.04 LTS.
4. **Instance type:** e.g. `t2.micro` (free tier) or `t3.small` for production.
5. **Key pair:** Create new or use existing. **Download the `.pem` file** and keep it safe (you need it to SSH).
6. **Network:** Create or use a security group. Add rules:
   - **SSH (22)** – your IP (or 0.0.0.0/0 only for testing; restrict in production).
   - **HTTP (80)** – 0.0.0.0/0.
   - **HTTPS (443)** – 0.0.0.0/0.
7. **Storage:** 8–20 GB is enough to start.
8. Launch the instance. Note the **Public IP** (e.g. `3.xx.xx.xx`).

---

### A2. Connect and Install Software (PHP, Composer, MySQL, Nginx)

1. **Connect via SSH** (from your PC, in PowerShell or a terminal). Replace `your-key.pem` and `ubuntu@3.xx.xx.xx`:
   ```bash
   ssh -i "path/to/your-key.pem" ubuntu@3.xx.xx.xx
   ```

2. **Update system:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

3. **Install PHP 8.2, Nginx, MySQL, and extensions:**
   ```bash
   sudo apt install -y software-properties-common
   sudo add-apt-repository ppa:ondrej/php -y
   sudo apt update
   sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd unzip nginx mysql-server
   ```

4. **Install Composer:**
   ```bash
   cd ~
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

5. **Secure MySQL (optional but recommended):**
   ```bash
   sudo mysql_secure_installation
   ```
   Set root password and answer the prompts. Then create a database and user for Wise HRM:
   ```bash
   sudo mysql -u root -p
   ```
   In MySQL:
   ```sql
   CREATE DATABASE wise_hrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'wisehrm'@'localhost' IDENTIFIED BY 'YourStrongPassword';
   GRANT ALL PRIVILEGES ON wise_hrm.* TO 'wisehrm'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

---

### A3. Deploy the Application

1. **Create web root and clone/upload project.**  
   If you use **Git** (recommended):
   ```bash
   sudo mkdir -p /var/www
   sudo chown ubuntu:ubuntu /var/www
   cd /var/www
   git clone https://github.com/your-username/wise-hrm.git
   cd wise-hrm
   ```
   If you don’t use Git: upload the project with **scp** or **SFTP** to `/var/www/wise-hrm` (e.g. FileZilla, WinSCP). Then:
   ```bash
   cd /var/www/wise-hrm
   ```

2. **Install dependencies and env:**
   ```bash
   composer install --optimize-autoloader --no-dev
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure `.env`:**
   ```bash
   nano .env
   ```
   Set at least:
   ```env
   APP_NAME="Wise HRM"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://YOUR_EC2_PUBLIC_IP
   # Or with domain: APP_URL=https://hrm.yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=wise_hrm
   DB_USERNAME=wisehrm
   DB_PASSWORD=YourStrongPassword
   ```
   Save (Ctrl+O, Enter, Ctrl+X in nano).

4. **Run migrations and storage link:**
   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```

5. **Permissions:**
   ```bash
   sudo chown -R www-data:www-data /var/www/wise-hrm
   sudo chmod -R 755 /var/www/wise-hrm
   sudo chmod -R 775 /var/www/wise-hrm/storage /var/www/wise-hrm/bootstrap/cache
   ```

---

### A4. Configure Nginx

1. Create Nginx site config:
   ```bash
   sudo nano /etc/nginx/sites-available/wise-hrm
   ```

2. Paste (replace `your_domain_or_IP` with your EC2 public IP or domain):
   ```nginx
   server {
       listen 80;
       server_name your_domain_or_IP;
       root /var/www/wise-hrm/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
           fastcgi_hide_header X-Powered-By;
       }
   }
   ```

3. Enable and test:
   ```bash
   sudo ln -s /etc/nginx/sites-available/wise-hrm /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   ```

4. In browser: `http://YOUR_EC2_IP/install` → complete the installer, then log in at `http://YOUR_EC2_IP/login`.

---

### A5. (Optional) Use a Domain and HTTPS with Let’s Encrypt

1. Point your domain’s **A record** to the EC2 public IP.
2. On the server:
   ```bash
   sudo apt install certbot python3-certbot-nginx -y
   sudo certbot --nginx -d hrm.yourdomain.com
   ```
3. Update `.env`: `APP_URL=https://hrm.yourdomain.com`.
4. Reload PHP so env is picked up: `sudo systemctl reload php8.2-fpm` (optional; new requests will use new env).

---

## Part B: AWS with RDS (MySQL) and Optional S3

Use this when you want **database on RDS** (managed MySQL) instead of MySQL on the same EC2.

### B1. Create RDS MySQL Instance

1. **AWS Console** → **RDS** → **Create database**.
2. **Engine:** MySQL 8.x.
3. **Template:** Dev/Test or Production.
4. **Settings:** Master username, password; note them.
5. **Instance:** e.g. db.t3.micro (free tier eligible).
6. **Storage:** default.
7. **Connectivity:** Same VPC as your EC2; **Public access** = No (recommended). Put EC2 and RDS in same security group or allow EC2’s security group on RDS port 3306.
8. Create. Note **Endpoint** (e.g. `xxx.region.rds.amazonaws.com`).

### B2. Configure Wise HRM to Use RDS

On EC2, edit `.env`:
```env
DB_HOST=your-rds-endpoint.region.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=wise_hrm
DB_USERNAME=admin
DB_PASSWORD=YourRDSPassword
```

Create the database and user in RDS (connect from EC2 or a client with access):
```sql
CREATE DATABASE wise_hrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Create user if not using master; grant privileges.
```

Then on EC2:
```bash
cd /var/www/wise-hrm
php artisan migrate --force
```

### B3. (Optional) S3 for Logos/Uploads

1. Create an S3 bucket and IAM user with programmatic access (read/write to that bucket).
2. Install Laravel S3 driver: `composer require league/flysystem-aws-s3-v3`.
3. In `.env` add:
   ```env
   FILESYSTEM_DISK=s3
   AWS_ACCESS_KEY_ID=
   AWS_SECRET_ACCESS_KEY=
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=your-bucket-name
   ```
4. In `config/filesystems.php`, ensure the `s3` disk is configured (Laravel default). In your Settings/upload code, use `Storage::disk('s3')` for logo uploads so they go to S3 instead of local `storage/app/public`.

---

## Part C: Quick Reference (AWS)

| Task              | Where / Command |
|-------------------|------------------|
| SSH to EC2        | `ssh -i key.pem ubuntu@EC2_IP` |
| App path          | `/var/www/wise-hrm` |
| Document root     | `/var/www/wise-hrm/public` (in Nginx) |
| .env              | `/var/www/wise-hrm/.env` |
| Nginx config      | `/etc/nginx/sites-available/wise-hrm` |
| PHP version       | 8.2 (adjust paths if you use 8.1) |
| Logs              | `storage/logs/laravel.log` on EC2 |
| RDS              | Use RDS endpoint as `DB_HOST` in `.env` |

---

## Security Checklist (AWS)

- Restrict **SSH (22)** to your IP in the EC2 security group.
- Use **HTTPS** (e.g. Certbot) for production.
- `APP_DEBUG=false`, `APP_ENV=production`.
- RDS in private subnet; only EC2 can connect.
- Keep the `.pem` key private; don’t commit `.env` or keys to Git.
