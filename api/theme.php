<?php
// Simple endpoint to set UI theme (light/dark)
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['theme'])) {
    $theme = ($_POST['theme'] === 'dark') ? 'dark' : 'light';
    $_SESSION['theme'] = $theme;
    // Optionally, persist theme to user settings in DB if needed
    http_response_code(200);
    echo json_encode(['status' => 'success', 'theme' => $theme]);
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>