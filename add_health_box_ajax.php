<?php
require_once 'db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nutritionist') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$image_url = trim($data['image_url'] ?? '');
$price = trim($data['price'] ?? '');

if (!$name || !$description || !$image_url || !$price) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("INSERT INTO health_boxes (nutritionist_id, name, description, image_url, price) VALUES (?, ?, ?, ?, ?)");
$ok = $stmt->execute([$user_id, $name, $description, $image_url, $price]);

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
