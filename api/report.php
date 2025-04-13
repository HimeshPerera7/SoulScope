<?php
session_start();
require_once '../includes/config.php';  // Database connection

$errors = [];

/* Get current user ID */
$userId = isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : 0;

/* Get URL supplied data */
$profileId = isset($_GET['profile']) ? (int) $_GET['profile'] : 0;
$reason = isset($_GET['reason']) ? htmlspecialchars($_GET['reason']) : '';

/* Check Data */
if ($userId == 0) {
    $errors[] = "Invalid User ID";
}

if ($profileId == 0) {
    $errors[] = "Invalid Profile ID";
}

/* Fetching all cities in the selected district */
try {
    $reportedDate = date("Y-m-d H:i:s");
    $deleted = 0;
    
    $sql = "INSERT INTO profile_reports (reported_user, profile, reason, reported_date, deleted) VALUES (:reported_user, :profile, :reason, :reported_date, :deleted)";
    $stmt = $pdo->prepare($sql);
    
    // Bind values
    $stmt->bindParam(":reported_user", $userId);
    $stmt->bindParam(":profile", $profileId);
    $stmt->bindParam(":reason", $reason);
    $stmt->bindParam(":reported_date", $reportedDate);
    $stmt->bindParam(":deleted", $deleted);
    $stmt->execute();
    
    // Get record ID
    $lastId = $pdo->lastInsertId();
    
} catch (PDOException $e) {
    $errors[] = $e->getMessage();
}

// Set HTTP headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: same-origin");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Return JSON response
if (count($errors) > 0) {
    $response = array(
        "success" => false,
        "data" => $errors,
    );
} else {
    $response = array(
        "success" => true,
        "data" => ''
    );
}

echo json_encode($response);
