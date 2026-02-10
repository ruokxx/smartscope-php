<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => $this->humanFilesize($disk->size($file)),
                    'created_at' => Carbon::createFromTimestamp($disk->lastModified($file)),
                    'path' => $file
                ];
            }
        }

        // Sort by created_at desc
        usort($backups, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return view('admin.backups.index', compact('backups'));
    }

    public function store()
    {
        $filename = 'backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Ensure directory exists
        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        // Basic mysqldump command
        // Note: passing password on command line can be insecure in shared envs, but okay for local/simple use.
        // For Windows, we might need quotes around paths if they have spaces, but storage_path usually uses forward slashes in Laravel or proper separators.
        // We'll try a standard command.

        $command = "mysqldump --user=\"{$dbUser}\" --password=\"{$dbPass}\" --host=\"{$dbHost}\" \"{$dbName}\" > \"{$path}\"";

        // On some restricted hosts or specific setups, mysqldump might not be in PATH.
        // attempting to executing it.

        $output = null;
        $result = null;
        exec($command, $output, $result);

        if ($result === 0 && File::exists($path) && File::size($path) > 0) {
            return redirect()->route('admin.backups.index')->with('success', 'Backup created successfully.');
        }
        else {
            // cleanup empty file if created
            if (File::exists($path)) {
                File::delete($path);
            }
            return redirect()->route('admin.backups.index')->with('error', 'Failed to create backup. Check if mysqldump is installed and accessible.');
        }
    }

    public function download($filename)
    {
        if (Storage::disk('local')->exists('backups/' . $filename)) {
            return Storage::disk('local')->download('backups/' . $filename);
        }
        return redirect()->back()->with('error', 'File not found.');
    }

    public function destroy($filename)
    {
        if (Storage::disk('local')->exists('backups/' . $filename)) {
            Storage::disk('local')->delete('backups/' . $filename);
            return redirect()->route('admin.backups.index')->with('success', 'Backup deleted.');
        }
        return redirect()->back()->with('error', 'File not found.');
    }

    private function humanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
