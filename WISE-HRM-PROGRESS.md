# Wise HRM – Phase 1: Core Foundation – Progress Tracker

**Purpose:** Single source of truth for Phase 1 scope, decisions, and progress. Use this file to resume work if the chat/session is lost.

---

## Project Overview

- **System:** Wise HRM – Modular HR Management System for Wise Technology  
- **Phase:** 1 – Core Foundation  
- **Architecture:** Modular Monolith (single-tenant now, multi-tenant ready)  
- **Stack:** Laravel 10+, MySQL, TailwindCSS, Blade (no React)

---

## Goals (Phase 1)

1. Clean, scalable modular architecture under `Modules/`
2. Foundation DB: companies, branches, departments, designations, employees, users
3. Role-based auth (admin/hr/manager/accounts/employee), role hidden from employees
4. UI: admin dashboard layout, sidebar, navbar, company logo, dark/light toggle
5. Settings: company, logo, primary/secondary color, SMTP, timezone, date format, currency
6. Installer: `/install` wizard → DB → Admin → Company → Finalize → Lock
7. Security: protect .env, lock installer, activity logs (login/logout)

---

## Module Structure (Target)

```
Modules/
  Core/         → Shared layout, auth, installer, activity
  Employee/     → Employees CRUD, profile (role hidden)
  Attendance/   → (Phase 2)
  Leave/        → (Phase 2)
  Payroll/      → (Phase 2)
  Settings/     → Company, theme, SMTP, timezone, date, currency
```

Each module: Models, Controllers, Routes, Policies, Requests, Views, Config (if needed).

---

## Database Schema (Foundation)

| Table         | Key columns |
|---------------|-------------|
| companies     | id, name, logo, address, phone, email |
| branches      | id, company_id, name, address |
| departments   | id, branch_id, name |
| designations  | id, name, level |
| employees     | id, employee_code, first_name, last_name, email, phone, branch_id, department_id, designation_id, reporting_manager_id, hire_date, employment_type, basic_salary, status |
| users         | id, employee_id (nullable), name, email, password, role, two_factor_enabled |
| activity_logs | id, user_id, action, description, ip_address, user_agent, created_at |

---

## Progress Log

| # | Task | Status | Notes |
|---|------|--------|--------|
| 1 | Progress file (this) | ✅ Done | WISE-HRM-PROGRESS.md |
| 2 | Laravel project init | ✅ Done | Manual structure + composer.json |
| 3 | Modular folders | ✅ Done | Core, Employee, Settings (+ Attendance, Leave, Payroll placeholders) |
| 4 | Migrations | ✅ Done | companies, branches, departments, designations, employees, users, password_reset_tokens, sessions, activity_logs, settings |
| 5 | Models | ✅ Done | All with relationships; User in Core |
| 6 | Middleware (roles) | ✅ Done | role.admin, role.hr, role.manager, role.accounts, role.employee, installed, not.installed |
| 7 | Auth + policies | ✅ Done | Login/logout; role not shown in employee profile views |
| 8 | UI layout + sidebar + navbar | ✅ Done | app.blade.php, sidebar partial, dark/light toggle (Alpine + cookie) |
| 9 | Settings module | ✅ Done | Company, logo, primary/secondary color, SMTP, timezone, date format, currency |
| 10 | Installer wizard | ✅ Done | /install → database, admin, company, finalize → lock file |
| 11 | Activity logs | ✅ Done | activity_logs table; login/logout logged in LoginController |
| 12 | Logo display fix | ✅ Done | Company logo served via `/app/logo` (works without symlink); layout uses route |
| 13 | Appearance / theme | ✅ Done | Color pickers, font selector, link/button/sidebar-active/border-radius; CSS variables in layout; wise-btn, wise-link, wise-sidebar-link classes |
| 14 | Companies & Branches | ✅ Done | Admin: Companies CRUD, Branches CRUD; sidebar Companies, Branches; multi-company multi-branch |
| 15 | Phase 2 scaffolding | ✅ Done | Attendance, Leave, Payroll modules: Provider, Routes, Controller, index view; sidebar links (Attendance/Leave: hr; Payroll: accounts) |
| 16 | Font selector fix | ✅ Done | Font applied app-wide via CSS var; preview in Settings; more fonts (Nunito, Poppins, Merriweather, Playfair Display, DM Sans, Work Sans); allowedFontValue whitelist |
| 17 | Phase 2 – Attendance | ✅ Done | attendances table; Attendance model; CRUD (index with filter, create, edit, delete); check-in/out, status, notes |
| 18 | Phase 2 – Leave | ✅ Done | leave_types, leave_requests tables; LeaveType CRUD; LeaveRequest submit, list, show, approve/reject (HR) |
| 19 | Phase 2 – Payroll | ✅ Done | payroll_runs, payslips tables; create run (period), generate payslips from active employees; finalize run; view payslip |
| 20 | Phase 2 polish | ✅ Done | Edit payslip (basic, allowances, deductions) when run is draft; net recalculated on save |
| 21 | Phase 3 – Reports | ✅ Done | Reports index; Attendance report (filter employee, date, status); Leave report (filter employee, status, dates); Payroll report (filter year/month, run summary) |
| 22 | Phase 1 gaps (Master Plan) | ✅ Done | Sites table + CRUD (Admin); Designation CRUD (Admin/HR); Dashboard filters (Company, Branch, Site, Department); Employee site_id; Audit log for employee create/update/delete |
| 23 | Favicon & versioning | ✅ Done | Favicon option in Settings (Company & General); app.favicon route (custom upload or wt-logo.png or company logo); APP_VERSION in config and .env.example; About page shows version and wt-logo.png (96–112px) |
| 24 | Dashboard filter dependency | ✅ Done | Company → Branch → Site/Department: changing company clears branch/site/department; changing branch clears site/department; server-side validation of filter consistency |
| 25 | Font fix | ✅ Done | Layout and Settings use font keys only; theme font/heading_font from DB passed through allowedFontValue(); Settings preview and app-wide fonts persist after save |
| 26 | Sidebar order | ✅ Done | Settings moved to second last, just before About |
| 27 | Phase 4 – Executive dashboard | ✅ Done | Route /dashboard/executive (Admin); KPI cards (employees, attendance rate, payroll this month, pending leave); department cost breakdown (bars); leave trend (last 6 months); WOW layout with gradient hero and hover cards |

