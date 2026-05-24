<?php
// Simple installation script to create database tables from schema.sql
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

$database = new Database();
$pdo = $database->getConnection();

$schemaFile = __DIR__ . '/database/schema.sql';
if (!file_exists($schemaFile)) {
    die('Schema file not found.');
}

$sql = file_get_contents($schemaFile);
try {
    $pdo->exec($sql);
    echo "Database tables created successfully.\n";
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>