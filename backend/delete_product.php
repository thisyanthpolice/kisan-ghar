<?php
require_once 'db_connect.php';
require_once 'functions.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array(getUserRole(), ['farmer', 'admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $user_id = $_SESSION['user_id'];
    $role = getUserRole();

    // If farmer, can only delete own products
    if ($role === 'farmer') {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND farmer_id = ?");
        $ok = $stmt->execute([$id, $user_id]);
    } else {
        // Admin can delete any product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $ok = $stmt->execute([$id]);
    }
    echo json_encode(['success' => $ok]);
    exit;
}
?>