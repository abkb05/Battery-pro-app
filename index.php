<?php
// Front controller for BatteryPro Management System

// Include Composer autoload if present (for external libraries)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Include our simple autoloader for MVC classes
require_once __DIR__ . '/config/autoload.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load routes and dispatch request
require_once __DIR__ . '/routes/web.php';
?>