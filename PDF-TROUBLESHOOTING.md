# PDF download (leave letter) – troubleshooting

If the leave letter PDF does not download or you see a blank/error page, run these from the project root.

## 1. Create storage link (required for images in PDF)

```bash
php artisan storage:link
```

If the link already exists you may see “The [...] link already exists.” That is fine.

## 2. Clear caches

```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

## 3. Ensure DomPDF is installed

```bash
composer require barryvdh/laravel-dompdf
```

## 4. Regenerate config (after changing .env)

```bash
php artisan config:cache
```

## 5. Test PDF generation from CLI (optional)

From project root:

```bash
php artisan tinker
```

Then in tinker (replace `1` with a real leave request ID that is approved or cancelled):

```php
$id = 1;
$lr = \Modules\Leave\Models\LeaveRequest::find($id);
if ($lr && in_array($lr->status, ['approved', 'cancelled'])) {
    app()->setRequest(request());
    $ctrl = app(\Modules\Leave\Http\Controllers\LeaveRequestController::class);
    $resp = $ctrl->downloadLetter($lr);
    echo "PDF response type: " . get_class($resp) . "\n";
} else {
    echo "Leave request not found or not approved/cancelled.\n";
}
exit;
```

If that runs without error, the problem may be browser/session (e.g. redirect to login). If you see an exception, fix the reported error (e.g. missing view, missing font, or file path).

## 6. Common causes

- **Storage link missing**: Run `php artisan storage:link` so logo/signature/stamp paths resolve.
- **Wrong APP_URL**: In `.env`, `APP_URL` must match the site URL (e.g. `https://yoursite.com`). Avoid trailing slash.
- **PHP memory**: For large documents, increase `memory_limit` in `php.ini` or when calling the PDF route.
- **File permissions**: `storage/app/public` and `public/storage` must be writable/readable by the web server.
