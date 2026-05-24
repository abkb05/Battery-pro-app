<?php
/**
 * Application Configuration
 */

return [
    'app_name' => 'BatteryPro Management System',
    'version' => '1.0.0',
    'base_url' => 'http://localhost:8080/batterypro/',
    'clean_urls' => true,  // set to false if mod_rewrite is not available
    'timezone' => 'UTC',
    'cookie_name' => 'batterypro_session',
    'cookie_lifetime' => 3600,
    'session_lifetime' => 7200,
    'jwt_secret' => 'your-very-secret-key',
    
    // Security settings
    'password_hash_cost' => 12,
    'token_length' => 32,
    'remember_me_duration' => 2592000, // 30 days in seconds
    
    // File upload settings
    'max_file_size' => 5242880, // 5MB
    'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif'],
    'upload_path' => __DIR__ . '/../uploads/',
    
    // Backup settings
    'backup_path' => __DIR__ . '/../database/backups/',
    'auto_backup_time' => '02:00', // 2 AM daily
    
    // Report settings
    'report_path' => __DIR__ . '/../database/reports/',
];