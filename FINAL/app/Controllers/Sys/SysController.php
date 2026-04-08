<?php

namespace App\Controllers\Sys;

use App\Controllers\BaseController;
use App\Models\{UserModel, GuruModel, SiswaModel, ActivityLogModel};

class SysController extends BaseController
{
    // ==========================================
    // ACTIVITY LOG
    // ==========================================
    public function activityLog()
    {
        $logModel = new ActivityLogModel();
        $userModel = new UserModel();

        $page = $this->request->getGet('page') ?: 1;
        $perPage = 30;

        $logs = $logModel->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);
        $pager = $logModel->pager;

        // Attach user names
        foreach ($logs as &$log) {
            if ($log['user_id']) {
                $user = $userModel->find($log['user_id']);
                $log['username'] = $user['username'] ?? 'Deleted User';
                $log['role'] = $user['role'] ?? '-';
            } else {
                $log['username'] = 'System';
                $log['role'] = '-';
            }
        }

        return view('sys/activity_log', [
            'title' => 'Activity Log',
            'logs' => $logs,
            'pager' => $pager,
        ]);
    }

    // ==========================================
    // SYSTEM INFO
    // ==========================================
    public function systemInfo()
    {
        $db = \Config\Database::connect();

        // Database size
        $dbName = $db->getDatabase();
        $query = $db->query("SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = '{$dbName}'");
        $dbSize = $query->getRow()->size ?? 0;

        // Table counts
        $tables = $db->query("SHOW TABLE STATUS FROM `{$dbName}`")->getResultArray();

        $info = [
            'php_version' => phpversion(),
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'os' => PHP_OS,
            'db_driver' => $db->getPlatform(),
            'db_version' => $db->getVersion(),
            'db_name' => $dbName,
            'db_size' => $dbSize,
            'max_upload' => ini_get('upload_max_filesize'),
            'max_post' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution' => ini_get('max_execution_time'),
            'timezone' => date_default_timezone_get(),
            'disk_free' => disk_free_space('.'),
            'disk_total' => disk_total_space('.'),
            'tables' => $tables,
        ];

        return view('sys/system_info', [
            'title' => 'System Info',
            'info' => $info,
        ]);
    }

    // ==========================================
    // MANAGE USERS
    // ==========================================
    public function manageUsers()
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('role')->orderBy('username')->findAll();

        // Attach entity names
        $guruModel = new GuruModel();
        $siswaModel = new SiswaModel();
        foreach ($users as &$u) {
            if ($u['role'] === 'guru' && $u['entity_id']) {
                $guru = $guruModel->find($u['entity_id']);
                $u['nama'] = $guru['nama'] ?? '-';
            } elseif ($u['role'] === 'siswa' && $u['entity_id']) {
                $siswa = $siswaModel->find($u['entity_id']);
                $u['nama'] = $siswa['nama'] ?? '-';
            } else {
                $u['nama'] = $u['username'];
            }
        }

        return view('sys/manage_users', [
            'title' => 'Manage Users',
            'users' => $users,
        ]);
    }

    public function toggleUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan');

        $userModel->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);
        $status = $user['is_active'] ? 'dinonaktifkan' : 'diaktifkan';
        return redirect()->back()->with('success', "User {$user['username']} berhasil {$status}");
    }

    public function resetPassword($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan');

        $userModel->update($id, [
            'password' => password_hash('123123', PASSWORD_DEFAULT),
            'is_first_login' => 1,
        ]);
        return redirect()->back()->with('success', "Password {$user['username']} direset ke '123123'");
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) return redirect()->back()->with('error', 'User tidak ditemukan');
        if ($user['id'] === 0) return redirect()->back()->with('error', 'Tidak bisa menghapus akun system');

        $userModel->delete($id);
        return redirect()->back()->with('success', "User {$user['username']} berhasil dihapus");
    }

    // ==========================================
    // DATABASE BACKUP
    // ==========================================
    public function dbBackup()
    {
        return view('sys/db_backup', [
            'title' => 'Database Backup',
        ]);
    }

    public function downloadBackup()
    {
        $db = \Config\Database::connect();
        $dbName = $db->getDatabase();

        $tables = $db->query("SHOW TABLES")->getResultArray();
        $sql = "-- Database Backup: {$dbName}\n-- Generated: " . date('Y-m-d H:i:s') . "\n-- Server: " . ($db->getVersion()) . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            $create = $db->query("SHOW CREATE TABLE `{$tableName}`")->getRowArray();
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $create['Create Table'] . ";\n\n";

            $rows = $db->query("SELECT * FROM `{$tableName}`")->getResultArray();
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $colStr = implode('`, `', $columns);

                foreach (array_chunk($rows, 100) as $chunk) {
                    $sql .= "INSERT INTO `{$tableName}` (`{$colStr}`) VALUES\n";
                    $vals = [];
                    foreach ($chunk as $row) {
                        $escaped = array_map(fn($v) => $v === null ? 'NULL' : "'" . addslashes($v) . "'", array_values($row));
                        $vals[] = '(' . implode(', ', $escaped) . ')';
                    }
                    $sql .= implode(",\n", $vals) . ";\n\n";
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_' . $dbName . '_' . date('Y-m-d_His') . '.sql';
        return $this->response
            ->setHeader('Content-Type', 'application/sql')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($sql);
    }

    // ==========================================
    // ERROR LOG
    // ==========================================
    public function errorLog()
    {
        $logPath = WRITEPATH . 'logs/';
        $logFiles = [];

        if (is_dir($logPath)) {
            $files = glob($logPath . 'log-*.log');
            rsort($files); // newest first
            foreach (array_slice($files, 0, 10) as $file) {
                $logFiles[] = [
                    'name' => basename($file),
                    'size' => filesize($file),
                    'modified' => filemtime($file),
                    'path' => $file,
                ];
            }
        }

        // Read selected or latest file
        $selectedFile = $this->request->getGet('file');
        $content = '';
        if ($selectedFile) {
            $filePath = $logPath . basename($selectedFile);
            if (file_exists($filePath)) {
                // Read last 200 lines
                $lines = file($filePath);
                $content = implode('', array_slice($lines, -200));
            }
        } elseif (!empty($logFiles)) {
            $lines = file($logFiles[0]['path']);
            $content = implode('', array_slice($lines, -200));
            $selectedFile = $logFiles[0]['name'];
        }

        return view('sys/error_log', [
            'title' => 'Error Log',
            'logFiles' => $logFiles,
            'content' => $content,
            'selectedFile' => $selectedFile,
        ]);
    }

    public function clearLog()
    {
        $file = $this->request->getPost('file');
        if ($file) {
            $filePath = WRITEPATH . 'logs/' . basename($file);
            if (file_exists($filePath)) {
                file_put_contents($filePath, '');
            }
        }
        return redirect()->back()->with('success', 'Log file berhasil dikosongkan');
    }

    // ==========================================
    // WEB FILE MANAGER
    // ==========================================
    public function fileManager()
    {
        $basePath = ROOTPATH; // The root of the CodeIgniter project
        $currentPath = $this->request->getGetPost('path') ?: '';
        
        // Security check to prevent directory traversal
        $targetPath = realpath($basePath . $currentPath);
        if (!$targetPath || strpos($targetPath, realpath($basePath)) !== 0) {
            $targetPath = realpath($basePath);
            $currentPath = '';
        }

        $action = $this->request->getPost('action');
        
        // Handle file saving
        if ($action === 'save') {
            $content = $this->request->getPost('content');
            $file = $this->request->getPost('file');
            $savePath = realpath($basePath . $file);
            
            if ($savePath && strpos($savePath, realpath($basePath)) === 0 && is_file($savePath)) {
                file_put_contents($savePath, $content);
                return redirect()->to('/sys/file-manager?path=' . urlencode(dirname($file)))->with('success', 'File berhasil disimpan.');
            }
            return redirect()->back()->with('error', 'Gagal menyimpan file.');
        }

        // Setup variables for the view
        $isDirectory = is_dir($targetPath);
        $fileContent = '';
        $items = [];

        if ($isDirectory) {
            $items = scandir($targetPath);
            // Filter . and ..
            $items = array_diff($items, ['.']);
            if ($currentPath === '') $items = array_diff($items, ['..']); 
            
            // Sort folders first
            usort($items, function($a, $b) use ($targetPath) {
                if ($a === '..') return -1;
                if ($b === '..') return 1;
                $aIsDir = is_dir($targetPath . '/' . $a);
                $bIsDir = is_dir($targetPath . '/' . $b);
                if ($aIsDir && !$bIsDir) return -1;
                if (!$aIsDir && $bIsDir) return 1;
                return strcasecmp($a, $b);
            });
        } elseif (is_file($targetPath)) {
            $fileContent = file_get_contents($targetPath);
        }

        return view('sys/file_manager', [
            'title' => 'Web File Manager',
            'basePath' => $basePath,
            'currentPath' => $currentPath,
            'targetPath' => $targetPath,
            'isDirectory' => $isDirectory,
            'items' => $items,
            'fileContent' => $fileContent
        ]);
    }

    // ==========================================
    // WEB TERMINAL
    // ==========================================
    public function terminal()
    {
        $output = '';
        $command = '';
        $cwd = ROOTPATH;

        if ($this->request->getMethod() === 'POST') {
            $rawCommand = $this->request->getPost('command');
            $command = escapeshellcmd($rawCommand);
            
            if (!empty($command)) {
                // Change directory to rootpath before executing
                $fullCommand = "cd " . escapeshellarg($cwd) . " && " . $command . " 2>&1";
                
                if (function_exists('shell_exec')) {
                    $output = shell_exec($fullCommand);
                    if ($output === null) $output = "Command returned no output or shell_exec is disabled.";
                } else {
                    $output = "Error: shell_exec() function is disabled on this server.";
                }
            }
        }

        return view('sys/terminal', [
            'title' => 'Web Terminal',
            'output' => $output,
            'command' => $command,
            'cwd' => $cwd
        ]);
    }

    // ==========================================
    // DEVELOPER SETTINGS & CACHE
    // ==========================================
    public function settings()
    {
        $envPath = ROOTPATH . '.env';
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        if ($this->request->getMethod() === 'POST') {
            $action = $this->request->getPost('action');

            if ($action === 'save_env') {
                $newContent = $this->request->getPost('env_content');
                if (file_put_contents($envPath, $newContent) !== false) {
                    return redirect()->back()->with('success', 'File .env berhasil diperbarui.');
                }
                return redirect()->back()->with('error', 'Gagal memperbarui file .env. Periksa permission.');
            }

            if ($action === 'clear_cache') {
                // Clear session data
                $sessionPath = WRITEPATH . 'session/';
                if (is_dir($sessionPath)) {
                    $files = glob($sessionPath . '*');
                    foreach ($files as $file) {
                        if (is_file($file) && basename($file) !== 'index.html') {
                            @unlink($file);
                        }
                    }
                }
                // Clear cache data
                $cachePath = WRITEPATH . 'cache/';
                if (is_dir($cachePath)) {
                    $files = glob($cachePath . '*');
                    foreach ($files as $file) {
                        if (is_file($file) && basename($file) !== 'index.html') {
                            @unlink($file);
                        }
                    }
                }
                return redirect()->back()->with('success', 'Cache & Session berhasil dibersihkan! Semua pengguna mungkin telah logout.');
            }
        }

        return view('sys/settings', [
            'title' => 'System Configuration',
            'envContent' => $envContent
        ]);
    }

    // ==========================================
    // DATABASE MANAGER GUI
    // ==========================================
    public function databaseManager()
    {
        $db = \Config\Database::connect();
        
        $tables = $db->listTables();
        $selectedTable = $this->request->getGet('table');
        $queryResult = null;
        $queryError = null;
        $queryType = null; // 'select' or 'action'
        $customQuery = $this->request->getPost('custom_query') ?: '';

        // Default query: Select Top 100 rows if table selected
        if ($selectedTable && in_array($selectedTable, $tables) && !$this->request->is('POST')) {
            $customQuery = "SELECT * FROM `" . escapeshellcmd($selectedTable) . "` LIMIT 100";
        }

        if ($this->request->getMethod() === 'POST' || !empty($customQuery)) {
            try {
                $query = $db->query($customQuery);
                
                // Identify query type roughly
                $upperQuery = strtoupper(trim($customQuery));
                if (strpos($upperQuery, 'SELECT') === 0 || strpos($upperQuery, 'SHOW') === 0 || strpos($upperQuery, 'DESCRIBE') === 0 || strpos($upperQuery, 'EXPLAIN') === 0) {
                    $queryType = 'select';
                    $queryResult = $query->getResultArray();
                } else {
                    $queryType = 'action';
                    $queryResult = $db->affectedRows();
                }
            } catch (\Exception $e) {
                $queryError = $e->getMessage();
            }
        }

        return view('sys/database', [
            'title' => 'Database Manager',
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'customQuery' => $customQuery,
            'queryResult' => $queryResult,
            'queryError' => $queryError,
            'queryType' => $queryType
        ]);
    }
}
