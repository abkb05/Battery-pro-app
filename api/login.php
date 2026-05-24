<?php
// API Login endpoint – returns JWT token on successful authentication
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../models/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

$userModel = new User();
$user = $userModel->login($username, $password);

if ($user) {
    $payload = [
        'sub' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'iat' => time(),
        'exp' => time() + 3600 // token valid for 1 hour
    ];
    $appCfg = $GLOBALS['appConfig'];
    $secretKey = $appCfg['jwt_secret'];
    $jwt = create_jwt($payload, $secretKey);
    echo json_encode(['token' => $jwt]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
?>