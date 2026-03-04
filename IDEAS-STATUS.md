# Ideas & findings – status

Quick reference of what’s done vs still missing from your list.

---

## Done

| Item | Where / notes |
|------|----------------|
| **Public holidays** (with date range) | Time → Public Holidays; start + optional end date |
| **Payslip: HR controls what’s shown** | Settings → Payslip display (checkboxes) |
| **Login: company logo + developer credit** | Login page (logo top, “Developed by M H Morshed” bottom) |
| **Leave cancellation** (who, when, why; CANCELLED on letter; return days) | Leave request: Cancel button; letter shows CANCELLED; verify page for cancelled |
| **Batch attendance: off-days/holidays** (colors, no time when off; time when status = Present) | Batch: amber/slate/blue rows; time inputs only when status is not off-day |
| **Payroll: hours worked on off days** | Payslip + edit: “Hours worked on off days” and details |
| **Employee attendance (batch + submit + lock)** | My Attendance: Save / Submit month; lock; HR can “Allow employee to edit” |
| **Hide Check-in/Check-out for employees** | Dashboard: hidden unless Owner enables in Owner Portal |
| **Owner: feature option (show Check-in/Check-out)** | Owner Portal → Feature options |
| **Owner: modules grouped** | Owner Portal: People, Time, Finance, Organization, Assets & reports |
| **Signatures for management/admins** | Settings → “My signature” (Admin/HR upload; stored per user) |
| **Company stamp (HR upload)** | Settings → “Company stamp” (Admin/HR upload; stored in settings) |
| **HR can access Settings** | Sidebar: Settings for Admin and HR; HR can upload stamp and their signature |
| **Letters: stamp & signature control** | Settings → “Letters & documents”: choose on which documents to show company stamp and signatory signature (leave letter, payslip) |
| **Letters: electronic footer** | All generated letters/payslips show configurable footer: “This document was generated electronically by Wise HRM…” (Settings → Letter footer text) |
| **Signatures/stamp on leave letter** | Leave PDF: approval chain with signatory signature image per approver; company stamp bottom-right; footer at bottom |
| **Payslip: footer and stamp** | Payslip view: same footer text; optional company stamp when enabled for “Payslip” in Letters & documents |

---

## Partially done / to extend

| Item | Status |
|------|--------|
| **Email settings** | Basic SMTP in Settings → Email. IMAP/Google/Microsoft and full templates not done. |
| **Leave approval hierarchy** | Leave has workflow (steps); “configurable who approves” (e.g. Manager → HR → MD) may need UI to assign roles per step. |
| **Approval letter: applied date, each approver date, final status** | Letter exists; “on which date who approved” can be added to the letter content from workflow data. |
| **Notes on leave / letters** | Notes exist in some flows; optional “notes” on letter can be added. |

---

## Not done (from your list)

| Item | Notes |
|------|--------|
| **Email templates per confirmation type** | No UI yet to edit templates per type (leave approved, leave rejected, etc.). |
| **Leave: remaining leaves at employee creation** | Field “leaves remaining” when creating employee not added. |
| **Leave portal: manual previous leave history** | No screen to enter past leave history for an employee. |
| **Approved days deducted from allowed leaves** | No automatic deduction from balance on approval (or return on cancel). |
| **UAE leave rules** (probation 6 mo, 2 days/month, 30 after 12 mo) | No MOHRE-style accrual logic implemented. |

---

## Where things are

- **Signatures:** Settings → “My signature” (Admin/HR; stored in `users.signature_path`).
- **Company stamp:** Settings → “Company stamp” (Admin/HR; stored in settings as `company_stamp_path`).
- **Using them:** Leave letter PDF and payslip view use stamp/signature and footer per Settings → Letters & documents.

**Prepared for pending (migrations only; no UI yet):**

- `employees.remaining_leave` – for “leaves remaining” at employee create/edit and balance tracking.
- `leave_history` table – for manual previous leave history (employee_id, leave_type_id, start_date, end_date, days, notes). Run migrations to add these; then you can add UI for “remaining leaves” on employee form and a Leave portal screen for manual history.

Run after pull:

```bash
php artisan migrate
php artisan storage:link
```

So that signature and stamp images under `storage/app/public/` are reachable at `/storage/...`.
