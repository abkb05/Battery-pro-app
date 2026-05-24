<?php
/**
 * Authentication Helper Functions
 */

use \Firebase\JWT\JWT;

if (!function_exists('hash_password')) {
    function hash_password($password) {
        $cost = isset($_ENV['PASSWORD_HASH_COST']) ? (int)$_ENV['PASSWORD_HASH_COST'] : 12;
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }
}

if (!function_exists('verify_password')) {
    function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }
}

if (!function_exists('generate_token')) {
    function generate_token($length = 32) {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists('create_jwt')) {
    function create_jwt($payload, $secret_key) {
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return $jwt;
    }
}

if (!function_exists('verify_jwt')) {
    function verify_jwt($jwt, $secret_key) {
        try {
            $decoded = JWT::decode($jwt, new \Firebase\JWT\Key($secret_key, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('set_flash_message')) {
    function set_flash_message($type, $message) {
        $_SESSION['flash'][$type][] = $message;
    }
}

if (!function_exists('get_flash_message')) {
    function get_flash_message($type = null) {
        if (!isset($_SESSION['flash'])) {
            return [];
        }
        
        if ($type === null) {
            $all = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $all;
        }
        
        if (isset($_SESSION['flash'][$type])) {
            $messages = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $messages;
        }
        
        return [];
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}

if (!function_exists('is_staff')) {
    function is_staff() {
        return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'staff']);
    }
}

if (!function_exists('check_login')) {
    function check_login() {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }
}

if (!function_exists('log_audit')) {
    function log_audit($action, $entityType, $entityId = null, $oldValues = null, $newValues = null) {
        try {
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("INSERT INTO audit_log (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['user_id'] ?? null,
                $action,
                $entityType,
                $entityId,
                $oldValues ? json_encode($oldValues) : null,
                $newValues ? json_encode($newValues) : null,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {}
    }
}

if (!function_exists('currency_format')) {
    function currency_format($amount, $decimals = 2) {
        $symbol = 'Rs. ';
        static $settingsSymbol = null;
        if ($settingsSymbol === null) {
            try {
                $db = (new Database())->getConnection();
                $row = $db->query("SELECT currency_symbol FROM settings LIMIT 1")->fetch();
                if ($row && !empty($row['currency_symbol'])) {
                    $symbol = $row['currency_symbol'] . ' ';
                }
            } catch (Exception $e) {}
        }
        return $symbol . number_format((float)$amount, $decimals);
    }
}

/**
 * Generate a route URL that works with or without clean URLs.
 * @param string $path  e.g. '/dashboard' or 'suppliers'
 * @return string       Full URL to the route
 */
if (!function_exists('site_url')) {
    function site_url($path = '') {
        static $base = null, $clean = null;
        if ($base === null) {
            $cfg = @include __DIR__ . '/../config/app.php';
            $base = $cfg['base_url'] ?? '';
            $base = rtrim($base, '/');
            $clean = $cfg['clean_urls'] ?? true;
        }
        $path = '/' . ltrim($path, '/');
        // If clean URLs are off, use query parameter fallback
        if (!$clean) {
            return $base . '/index.php?url=' . ltrim($path, '/');
        }
        return $base . $path;
    }
}