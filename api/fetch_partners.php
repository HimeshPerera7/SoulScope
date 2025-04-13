<?php

$errors = array();
$record = array();
$data = array();

require_once '../includes/config.php';  // Database connection

$currentUserId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($currentUserId == 0) {
    $errors[] = "Invalid user selected";
    sendResponse($errors, $results);
    exit();
}

// Fetch user's details
$userQuery = "SELECT gender, zodiacSign, TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) AS age FROM users WHERE userId = :userId AND deleted = 0";
$stmt = $pdo->prepare($userQuery);
$stmt->bindParam(":userId", $currentUserId, PDO::PARAM_INT);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (count($userData) == 0) {
    $errors[] = "User not found";
    sendResponse($errors, $results);
    exit();
}

$gender = $userData['gender'];
$sign = $userData['zodiacSign'];

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

// Check if there are compatible signs
if (empty($compatibleSigns)) {
    $errors[] = "No any compatible Zodiac Sign found";
    sendResponse($errors, $results);
    exit();
}

// Dynamically generate named placeholders
//if (count($compatibleSigns) > 0) {
//    $Compatibles = implode(',', $compatibleSigns);
//}
//$placeholders = implode(',', array_fill(0, count($compatibleSigns), '?'));

// Fetch compatible users (excluding current user & admins)

$placeholders = [];
$params = [];
foreach ($compatibleSigns as $index => $name) {
    $paramName = ":name$index";
    $placeholders[] = $paramName;
    $params[$paramName] = $name;
}

$placeholdersString = implode(", ", $placeholders);

$query = sprintf("SELECT u.userId, u.firstName, u.lastName, u.zodiacSign, u.profilePic,
                 TIMESTAMPDIFF(YEAR, u.birthDate, CURDATE()) AS age, c.name_en AS cityName
          FROM users AS u
          LEFT JOIN cities AS c ON u.city=c.id
          WHERE u.gender <> '%s'
          AND u.zodiacSign IN ($placeholdersString)
          AND u.userId <> '%s'
          AND u.deleted = 0
          AND u.role = 'user'
          ORDER BY RAND()
          LIMIT 5", $gender, (int)$currentUserId);

$stmt = $pdo->prepare($query);
//$stmt->bindParam(":gender", $gender, PDO::PARAM_STR);
//$stmt->bindParam(":compatibles", $placeholders, PDO::PARAM_STR);
//$stmt->bindParam(":currentUserId", $currentUserId, PDO::PARAM_INT);
$stmt->execute($params);
$matches = $stmt->fetchAll( PDO::FETCH_ASSOC );

function getElement($sign) {
    $elements = array(
        "Aries" => "Fire", "Leo" => "Fire", "Sagittarius" => "Fire",
        "Taurus" => "Earth", "Virgo" => "Earth", "Capricorn" => "Earth",
        "Gemini" => "Air", "Libra" => "Air", "Aquarius" => "Air",
        "Cancer" => "Water", "Scorpio" => "Water", "Pisces" => "Water"
    );
    return $elements[$sign] ?? null;
}

function zodiacCompatibilityScore($sign1, $sign2) {
    $element1 = getElement($sign1);
    $element2 = getElement($sign2);
    
    if ($element1 === $element2) {
        return 50 + 40; // Same element
    } elseif (
        ($element1 === "Fire" && $element2 === "Air") || ($element1 === "Air" && $element2 === "Fire") ||
        ($element1 === "Water" && $element2 === "Earth") || ($element1 === "Earth" && $element2 === "Water")
    ) {
        return 50 + 30; // Complementary
    } elseif (
        ($element1 === "Fire" && $element2 === "Water") || ($element1 === "Water" && $element2 === "Fire") ||
        ($element1 === "Air" && $element2 === "Earth") || ($element1 === "Earth" && $element2 === "Air")
    ) {
        return 50 + 10; // Conflict
    } else {
        return 50 + 20; // Neutral
    }
}

function ageModifier($age1, $age2) {
    $diff = abs($age1 - $age2);
    if ($diff <= 3) return 10;
    elseif ($diff <= 6) return 5;
    elseif ($diff <= 10) return 2;
    else return 0;
}

if (count($matches) > 0) {
    foreach ($matches as $match) {
        $record["id"] = (int)$match["userId"];
        $record["name"] = htmlspecialchars($match["firstName"] . " " . $match["lastName"]);
        $record["age"] = (int)($match["age"]);
        $record["zodiacSign"] = htmlspecialchars($match["zodiacSign"]);
        $record["profilePic"] = htmlspecialchars($match["profilePic"]);
        $record["city"] = $match["cityName"];
        
        // Compatibility calculation
        $baseScore = zodiacCompatibilityScore($sign, $match["zodiacSign"]);
        $ageBonus = ageModifier($userData['age'] ?? 0, (int)$match["age"]);
        $finalScore = min(100, $baseScore + $ageBonus);
        $record["compatibility"] = $finalScore;
        
        $data[] = $record;
    }
    sendResponse($errors, $data);
    exit();
}

function sendResponse($errors, $results = '') {
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
            "data" => $results
        );
    }
    
    echo json_encode($response);
}




