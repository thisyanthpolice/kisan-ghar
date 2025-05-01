<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    $id = $data['id'];
    $type = $data['type'];
    $quantity = $data['quantity'] ?? 1;

    // Check if already in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND " . 
        ($type === 'product' ? "product_id = ?" : "health_box_id = ?"));
    $stmt->execute([$user_id, $id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update quantity if already in cart
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $existing['id']]);
    } else {
        // Add new item to cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, " . 
            ($type === 'product' ? "product_id" : "health_box_id") . 
            ", quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $id, $quantity]);
    }
    
    http_response_code(200);
    echo json_encode(['message' => 'Added to cart successfully']);
}
?>