---

## Master plan

See **WISE-HRM-MASTER-PLAN.md** for the full product plan mapped to phases and what is implemented vs deferred.

---

## Phase 1 completion – Sites & Designations (migration 000016)

| Table  | Key columns |
|--------|--------------|
| sites  | id, branch_id, name, address |
| employees | + site_id (nullable, FK to sites) |

- **Sites:** Admin CRUD under Core; sidebar “Sites”. Branch cannot be deleted if it has sites.
- **Designations:** Admin/HR CRUD under Core; sidebar “Designations”. Job titles (e.g. IT Manager, HR Officer).
- **Employee:** create/edit include optional Site; show displays Site.
- **Dashboard:** Filter by Company, Branch, Site, Department (Admin/HR); stats scoped by selection.
- **Audit:** Activity log entries for employee_created, employee_updated, employee_deleted.

After pull: run `php artisan migrate` to create `sites` and add `employees.site_id`.

---

## Phase 2 schema (migrations 000011–000015)

| Table          | Key columns |
|----------------|-------------|
| attendances    | id, employee_id, date, check_in_at, check_out_at, status, notes |
| leave_types    | id, name, days_per_year, carry_over, color, is_paid |
| leave_requests | id, employee_id, leave_type_id, start_date, end_date, days, status, reason, approved_by, approved_at, rejection_reason |
| payroll_runs   | id, period_start, period_end, status, paid_at, notes |
| payslips       | id, payroll_run_id, employee_id, basic_salary, allowances, deductions, net_pay, notes |

After pulling Phase 2: run `php artisan migrate` to create these tables.

---

## Phase 3 – Reports

- **Reports** (sidebar): Admin, HR, and Accounts see “Reports”. Index lists available reports by role.
- **Attendance report** (Admin, HR): Filter by employee, date range, status; paginated table.
- **Leave report** (Admin, HR): Filter by employee, status, date range; paginated table.
- **Payroll report** (Admin, Accounts): Filter by year/month; list runs; select a run to see summary (total basic, allowances, deductions, net pay).

---

## How to Resume

1. Open this file: `WISE-HRM-PROGRESS.md`
2. Check the Progress Log table for next ⏳ Pending item
3. Follow folder structure and schema above
4. Ensure installer is locked after first run; role never shown to employees

---

**Local setup:** Project is intended to run from **WAMP's htdocs** (e.g. `C:\wamp64\www\Wise-HRM`). Use **http://localhost/Wise-HRM/public** and **http://localhost/Wise-HRM/public/install** — see SETUP-GUIDE-WAMP-XAMPP.md.

*Last updated: Phase 2 complete (incl. edit payslip in draft). Phase 3 complete (Reports: Attendance, Leave, Payroll). Run `php artisan migrate` after pull for Phase 2 tables.*
