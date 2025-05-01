<?php
require_once 'db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$category = trim($data['category'] ?? '');
$name = trim($data['name'] ?? '');
$image_url = trim($data['image_url'] ?? '');
$quantity = trim($data['quantity'] ?? '');
$price = trim($data['price'] ?? '');
$edit_id = isset($data['edit_id']) ? intval($data['edit_id']) : 0;

if (!$category || !$name || !$image_url || !$quantity || !$price) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($edit_id) {
    // Update existing product
    $stmt = $pdo->prepare("UPDATE products SET category = ?, name = ?, image_url = ?, quantity_available = ?, price = ? WHERE id = ? AND farmer_id = ?");
    $ok = $stmt->execute([$category, $name, $image_url, $quantity, $price, $edit_id, $user_id]);
} else {
    // Insert new product
    $stmt = $pdo->prepare("INSERT INTO products (farmer_id, category, name, image_url, quantity_available, price) VALUES (?, ?, ?, ?, ?, ?)");
    $ok = $stmt->execute([$user_id, $category, $name, $image_url, $quantity, $price]);
}

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
