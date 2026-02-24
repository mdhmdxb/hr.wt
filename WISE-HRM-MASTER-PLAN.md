# Wise HRM – Master Plan (Phased)

**Developer:** M H Morshed  
**Copyright:** © M H Morshed. Built with Laravel.

This document maps your full product plan into phases and tracks what is implemented vs missed in each phase.

---

## Your Plan Mapped to Phases

| # | Your item | Phase | Notes |
|---|-----------|--------|--------|
| 1️⃣ | Multi-Company / Multi-Branch / Multi-Site; Dashboard filters (Company, Branch, Site, Dept, Project); SaaS-ready | **1** (structure) + **3** (filters) | Sites & filters in Phase 1/3 |
| 2️⃣ | Role (hidden) vs Designation (visible); Dept, reporting manager, employment type, hire date, salary, status; HR-only designation; employee lock + audit | **1** | Core data in 1; designation CRUD & audit in 1 |
| 3️⃣ | Attendance & Overtime (configurable rules, night shift, reminders, no auto-absent) | **2** (basic) + **4** (overtime) | Basic attendance in 2; full rules in 4 |
| 4️⃣ | Leave (hierarchical workflow, letter PDF, QR, calendar, salary impact) | **2** (basic) + **4** (workflow/PDF) | Basic leave in 2; workflow/letter in 4 |
| 5️⃣ | QR verification (leave, then payslip, certificates) | **4** | After leave letter & payslip finalization |
| 6️⃣ | Customizable templates (drag-drop, variables, live preview, white-label) | **4** | Template engine |
| 7️⃣ | Payroll (overtime, premium, multi-currency, revision history, QR) | **2** (basic) + **4** (extras) | Basic in 2; extras in 4 |
| 8️⃣ | Document vault (passport, visa, expiry, version, alerts) | **4** | Documents module |
| 9️⃣ | Smart notifications & compliance alerts | **4** | Notifications |
| 🔟 | Executive dashboard (MD: charts, cost breakdown, trends) | **3** (reports) + **4** (MD dashboard) | Reports in 3; MD view in 4 |
| 1️⃣1️⃣ | Project module (assign employees, site, budget, overtime by project) | **4** | Projects module |
| 1️⃣2️⃣ | Recruitment / ATS (career portal, pipeline, interviews) | **4** | Recruitment module |
| 1️⃣3️⃣ | AI (optional: screening, letters, chatbot, anomaly) | **5** | AI module |
| 1️⃣4️⃣ | Modular architecture (enable/disable, sellable) | **1** (start) + ongoing | Already modular |
| 1️⃣5️⃣ | Security (2FA, audit, encryption, backup) | **1** (audit, installer) + **4** (2FA, backup) | Partial in 1 |
| — | Deployment strategy | Ongoing | Docs exist |

---

## Phase 1 – Core Foundation (Multi-Company, Role & Designation, Structure)

**Goal:** Multi-company / multi-branch (and multi-site) structure, role vs designation, core employee data, dashboard filters, designation management, audit for employee changes.

### Implemented ✅

| Item | Status |
|------|--------|
| Multiple companies | ✅ Companies CRUD (Admin) |
| Multiple branches per company | ✅ Branches CRUD (Admin) |
| Role-based access (Admin, HR, Manager, Accounts, Employee) | ✅ Middleware & policies |
| Role hidden from employees | ✅ Not shown in employee views |
| Designation as job title (visible) | ✅ On employee (designation_id, designation name in UI) |
| Department, reporting manager, employment type, hire date, salary, status | ✅ On Employee model & forms |
| Departments per branch | ✅ departments.branch_id |
| Companies & Branches UI | ✅ Sidebar, index/create/edit/delete |
| Installer, activity logs (login/logout), secure installer | ✅ |
| Settings (company, logo, theme, SMTP, timezone, currency) | ✅ |
| Layout, sidebar, dark mode, fonts | ✅ |
| About / Developer & Copyright | ✅ |

### Missed in Phase 1 (to add)

| Item | Action |
|------|--------|
| **Multiple sites per branch** | Add `sites` table (branch_id), Sites CRUD (Admin), link employees/attendance to site later |
| **Dashboard filter by Company / Branch / Site / Department** | Add dropdowns on dashboard; filter stats (employees, attendance, leave, payroll) by selection |
| **Dashboard filter by Project** | Add when Project module exists (Phase 4) |
| **Designation CRUD (only HR/Admin)** | Add Designation management UI; only Admin/HR can create/edit/delete designations |
| **Audit trail for employee changes** | Log employee create/update/delete in activity_logs (who, when, what changed) |
| **Employee “submit then lock”** | Optional: employee self-service form to submit personal details once, then only HR/Admin can edit; can be Phase 4 |

---

## Phase 2 – Attendance, Leave, Payroll (Basic)

