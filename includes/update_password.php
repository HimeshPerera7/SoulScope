<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        die("New passwords do not match!");
    }

    try {
        $sql = "SELECT password FROM users WHERE userId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            die("Current password is incorrect!");
        }

        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password WHERE userId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':password' => $newHashedPassword, ':userId' => $userId]);

        
        header("Location: ../user_edit_profile.php?success=Password changed successfully");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
