<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    private $settings;

    public function __construct() {
        $this->userModel = new User();
        $this->settings = $GLOBALS['appConfig'] ?? require __DIR__ . '/../config/app.php';
        
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function baseUrl() {
        if (isset($this->settings['base_url'])) {
            return rtrim($this->settings['base_url'], '/');
        }
        return '';
    }

    public function login() {
        $base = $this->baseUrl();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $rememberMe = isset($_POST['remember_me']);

            // Validate input
            if (empty($username) || empty($password)) {
                set_flash_message('error', 'Username and password are required');
                redirect($base . '/login');
                return;
            }

            // Attempt login
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                // Remember me functionality
                if ($rememberMe) {
                    $token = generate_token();
                    ini_set('session.gc_maxlifetime', $this->settings['remember_me_duration']);
                    session_set_cookie_params($this->settings['remember_me_duration']);
                }
                
                set_flash_message('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
                redirect($base . '/');
            } else {
                set_flash_message('error', 'Invalid username or password');
                redirect($base . '/login');
            }
        } else {
            // Show login form
            $app_config = $this->settings;
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout() {
        $base = $this->baseUrl();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        set_flash_message('info', 'You have been logged out successfully');
        redirect($base . '/login');
    }

    public function profile() {
        $base = $this->baseUrl();
        if (!check_login()) {
            redirect($base . '/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
                $user = $this->userModel->find($userId);
                if ($user && verify_password($_POST['current_password'], $user['password'])) {
                    if ($_POST['new_password'] === $_POST['confirm_password']) {
                        $data['password'] = hash_password($_POST['new_password']);
                    } else {
                        set_flash_message('error', 'New passwords do not match');
                        redirect($base . '/profile');
                        return;
                    }
                } else {
                    set_flash_message('error', 'Current password is incorrect');
                    redirect($base . '/profile');
                    return;
                }
            }
            
            $this->userModel->update($userId, $data);
            set_flash_message('success', 'Profile updated successfully');
            redirect($base . '/profile');
            return;
        }
        
        $user = $this->userModel->find($_SESSION['user_id']);
        require_once __DIR__ . '/../views/auth/profile.php';
    }
}