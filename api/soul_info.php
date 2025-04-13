<?php
session_start();
require_once "../includes/config.php";
$errors = array();
$data = array();

/* Get data from URL */
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lat = isset($_GET['lat']) ? (float)$_GET['lat'] : 0;
$long = isset($_GET['long']) ? (float)$_GET['long'] : 0;
$date = isset($_GET['date']) ? (string)$_GET['date'] : '';

if ($userId == 0) {
    $errors[] = "Invalid district selected";
}

if ($lat == 0) {
    $errors[] = "Invalid latitude given";
}

if ($long == 0) {
    $errors[] = "Invalid longitude given";
}

if (strlen($date) == 0) {
    $errors[] = "Invalid date given";
}

// Initialize variables
$userFirstName = $userLastName = $birthDate = $birthTime = $birthPlace = $zodiacSign = "";

// Fetch user details if logged in
if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];
    
    // Use PDO to fetch user details
    $query = "SELECT u.*, c.name_en, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $userFirstName = htmlspecialchars($user['firstName']);
        $userLastName  = htmlspecialchars($user['lastName']);
        $birthDate     = htmlspecialchars($user['birthDate']);
        $birthTime     = htmlspecialchars($user['birthTime']);
        $birthPlace    = htmlspecialchars($user['name_en']);
        $zodiacSign    = htmlspecialchars($user['zodiacSign']);
        $profilePic = "assets/images/avatar/" . htmlspecialchars($user['profilePic']);
        $zodiacPic = "assets/images/zodiac_signs/" . strtolower(htmlspecialchars($zodiacSign)). ".png";
    }
    $stmt = null;
    
    /* Get rahu kalaya from Astrology API */
    
    /* Current date and user location for retrieving current sunrise/sunset and rahu kalaya */
    $currentYear = date_format(date_create($date),"Y");
    $currentMonth = date_format(date_create($date),"n");
    $currentDate = date_format(date_create($date),"j");

    $postFields = [
        "year" => (int)$currentYear,
        "month" => (int)$currentMonth,
        "date" => (int)$currentDate,
        "hours" => 0,
        "minutes" => 0,
        "seconds" => 0,
        "latitude" => (float)$lat,
        "longitude" => (float)$long,
        "timezone" => 5.5,
        "settings" => [
            "observation_point" => "topocentric",
            "ayanamsha" => "lahiri"
        ]
    ];

    /* Preparing Astrology API request */
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://json.freeastrologyapi.com/good-bad-times',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postFields),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'x-api-key:' . API_KEY
        ),
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
    } else {
        $astroData = json_decode($response, true);
        if (isset($astroData["rahu_kaalam_data"])) {
            $data['rahu_kalaya'] = json_decode($astroData["rahu_kaalam_data"]);
        }
    }
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://json.freeastrologyapi.com/getsunriseandset',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postFields),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'x-api-key:' . API_KEY
        ),
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($curl);
    
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
    } else {
        $astroData = json_decode($response, true);
        if (isset($astroData)) {
            $data['sun'] = array(
                "sunrise" => $astroData['output']['sun_rise_time'],
                "sunset" => $astroData['output']['sun_set_time'],
            );
        }
    }
    
    curl_close($curl);
    
} else {
    $errors[] = "User not found";
}

// Set HTTP headers
header("Content-Type: application/json");
//header("Access-Control-Allow-Origin: same-origin");
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
        "data" => $data
    );
}

echo json_encode($response);

