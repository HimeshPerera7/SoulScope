<?php
session_start();
require_once "../includes/config.php";
//file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);

if (isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $sender_id = $_SESSION['user_id'] ?? 0;
    $receiver_id = (int)$_POST['receiver_id'];
    $message = trim($_POST['message']);
    
    if ($sender_id && $receiver_id && $message !== '') {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at, deleted) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->execute([$sender_id, $receiver_id, $message]);
    }
}
