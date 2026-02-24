<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\ActivityLog;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Company;
use Modules\Core\Models\Department;
use Modules\Core\Models\Designation;
use Modules\Core\Models\User;

class InstallController extends Controller
{
    public function welcome()
    {
        return view('core::install.welcome');
    }

    public function database()
    {
        return view('core::install.database');
    }

    public function storeDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|string',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        $envPath = base_path('.env');
        if (! File::exists($envPath)) {
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envPath);
            } else {
                return back()->with('error', '.env.example not found.');
            }
        }

        $env = File::get($envPath);
        $replace = [
            'DB_HOST' => $request->db_host,
            'DB_PORT' => $request->db_port,
            'DB_DATABASE' => $request->db_database,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password ?? '',
        ];

        foreach ($replace as $key => $value) {
            $env = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$value}\"",
                $env
            );
            if (! preg_match("/^{$key}=/m", $env)) {
                $env .= "\n{$key}=\"{$value}\"\n";
            }
        }

        File::put($envPath, $env);

        // Connect to MySQL without a database and create the database if it doesn't exist (like WordPress/CMS installers)
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                $request->db_host,
                $request->db_port
            );
            $pdo = new \PDO(
                $dsn,
                $request->db_username,
                $request->db_password ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $dbName = $request->db_database;
            $safeName = '`' . str_replace(['`', "\0"], ['``', ''], $dbName) . '`';
            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$safeName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not connect or create database: ' . $e->getMessage());
        }

        // Now test Laravel's connection using the database we just created
        config(['database.connections.mysql.host' => $request->db_host]);
        config(['database.connections.mysql.port' => $request->db_port]);
        config(['database.connections.mysql.database' => $request->db_database]);
        config(['database.connections.mysql.username' => $request->db_username]);
        config(['database.connections.mysql.password' => $request->db_password]);

        try {
            DB::purge('mysql');
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not connect to database: ' . $e->getMessage());
        }

        return redirect()->route('install.admin');
    }

    public function admin()
    {
        return view('core::install.admin');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        session([
            'install.admin' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ],
        ]);

        return redirect()->route('install.company');
    }

    public function company()
    {
        return view('core::install.company');
    }

    public function storeCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        session([
            'install.company' => [
                'name' => $request->company_name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ],
        ]);

        return redirect()->route('install.finalize');
    }

    public function finalize()
    {
        return view('core::install.finalize');
    }

    public function complete(Request $request)
    {
        $admin = session('install.admin');
        $companyData = session('install.company');

        if (! $admin || ! $companyData) {
            return redirect()->route('install.welcome')->with('error', 'Session expired. Please start over.');
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }

        $company = Company::create([
            'name' => $companyData['name'],
            'address' => $companyData['address'],
            'phone' => $companyData['phone'],
            'email' => $companyData['email'],
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'name' => 'Head Office',
            'address' => $companyData['address'],
        ]);

        $department = Department::create([
            'branch_id' => $branch->id,
            'name' => 'General',
        ]);

        Designation::create(['name' => 'Staff', 'level' => 1]);

        $user = User::create([
            'name' => $admin['name'],
            'email' => $admin['email'],
            'password' => $admin['password'],
            'role' => User::ROLE_ADMIN,
        ]);

        File::put(storage_path('wise_hrm_installed.lock'), date('c'));

        session()->forget(['install.admin', 'install.company']);

        ActivityLog::log('installation_complete', 'Wise HRM installed', $user->id);

        return redirect()->route('login')->with('success', 'Installation complete. You can log in now.');
    }
}
