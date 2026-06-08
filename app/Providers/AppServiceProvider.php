<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    protected static $isSettingUp = false;

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if (self::$isSettingUp) {
            return;
        }

        // Avoid running database checks during migrations, rollback, or seeding commands
        if (app()->runningInConsole()) {
            $argv = $_SERVER['argv'] ?? [];
            foreach ($argv as $arg) {
                if (in_array($arg, ['migrate', 'db:seed', 'migrate:fresh', 'migrate:rollback', 'migrate:reset', 'migrate:status'])) {
                    return;
                }
            }
        }

        self::$isSettingUp = true;

        try {
            $dbName = config('database.connections.mysql.database');
            
            // Try to check if our key table 'usuarios' exists
            $hasUsuarios = false;
            try {
                $hasUsuarios = Schema::connection('mysql')->hasTable('usuarios');
            } catch (\Throwable $e) {
                // Connection failed (e.g. database does not exist) or check failed
                $hasUsuarios = false;
            }

            if (!$hasUsuarios) {
                $this->setupDatabase($dbName);
            }
        } catch (\Throwable $e) {
            Log::error("Error checking database in AppServiceProvider: " . $e->getMessage());
        } finally {
            self::$isSettingUp = false;
        }
    }

    protected function setupDatabase(string $dbName): void
    {
        try {
            // Get default mysql configuration
            $config = config('database.connections.mysql');
            
            // Clone configuration without database name to connect to the server
            $tempConfig = $config;
            $tempConfig['database'] = null;
            config(['database.connections.temp_mysql' => $tempConfig]);
            
            // Create the database
            DB::connection('temp_mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            
            // Purge connections to ensure the new database configuration is reloaded
            DB::purge('temp_mysql');
            DB::purge('mysql');
            
            // Run migrations and seeders programmatically
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            
            Log::info("Database '{$dbName}' has been successfully created, migrated, and seeded.");
        } catch (\Throwable $e) {
            Log::error("Failed to auto-setup database: " . $e->getMessage());
        }
    }
}
