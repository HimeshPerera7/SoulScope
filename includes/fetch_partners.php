<?php
require_once 'config.php';  // Database connection
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$currentUserId = $_SESSION['user_id'];  // Session key

// Fetch logged-in user's details
$userQuery = "SELECT gender, zodiacSign FROM users WHERE userId = :userId AND deleted = 0";
$stmt = $pdo->prepare($userQuery);
$stmt->execute([':userId' => $currentUserId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo json_encode(["error" => "User not found"]);
    exit();
}

$gender = $userData['gender'];
$sign = $userData['zodiacSign'];

// Define compatible zodiac signs
//$compatibleSigns = array();

switch ($sign) {
    case 'Aries':
        $compatibleSigns = array("Leo", "Sagittarius");
        break;
    case 'Taurus':
        $compatibleSigns = array("Virgo", "Capricorn");
        break;
    case 'Gemini':
        $compatibleSigns = array("Libra", "Aquarius");
        break;
    case 'Cancer':
        $compatibleSigns = array("Scorpio", "Pisces");
        break;
    case 'Leo':
        $compatibleSigns = array("Aries", "Sagittarius");
        break;
    case 'Virgo':
        $compatibleSigns = array("Taurus", "Capricorn");
        break;
    case 'Libra':
        $compatibleSigns = array("Gemini", "Aquarius");
        break;
    case 'Scorpio':
        $compatibleSigns = array("Cancer", "Pisces");
        break;
    case 'Sagittarius':
        $compatibleSigns = array("Aries", "Leo");
        break;
    case 'Capricorn':
        $compatibleSigns = array("Taurus", "Virgo");
        break;
    case 'Aquarius':
        $compatibleSigns = array("Gemini", "Libra");
        break;
    case 'Pisces':
        $compatibleSigns = array("Cancer", "Scorpio");
        break;
}

//$compatibleSigns = [
//    "Aries" => ["Leo", "Sagittarius"],
//    "Taurus" => ["Virgo", "Capricorn"],
//    "Gemini" => ["Libra", "Aquarius"],
//    "Cancer" => ["Scorpio", "Pisces"],
//    "Leo" => ["Aries", "Sagittarius"],
//    "Virgo" => ["Taurus", "Capricorn"],
//    "Libra" => ["Gemini", "Aquarius"],
//    "Scorpio" => ["Cancer", "Pisces"],
//    "Sagittarius" => ["Aries", "Leo"],
//    "Capricorn" => ["Taurus", "Virgo"],
//    "Aquarius" => ["Gemini", "Libra"],
//    "Pisces" => ["Cancer", "Scorpio"]
//];

// Get compatible signs for the user
//$compatible = $compatibleSigns[$sign] ?? [];

// Check if there are compatible signs
if (empty($compatible)) {
    echo json_encode([]);
    exit();
}

// Dynamically generate named placeholders
$placeholders = implode(',', array_map(fn($k) => ":sign$k", array_keys($compatible)));

// Fetch compatible users (excluding current user & admins)
$query = "SELECT userId, firstName, lastName, zodiacSign, profilePic, 
                 TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) AS age 
          FROM users 
          WHERE gender != :gender 
          AND zodiacSign IN ($placeholders) 
          AND userId != :currentUserId 
          AND deleted = 0 
          AND role = 'user'
          ORDER BY RAND() 
          LIMIT 5";

$stmt = $pdo->prepare($query);

// Bind parameters properly
$params = [':gender' => $gender, ':currentUserId' => $currentUserId];
foreach ($compatible as $index => $zodiac) {
    $params[":sign$index"] = $zodiac;
}

$stmt->execute($params);

