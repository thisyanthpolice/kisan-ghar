<?php
require_once 'db_connect.php';
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user_id']) || getUserRole() !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    // Admin can't delete other admins or themselves
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin' AND id != ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}
?>