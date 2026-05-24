# BatteryPro Management System

**BatteryPro Management System** is a professional, open‑source battery shop ERP built with **Core PHP 8+**, **MySQL/SQLite**, and **Bootstrap 5**. It provides a responsive web admin panel and a RESTful API for mobile clients.

---

## Features (at a glance)
- Secure login with password hashing and session management.
- Role‑based access (Admin / Staff).
- Dashboard with stock, sales, profit, expenses, and recent activity widgets.
- Supplier, Battery inventory, Customer, Sales, Expense management modules.
- PDF/Excel report generation (via **mPDF** and **PhpSpreadsheet**).
- Automatic daily backup (MySQL dump) with optional Google Drive sync.
- Light/Dark theme toggle.
- REST API (JWT authentication) for mobile apps – login, dashboard, CRUD.
- Offline‑first support via SQLite (configurable in `config/database.php`).

---

## Quick Start

### Prerequisites
- PHP 8.0+ (with PDO, OpenSSL, GD, mbstring extensions)
- MySQL server (or SQLite for offline mode)
- Composer (for third‑party libraries)
- XAMPP/Apache with rewrite module enabled

### 1. Clone / copy the repository
```bash
# Assuming you placed the code under XAMPP htdocs
cd "C:\xampp\htdocs"
# The folder name is "battery pro"
```

### 2. Install PHP dependencies
```bash
cd "battery pro"
composer install
```
This will install:
- `firebase/php-jwt` – JWT handling for the API.
- `mpdf/mpdf` – PDF generation for invoices & reports.
- `phpoffice/phpspreadsheet` – Excel export.

### 3. Configure the database
Edit `config/database.php` if you need custom MySQL credentials (default: `root`/``). The SQLite path can be set via `connectSQLite()` if you prefer offline mode.

### 4. Run the installer
Open a browser and navigate to:
```
http://localhost/battery%20pro/install.php
```
The script will create all tables defined in `database/schema.sql` and insert a default admin user:
- **Username:** `admin`
- **Password:** `admin123` (you should change it after first login).

### 5. Access the application
- Web admin panel: `http://localhost/battery%20pro/`
- Login page: `http://localhost/battery%20pro/login`

### 6. API usage (example)
```bash
# Login – obtain JWT token
curl -X POST -H "Content-Type: application/json" -d '{"username":"admin","password":"admin123"}' http://localhost/battery%20pro/api/login.php

# Use the token to fetch the dashboard summary
curl -H "Authorization: Bearer <your_jwt_token>" http://localhost/battery%20pro/api/dashboard.php
```
All other CRUD endpoints follow the pattern `/api/<resource>.php` and expect a valid JWT token.

---

## Folder Structure
```
/battery pro/
├─ /api/            # REST API endpoints (login, dashboard, etc.)
├─ /assets/         # CSS, JS, images (Bootstrap via CDN, custom style.css)
├─ /config/         # app and database configuration, autoloader
├─ /controllers/    # MVC controllers (BaseController, AuthController, DashboardController…)
├─ /helpers/        # utility classes (Router, auth_helper)
├─ /models/         # PDO models (BaseModel, User, Supplier, Battery, …)
├─ /routes/         # web.php – route definitions
├─ /views/          # Blade‑like PHP view files (layout, auth, dashboard…)
├─ /uploads/        # Uploaded images (supplier photo, battery image)
├─ /database/       # schema.sql and future backup files
├─ composer.json    # dependencies
├─ index.php        # Front‑controller entry point
└─ .htaccess        # URL rewriting to index.php
```

---

## Extending the System
1. **Add new modules** – create a Model, Controller, and view files, then register routes in `routes/web.php` or `api/`.
2. **Secure API** – all API files start with JWT verification (`verify_jwt`). Use the same pattern for other resources.
3. **Backup system** – the `backups` table stores metadata. Implement a cron job (Windows Task Scheduler) that calls a script similar to `install.php` but runs `mysqldump`, encrypts the file, and uploads to Google Drive via the Drive API (store OAuth token in `settings.google_drive_token`).
4. **Biometric login for mobile** – the mobile app should call the `/api/login.php` endpoint after the device has verified the fingerprint using OS‑level APIs; the server does not need to know the biometric data.

---

## Security Notes
- Passwords are hashed with `password_hash` using a configurable cost (`app.php > password_hash_cost`).
- Sessions are regenerated on login to prevent fixation.
- All database queries use prepared statements via PDO to avoid SQL injection.
- CSRF protection can be added by generating a token in forms (`$_SESSION['csrf_token']`).
- HTTPS is strongly recommended for production.

---

## License
MIT – feel free to customize and use in commercial projects.
