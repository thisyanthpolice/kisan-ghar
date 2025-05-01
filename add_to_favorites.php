<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    $id = $data['id'];
    $type = $data['type'];

    // Check if already in favorites
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND " . 
        ($type === 'product' ? "product_id = ?" : "health_box_id = ?"));
    $stmt->execute([$user_id, $id]);
    
    if (!$stmt->fetch()) {
        // Add to favorites if not already present
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, " . 
            ($type === 'product' ? "product_id" : "health_box_id") . 
            ") VALUES (?, ?)");
        $stmt->execute([$user_id, $id]);
        echo json_encode(['message' => 'Added to favorites successfully']);
    } else {
        echo json_encode(['message' => 'Already in favorites']);
    }
    exit();
}
?>