<?php
/**
 * Base Controller
 */

class BaseController {
    protected $viewData = [];
    protected $layout = 'layout';
    protected $appConfig = [];

    public function __construct() {
        $configPath = __DIR__ . '/../config/app.php';
        if (file_exists($configPath)) {
            $this->appConfig = require $configPath;
        }
        if (!empty($this->appConfig['timezone'])) {
            date_default_timezone_set($this->appConfig['timezone']);
        }
    }

    protected function baseUrl() {
        return isset($this->appConfig['base_url'])
            ? rtrim($this->appConfig['base_url'], '/')
            : '';
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    protected function redirectTo($path) {
        $this->redirect($this->baseUrl() . '/' . ltrim($path, '/'));
    }

    public function render($view, $data = []) {
        $this->viewData = array_merge($this->viewData, $data, [
            'base_url' => $this->baseUrl(),
            'app_config' => $this->appConfig,
        ]);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../views/' . $this->layout . '.php';
        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: $viewPath");
        }
        extract($this->viewData);
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }
}
