<?php
require_once __DIR__ . '/BaseController.php';

class SettingsController extends BaseController {
    public function index() {
        if (!check_login()) { $this->redirectTo('/login'); }
        if (!is_admin()) { set_flash_message('error', 'Admin access required'); $this->redirectTo('/'); return; }

        $db = (new Database())->getConnection();
        $settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'shop_name' => trim($_POST['shop_name'] ?? ''),
                'shop_address' => trim($_POST['shop_address'] ?? ''),
                'shop_phone' => trim($_POST['shop_phone'] ?? ''),
                'shop_email' => trim($_POST['shop_email'] ?? ''),
                'currency' => trim($_POST['currency'] ?? 'USD'),
                'currency_symbol' => trim($_POST['currency_symbol'] ?? '$'),
                'timezone' => trim($_POST['timezone'] ?? 'UTC'),
                'date_format' => trim($_POST['date_format'] ?? 'Y-m-d'),
                'time_format' => trim($_POST['time_format'] ?? 'H:i:s'),
                'theme' => $_POST['theme'] ?? 'light',
            ];

            if ($settings) {
                $params = array_values($data);
                $params[] = $settings['id'];
                $stmt = $db->prepare("UPDATE settings SET shop_name=?, shop_address=?, shop_phone=?, shop_email=?, currency=?, currency_symbol=?, timezone=?, date_format=?, time_format=?, theme=? WHERE id=?");
                $stmt->execute($params);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
                $stmt = $db->prepare("INSERT INTO settings (shop_name, shop_address, shop_phone, shop_email, currency, currency_symbol, timezone, date_format, time_format, theme) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute(array_values($data));
            }
            set_flash_message('success', 'Settings saved');
            $this->redirectTo('/settings');
        }

        $this->render('settings/index', [
            'title' => 'Settings',
            'settings' => $settings,
        ]);
    }
}