**Goal:** Basic attendance (check-in/out), leave (types, request, approve/reject), payroll (run, payslips, edit in draft).

### Implemented ✅

| Item | Status |
|------|--------|
| Attendance: check-in, check-out, date, status, notes | ✅ Attendances CRUD |
| Leave types (days, carry-over, paid) | ✅ LeaveType CRUD |
| Leave request (submit, list, show, approve/reject by HR) | ✅ LeaveRequest |
| Payroll run (period, generate payslips, finalize) | ✅ PayrollRun |
| Payslip (basic, allowances, deductions, net) | ✅ View & edit in draft |
| Reports: Attendance, Leave, Payroll | ✅ Phase 3 reports |

### Missed in Phase 2 (defer to Phase 4 or later)

| Item | Where |
|------|--------|
| Attendance: attachment upload | Phase 4 |
| Overtime rules (rounding, premium, night shift, site-based) | Phase 4 |
| No auto-absent; reminder / escalation notifications | Phase 4 |
| Leave: hierarchical workflow (Supervisor → HR → Accounts → Management) | Phase 4 |
| Leave: official letter PDF, QR, seal, email | Phase 4 |
| Leave: calendar view, salary impact preview | Phase 4 |
| Payroll: overtime, premium, multi-currency, salary revision history, payslip QR | Phase 4 |

---

## Phase 3 – Reports & Executive View

**Goal:** Reports for attendance, leave, payroll; filters; foundation for executive dashboard.

### Implemented ✅

| Item | Status |
|------|--------|
| Reports index (by role) | ✅ |
| Attendance report (employee, date range, status) | ✅ |
| Leave report (employee, status, dates) | ✅ |
| Payroll report (year/month, run summary) | ✅ |
| Dashboard stats (employees, attendance today, pending leave, payroll this month) | ✅ |

### Missed in Phase 3 (to add in Phase 3 or 4)

| Item | Action |
|------|--------|
| **Dashboard filters** (Company, Branch, Site, Department) | Add in Phase 1 completion (above) |
| **Executive (MD) dashboard** (charts, department cost, trends, expiry alerts) | Phase 4 – separate MD view or dashboard role filter |

---

## Phase 4 – Full Feature Set (Planned)

- **1️⃣** Complete dashboard filters (include Project when module exists).
- **3️⃣** Attendance: attachments, overtime engine, configurable rules, night shift, reminders (no auto-absent).
- **4️⃣** Leave: hierarchical approval, leave letter PDF, QR, calendar, salary impact.
- **5️⃣** QR verification for leave letters (then payslips, certificates).
- **6️⃣** Customizable document templates (variables, preview, white-label).
- **7️⃣** Payroll: overtime, premium, multi-currency, salary revision history, payslip QR.
- **8️⃣** Document vault (passport, visa, expiry, version, alerts).
- **9️⃣** Notifications & compliance alerts (expiry, probation, approvals).
- **🔟** Executive (MD) dashboard with charts and cost breakdown.
- **1️⃣1️⃣** Project module (sites, assign employees, budget, overtime by project).
- **1️⃣2️⃣** Recruitment / ATS (career portal, pipeline, interviews).
- **1️⃣5️⃣** 2FA, backup, encryption where needed.

---

## Phase 5 – AI & Optional

- **1️⃣3️⃣** AI module (optional, toggle, Ollama/API): screening, letters, chatbot, anomaly detection.

---

## Summary: What to Add Before Phase 4

1. **Sites** – Table + CRUD (Admin), linked to branch; optional link to employees/attendance later.
2. **Designation CRUD** – Manage designations (Admin/HR only); list, create, edit, delete.
3. **Dashboard filters** – Company, Branch, Site, Department dropdowns; filter dashboard stats.
4. **Audit for employee changes** – Log create/update/delete of employees in `activity_logs` with details.

These complete the Phase 1–3 scope per your plan so Phase 4 can build on a solid base. Phase 1–3 gaps (Sites, Designations, Dashboard filters, Employee audit) are now implemented.

---

## Implemented in this pass (Phase 1–3 gaps)

- **Sites:** Migration `2024_01_01_000016_create_sites_table` (sites table + `employees.site_id`). Site model, SiteController, CRUD views. Sidebar “Sites” (Admin). Employee create/edit/show include Site.
- **Designations:** DesignationController, CRUD views. Sidebar “Designations” (Admin/HR). Only HR/Admin can add/edit/delete job titles.
- **Dashboard filters:** Company, Branch, Site, Department dropdowns (Admin/HR). Stats scoped by selection. “Clear” link when a filter is active.
- **Audit:** ActivityLog::log() on employee create, update, delete (action + description with name and ID).

Run `php artisan migrate` after pull to apply the sites migration.

---

*Last updated: aligned with your 15-point plan. Phases 1–3 implemented; gaps listed and “add before Phase 4” items defined.*
