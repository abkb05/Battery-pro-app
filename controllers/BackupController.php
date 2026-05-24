<?php
require_once __DIR__ . '/BaseController.php';

class BackupController extends BaseController {
    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if (!is_admin()) { set_flash_message('error', 'Admin access required'); $this->redirectTo('/'); return; }

        $db = (new Database())->getConnection();
        $backups = $db->query("SELECT * FROM backups ORDER BY created_at DESC LIMIT 20")->fetchAll();

        $this->render('backups/index', ['title' => 'Backups', 'backups' => $backups]);
    }

    public function create() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if (!is_admin()) { set_flash_message('error', 'Admin access required'); $this->redirectTo('/'); return; }

        $backupDir = __DIR__ . '/../database/backups/';
        if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $filepath = $backupDir . $filename;

        $cmd = '"C:\\xampp\\mysql\\bin\\mysqldump.exe" -u root batterypro > "' . $filepath . '" 2>&1';
        $output = shell_exec($cmd);

        $success = file_exists($filepath) && filesize($filepath) > 0;
        $db = (new Database())->getConnection();

        if ($success) {
            $db->prepare("INSERT INTO backups (filename, file_size, backup_type, status, created_at) VALUES (?, ?, 'manual', 'completed', NOW())")
                ->execute([$filename, filesize($filepath)]);
            log_audit('create', 'backup', null, null, ['filename' => $filename]);
            set_flash_message('success', 'Backup created: ' . $filename);
        } else {
            $db->prepare("INSERT INTO backups (filename, backup_type, status, created_at) VALUES (?, 'manual', 'failed', NOW())")
                ->execute([$filename]);
            set_flash_message('error', 'Backup failed. Make sure mysqldump is available.');
        }

        $this->redirectTo('/backups');
    }

    public function download($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        if (!is_admin()) { $this->redirectTo('/'); return; }

        $db = (new Database())->getConnection();
        $backup = $db->prepare("SELECT * FROM backups WHERE id = ?");
        $backup->execute([$id]);
        $backup = $backup->fetch();
        if (!$backup) { $this->redirectTo('/backups'); return; }

        $filepath = __DIR__ . '/../database/backups/' . $backup['filename'];
        if (!file_exists($filepath)) { set_flash_message('error', 'File not found'); $this->redirectTo('/backups'); return; }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $backup['filename'] . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
