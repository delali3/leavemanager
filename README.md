# Leave Request Management System

A production-ready leave management system built with **Laravel 12**, **Spatie Laravel Permission**, **MySQL**, and **TailwindCSS**.

---

## Table of Contents

1. [Features](#features)
2. [System Requirements](#system-requirements)
3. [Installation (Local / XAMPP)](#installation-local--xampp)
4. [Deployment on Shared Hosting (cPanel)](#deployment-on-shared-hosting-cpanel)
5. [Queue Setup](#queue-setup)
6. [Cron Job Setup](#cron-job-setup)
7. [Demo Accounts](#demo-accounts)
8. [Architecture Overview](#architecture-overview)
9. [Business Rules](#business-rules)

---

## Features

- **Role-based access**: Admin, HR, Manager, Employee (Spatie Permission)
- **Leave Types** CRUD with soft delete & restore (Admin / HR)
- **Leave Requests** with weekend exclusion, overlap detection, balance checks
- **Auto balance deduction** on approval, restore on rejection
- **Email notifications** via database queue (submission to Manager, decision to Employee)
- **Role-aware dashboards** with stats, charts, and approval panels
- **Reports** filterable by year, month, and status
- **Policies** protecting every action with authorization
- **File attachment** support (PDF/JPG/PNG, max 2MB)
- **Database queue** — shared-hosting compatible (no Redis required)
- **Email verification** via Laravel Breeze

---

## System Requirements

| Requirement | Minimum |
|-------------|---------|
| PHP | 8.2+ |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js | 18+ |

---

## Installation (Local / XAMPP)

### 1. Place the Project

```
C:\xampp\htdocs\GhProfit\laravel_request\
```

### 2. Install Dependencies

```bash
composer install
npm install
npm run build
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Leave Manager"
APP_URL=http://localhost/laravel_request/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leave_manager
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

# Email — Mailtrap for testing:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_FROM_ADDRESS="noreply@company.com"
```

### 4. Create Database

```sql
CREATE DATABASE leave_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 6. Access

Open: **http://localhost/laravel_request/public**

---

## Deployment on Shared Hosting (cPanel)

### Step 1 — Upload Files

Upload everything **except** `node_modules/` to your server. Build assets locally first:

```bash
npm run build
```

Then upload the `public/build/` folder along with all project files.

### Step 2 — Point Document Root

In **cPanel → Domains**, set Document Root to:

```
/home/youraccount/laravel_request/public
```

Or create a subdomain pointing to that path.

### Step 3 — Create .env on Server

```env
APP_NAME="Leave Manager"
APP_ENV=production
APP_KEY=                          # Generate in step 5
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youraccount_leave_manager
DB_USERNAME=youraccount_dbuser
DB_PASSWORD=your_db_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Leave Manager"
```

### Step 4 — Install Composer (SSH)

```bash
cd /home/youraccount/laravel_request
composer install --no-dev --optimize-autoloader
```

If SSH is unavailable, install locally and upload `vendor/`.

### Step 5 — Run Setup Commands (SSH)

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6 — Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

---

## Queue Setup

The system sends emails **asynchronously** via the database queue driver — no Redis required.

### Development

```bash
php artisan queue:work --queue=notifications,default --sleep=3 --tries=3
```

### Production (Shared Hosting — Cron-based)

Add this cron in **cPanel → Cron Jobs** (runs every minute):

```bash
* * * * * cd /home/youraccount/laravel_request && php artisan queue:work --stop-when-empty --queue=notifications,default --tries=3 >> /dev/null 2>&1
```

`--stop-when-empty` makes the worker exit after processing all pending jobs — safe for cron.

### Failed Jobs

```bash
php artisan queue:failed          # List failed jobs
php artisan queue:retry all       # Retry all
php artisan queue:flush           # Clear failed jobs table
```

---

## Cron Job Setup

Add in **cPanel → Cron Jobs**:

| Description | Command | Schedule |
|-------------|---------|----------|
| Laravel Scheduler | `cd /home/.../laravel_request && php artisan schedule:run >> /dev/null 2>&1` | `* * * * *` |
| Queue Worker | `cd /home/.../laravel_request && php artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1` | `* * * * *` |

---

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@company.com | Admin@123 |
| **HR** | hr@company.com | HR@1234! |
| **Manager** | manager@company.com | Manager@123 |
| **Employee** | employee@company.com | Employee@123 |
| **Employee** | jane@company.com | Jane@1234! |

> Change all passwords before going to production!

---

## Architecture Overview

```
app/
├── Http/Controllers/
│   ├── DashboardController.php      — Role-aware dashboard routing
│   ├── LeaveTypeController.php      — CRUD + soft delete/restore
│   ├── LeaveRequestController.php   — Submit, approve, reject
│   ├── UserController.php           — User management + balance init
│   └── ReportController.php         — Filterable reports
│
├── Http/Requests/                   — Form Request validation (thin controllers)
│
├── Models/
│   ├── User.php                     — HasRoles + relationships
│   ├── LeaveType.php                — SoftDeletes
│   ├── LeaveBalance.php             — Remaining day tracking
│   └── LeaveRequest.php             — Working days calc + scopes
│
├── Policies/                        — Authorization rules per model
│
├── Services/
│   ├── LeaveRequestService.php      — Core business logic
│   └── LeaveBalanceService.php      — Balance CRUD & carry-forward
│
├── Events/ + Listeners/             — Event-driven approval notifications
└── Notifications/                   — Queued email notifications

database/
├── migrations/                      — Indexed table definitions
└── seeders/
    ├── RolePermissionSeeder.php     — 4 roles + 15 permissions
    ├── LeaveTypeSeeder.php          — 6 default leave types
    └── UserSeeder.php               — 5 demo users with balances
```

---

## Business Rules

| Rule | Location |
|------|----------|
| Weekend exclusion | `LeaveRequest::calculateWorkingDays()` |
| Overlap prevention | `LeaveRequestService::hasOverlap()` |
| Balance check before submission | `LeaveBalanceService::hasEnoughBalance()` |
| Auto balance deduction on approval | `LeaveBalanceService::deduct()` |
| Balance restore on rejection (not applied — leaves unchanged) | Design decision |
| Attachment required per leave type | `StoreLeaveRequestRequest` dynamic rule |
| File validation (PDF/JPG/PNG ≤ 2MB) | Form Request rules |
| Self-approval prevention | `LeaveRequestPolicy::approve()` |
| Admin bypasses all policies | `Gate::before()` in `AppServiceProvider` |

---

## Disabling Public Registration

Since this is a single-company system, only Admin/HR should create accounts. Disable the Breeze registration route in `routes/auth.php`:

```php
// Comment out to disable public self-registration:
// Route::get('register', [RegisteredUserController::class, 'create']);
// Route::post('register', [RegisteredUserController::class, 'store']);
```

---

## Troubleshooting

**500 error:**
```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear
chmod -R 775 storage bootstrap/cache
```

**Emails not sending:**
```bash
php artisan queue:work --once       # Process one job manually
php artisan queue:failed            # Check failed jobs
```

**Storage files inaccessible:**
```bash
php artisan storage:link
```
