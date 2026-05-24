<?php
require_once __DIR__ . '/BaseController.php';

class ExchangeController extends BaseController {

    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $exchanges = $db->query("SELECT e.*, s.id as sale_id FROM exchange_batteries e LEFT JOIN sales s ON e.sale_id = s.id ORDER BY e.created_at DESC")->fetchAll();
        $this->render('exchange/index', [
            'title' => 'Exchange Batteries',
            'exchanges' => $exchanges,
        ]);
    }

    public function markScrapped($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $db->prepare("UPDATE exchange_batteries SET scrap_status = 'scrapped' WHERE id = ?")->execute([$id]);
        log_audit('update', 'exchange', $id, null, ['status' => 'scrapped']);
        set_flash_message('info', 'Battery marked as scrapped');
        $this->redirectTo('/exchange');
    }

    public function markSold($id) {
        if (!check_login()) { $this->redirectTo('/login'); }
        $db = (new Database())->getConnection();
        $db->prepare("UPDATE exchange_batteries SET scrap_status = 'sold' WHERE id = ?")->execute([$id]);
        log_audit('update', 'exchange', $id, null, ['status' => 'sold']);
        set_flash_message('success', 'Battery marked as sold');
        $this->redirectTo('/exchange');
    }
}