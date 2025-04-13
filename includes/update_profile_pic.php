<?php
session_start();
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["avatar"])) {
    $avatar = $_FILES["avatar"];

    // Allowed file types and max size (2MB)
    $allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($avatar["type"], $allowedTypes) || $avatar["size"] > $maxFileSize) {
        die("Invalid file type or file too large!");
    }

    // Get file extension
    $ext = pathinfo($avatar["name"], PATHINFO_EXTENSION);
    
    // Generate unique filename
    $newFileName = "user_" . $userId . "_" . time() . "." . $ext;
    $uploadPath = "../assets/images/avatar/" . $newFileName;

    // Move file to the correct directory
    if (move_uploaded_file($avatar["tmp_name"], $uploadPath)) {
        try {
            // Update database
            $sql = "UPDATE users SET profilePic = :profilePic WHERE userId = :userId";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':profilePic' => $newFileName, ':userId' => $userId]);

            // Redirect back
            header("Location: ../user_edit_profile.php?success=Profile picture updated");
            exit();
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        die("Error uploading file.");
    }
}
?>
