<?php
session_start();
require_once "../includes/config.php";

$sender_id = $_SESSION['user_id'] ?? 0;
$message_id = $_POST['message_id'] ?? 0;

if ($sender_id && $message_id) {
    $stmt = $pdo->prepare("UPDATE messages SET deleted = 1 WHERE id = ? AND sender_id = ?");
    $stmt->execute([$message_id, $sender_id]);
    echo 'success';
}
