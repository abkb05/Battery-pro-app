<?php
require_once __DIR__ . '/BaseController.php';

class AuditController extends BaseController {
    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if (!is_admin()) { set_flash_message('error', 'Admin access required'); $this->redirectTo('/'); return; }

        $db = (new Database())->getConnection();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        $total = $db->query("SELECT COUNT(*) as c FROM audit_log")->fetch()['c'];
        $logs = $db->prepare("SELECT al.*, u.full_name as user_name FROM audit_log al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT ? OFFSET ?");
        $logs->execute([$perPage, $offset]);

        $this->render('audit/index', [
            'title' => 'Audit Log', 'logs' => $logs->fetchAll(),
            'page' => $page, 'totalPages' => ceil($total / $perPage),
        ]);
    }
}
