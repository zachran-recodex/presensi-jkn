<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Exception;

class BackupSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create
                            {--database : Backup database only}
                            {--files : Backup files only}
                            {--compress : Compress backup files}
                            {--output= : Custom output directory}
                            {--retention=30 : Days to keep old backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create system backup including database and files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = $this->option('output') ?: storage_path('backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $this->info('🔄 Starting system backup...');
        $this->newLine();

        $backupPaths = [];

        try {
            // Backup database
            if (!$this->option('files')) {
                $databaseBackup = $this->backupDatabase($backupDir, $timestamp);
                if ($databaseBackup) {
                    $backupPaths[] = $databaseBackup;
                    $this->line('✅ Database backup completed');
                }
            }

            // Backup files
            if (!$this->option('database')) {
                $filesBackup = $this->backupFiles($backupDir, $timestamp);
                if ($filesBackup) {
                    $backupPaths[] = $filesBackup;
                    $this->line('✅ Files backup completed');
                }
            }

            // Compress if requested
            if ($this->option('compress') && count($backupPaths) > 0) {
                $compressedBackup = $this->compressBackups($backupPaths, $backupDir, $timestamp);
                if ($compressedBackup) {
                    // Remove individual backup files
                    foreach ($backupPaths as $path) {
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                    $backupPaths = [$compressedBackup];
                    $this->line('✅ Backup compressed');
                }
            }

            // Clean old backups
            $this->cleanOldBackups($backupDir, (int) $this->option('retention'));

            $this->newLine();
            $this->info('🎉 Backup completed successfully!');
            $this->newLine();

            foreach ($backupPaths as $path) {
                $this->line("📦 Backup file: {$path}");
                $this->line("📊 Size: " . $this->formatFileSize(filesize($path)));
            }

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Backup database
     */
    private function backupDatabase(string $backupDir, string $timestamp): ?string
    {
        $this->info('💾 Backing up database...');

        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($config['driver'] !== 'mysql') {
            $this->warn('Database backup only supports MySQL');
            return null;
        }

        $filename = "database_backup_{$timestamp}.sql";
        $backupPath = "{$backupDir}/{$filename}";

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port'] ?? 3306),
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($backupPath)
        );

        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception('Database backup command failed');
        }

        if (!file_exists($backupPath) || filesize($backupPath) === 0) {
            throw new Exception('Database backup file is empty or not created');
        }

        return $backupPath;
    }

    /**
     * Backup important files
     */
    private function backupFiles(string $backupDir, string $timestamp): ?string
    {
        $this->info('📁 Backing up files...');

        $filename = "files_backup_{$timestamp}.tar.gz";
        $backupPath = "{$backupDir}/{$filename}";

        // Directories to backup
        $directoriesToBackup = [
            storage_path('app/public/attendance'),
            storage_path('app/reports'),
            base_path('.env'),
            base_path('app'),
            base_path('config'),
            base_path('database/migrations'),
            base_path('database/seeders'),
            base_path('routes'),
        ];

        // Filter existing paths
        $existingPaths = array_filter($directoriesToBackup, function($path) {
            return file_exists($path);
        });

        if (empty($existingPaths)) {
            $this->warn('No files found to backup');
            return null;
        }

        // Create tar command
        $pathsString = implode(' ', array_map('escapeshellarg', $existingPaths));
        $command = "tar -czf " . escapeshellarg($backupPath) . " -C " . escapeshellarg(base_path()) . " " . $pathsString;

        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception('Files backup command failed');
        }

        if (!file_exists($backupPath)) {
            throw new Exception('Files backup not created');
        }

        return $backupPath;
    }

    /**
     * Compress multiple backup files into one archive
     */
    private function compressBackups(array $backupPaths, string $backupDir, string $timestamp): ?string
    {
        $this->info('🗜️  Compressing backups...');

        if (!class_exists('ZipArchive')) {
            $this->warn('ZipArchive not available, skipping compression');
            return null;
        }

        $zipFilename = "complete_backup_{$timestamp}.zip";
        $zipPath = "{$backupDir}/{$zipFilename}";

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            throw new Exception('Cannot create ZIP file');
        }

        foreach ($backupPaths as $path) {
            if (file_exists($path)) {
                $filename = basename($path);
                $zip->addFile($path, $filename);
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Clean old backup files
     */
    private function cleanOldBackups(string $backupDir, int $retentionDays): void
    {
        $this->info("🧹 Cleaning backups older than {$retentionDays} days...");

        $cutoffTime = time() - ($retentionDays * 24 * 60 * 60);
        $files = glob("{$backupDir}/*");
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->line("🗑️  Deleted {$deletedCount} old backup files");
        }
    }

    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
