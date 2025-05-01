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

    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND " . 
        ($type === 'product' ? "product_id = ?" : "health_box_id = ?"));
    $stmt->execute([$user_id, $id]);
    
    echo json_encode(['message' => 'Removed from favorites successfully']);
}
?>