<?php
require_once 'db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nutritionist') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$name = trim($data['name'] ?? '');
$description = trim($data['description'] ?? '');
$image_url = trim($data['image_url'] ?? '');
$price = trim($data['price'] ?? '');

if (!$id || !$name || !$description || !$image_url || !$price) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("UPDATE health_boxes SET name = ?, description = ?, image_url = ?, price = ? WHERE id = ? AND nutritionist_id = ?");
$ok = $stmt->execute([$name, $description, $image_url, $price, $id, $user_id]);

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
