<?php

require_once '../includes/config.php';  // Database connection

$errors = array();

/* Get district id */
$district = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($district == 0) {
    $errors[] = "Invalid district selected";
}

/* Fetching all cities in the selected district */
try {
    $sql = "SELECT id, name_en FROM cities WHERE district_id = :id ORDER BY name_en ASC ;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $district]);
    $cities = $stmt->fetchAll( PDO::FETCH_ASSOC );
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
        "result" => "fail",
        "data" => $errors,
    );
} else {
    $response = array(
        "result" => "success",
        "data" => $cities
    );
}

echo json_encode($response);
