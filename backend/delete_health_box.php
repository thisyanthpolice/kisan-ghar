<?php
require_once 'db_connect.php';
require_once 'functions.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array(getUserRole(), ['nutritionist', 'admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $user_id = $_SESSION['user_id'];
    $role = getUserRole();

    // If nutritionist, can only delete own health boxes
    if ($role === 'nutritionist') {
        $stmt = $pdo->prepare("DELETE FROM health_boxes WHERE id = ? AND nutritionist_id = ?");
        $ok = $stmt->execute([$id, $user_id]);
    } else {
        // Admin can delete any health box
        $stmt = $pdo->prepare("DELETE FROM health_boxes WHERE id = ?");
        $ok = $stmt->execute([$id]);
    }
    echo json_encode(['success' => $ok]);
    exit;
}
?>