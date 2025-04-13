<?php
session_start();
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$confirmPassword = trim($_POST['confirm_password']);

try {
    // Fetch the stored password hash
    $stmt = $pdo->prepare("SELECT password FROM users WHERE userId = :userId");
    $stmt->execute([':userId' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "User not found!";
        header("Location: ../user_edit_profile.php");
        exit();
    }

    // Verify the password
    if (!password_verify($confirmPassword, $user['password'])) {
        $_SESSION['error'] = "Incorrect password!";
        header("Location: ../user_edit_profile.php");
        exit();
    }

    // Soft Delete: Set 'deleted' column to 1
    $deleteStmt = $pdo->prepare("UPDATE users SET deleted = 1 WHERE userId = :userId");
    $deleteStmt->execute([':userId' => $userId]);

    // Destroy session and redirect to login
    session_destroy();
    header("Location: ../login.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Something went wrong: " . $e->getMessage();
    header("Location: ../user_edit_profile.php");
    exit();
}
?>
