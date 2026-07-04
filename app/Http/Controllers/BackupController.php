<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Ifsnop\Mysqldump\Mysqldump;

class BackupController extends Controller
{
    public function index()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $files = File::files($backupDir);
        
        $backups = [];
        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $this->formatSizeUnits($file->getSize()),
                    'created_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime())->format('d-m-Y H:i:s'),
                    'timestamp' => $file->getMTime()
                ];
            }
        }

        // Sort backups by newest first
        usort($backups, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return view('master.backups', compact('backups'));
    }

    public function create()
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupDir . '/' . $filename;

            // Get DB config from env
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbConn = env('DB_CONNECTION', 'mysql');

            // Determine DSN based on driver (mysql, pgsql, sqlite)
            if ($dbConn === 'sqlite') {
                $dsn = "sqlite:$dbName";
            } else {
                $dsn = "$dbConn:host=$dbHost;port=$dbPort;dbname=$dbName";
            }

            $dump = new Mysqldump($dsn, $dbUser, $dbPass);
            $dump->start($filePath);

            return back()->with('success', 'Backup database berhasil dibuat: ' . $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $file = storage_path('app/backups/' . $filename);
        
        if (File::exists($file)) {
            return response()->download($file);
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    public function restore(Request $request, $filename)
    {
        try {
            $file = storage_path('app/backups/' . $filename);
            
            if (!File::exists($file)) {
                return back()->with('error', 'File backup tidak ditemukan.');
            }

            // Restore DB using DB::unprepared
            $sql = File::get($file);
            DB::unprepared($sql);

            return back()->with('success', 'Database berhasil direstore dari: ' . $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal merestore database: ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        $file = storage_path('app/backups/' . $filename);
        
        if (File::exists($file)) {
            File::delete($file);
            return back()->with('success', 'File backup berhasil dihapus.');
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
