<?php
require_once '../../includes/config.php';  // Database connection

$errors = [];

/* Get URL supplied data */
$profileId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* Check Data */
if ($profileId == 0) {
    exit();
}

try {
    // Prepare and execute the update query
    $stmt = $pdo->prepare("UPDATE users SET deleted = 1 WHERE userId = ?");
    $stmt->execute([$profileId]);
    if ($stmt->rowCount() <= 0) {
        $errors[] = "No record updated. Profile ID may not exist.";
    }
} catch (Exception $e) {
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
