<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function download()
    {
        // Check permissions (optional, but recommended)
        // if (!auth()->user()->can('users.manage')) {
        //     abort(403);
        // }

        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        $filePath = storage_path("app/" . $filename);

        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        
        // Escape characters to prevent issues
        $password = escapeshellarg($password);

        // NOTE: for security, putting password in command line is not ideal in shared env, 
        // but often acceptable for single-tenant VPS scripts.
        // We use --no-tablespaces to avoid permission issues on some setups.
        // The command varies slightly depending on OS, but for standard Linux/Ubuntu:
        // We construct the command carefully.
        
        // If password is set
        $passwordPart = !empty(config('database.connections.mysql.password')) ? " -p" . config('database.connections.mysql.password') : "";

        // Secure way using command execution
        // We will try to execute it.
        // Note: mysqldump usually requires full path or being in path.
        
        // Constructing the command
        // Using --column-statistics=0 is often needed for compatibility with newer mysqldump versions v8+
        $command = "mysqldump --user={$username} --password=" . config('database.connections.mysql.password') . " --host={$host} --port={$port} {$database} > {$filePath}";

        try {
            $output = null;
            $resultCode = null;
            exec($command, $output, $resultCode);

            if ($resultCode !== 0) {
                 // Try without column-statistics if it failed (common issue)
                 $command = "mysqldump --column-statistics=0 --user={$username} --password=" . config('database.connections.mysql.password') . " --host={$host} --port={$port} {$database} > {$filePath}";
                 exec($command, $output, $resultCode);
            }

            if ($resultCode === 0 && file_exists($filePath)) {
                return Response::download($filePath)->deleteFileAfterSend(true);
            } else {
                Log::error("Backup failed. Result code: $resultCode");
                return back()->with('error', 'فشل إنشاء النسخة الاحتياطية. تأكد من إعدادات السيرفر.');
            }

        } catch (\Exception $e) {
            Log::error("Backup exception: " . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء النسخ الاحتياطي: ' . $e->getMessage());
        }
    }

    public function downloadFiles()
    {
        $publicStoragePath = storage_path('app/public');
        $zipFileName = 'files-backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.zip';
        
        // Use a temporary file in the system temp directory
        $tempFile = tempnam(sys_get_temp_dir(), 'backup_zip');
        
        if (!class_exists('ZipArchive')) {
             return back()->with('error', 'PHP ZipArchive extension is not installed.');
        }

        $zip = new \ZipArchive;
        // MUST use OVERWRITE to ensure we start fresh on this temp file
        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicStoragePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($publicStoragePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            
            $zip->close();

            if (file_exists($tempFile) && filesize($tempFile) > 0) {
                return Response::download($tempFile, $zipFileName)->deleteFileAfterSend(true);
            } else {
                 return back()->with('error', 'Failed to create zip file (empty or write error).');
            }
        } else {
             return back()->with('error', 'Could not create Zip archive.');
        }
    }
}
