<?php
session_start();
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Sanitize & Validate Inputs
$firstName  = trim($_POST['first_name']);
$lastName   = trim($_POST['last_name']);
$birthDate  = trim($_POST['birth_date']);
$birthTime  = trim($_POST['birth_time']);
$city = $_POST["city"];


// Ensure required fields are not empty
if (empty($firstName) || empty($lastName) || empty($birthDate) || empty($birthTime)) {
    $_SESSION['error'] = "All fields are required!";
    header("Location: ../user_edit_profile.php");
    exit();
}

try {
    // Prepare and execute SQL Update query
    $sql = "UPDATE users SET 
                firstName = :firstName, 
                lastName = :lastName, 
                birthDate = :birthDate, 
                birthTime = :birthTime, 
                city = :city
            WHERE userId = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':firstName'  => $firstName,
        ':lastName'   => $lastName,
        ':birthDate'  => $birthDate,
        ':birthTime'  => $birthTime,
        ':city' => $city,
        ':userId'     => $userId
    ]);
    
    /* Updating Zodiac Sign according to birth city */
    $sqlNewUser = "SELECT u.*, c.name_en, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId AND deleted = 0";
    $stmtNewUser = $pdo->prepare($sqlNewUser);
    $stmtNewUser->execute([':userId' => $userId]);
    $newUser = $stmtNewUser->fetch(PDO::FETCH_ASSOC);
    
    $year = date_format(date_create($newUser['birthDate']),"Y");
    $month = date_format(date_create($newUser['birthDate']),"n");
    $date = date_format(date_create($newUser['birthDate']),"j");
    $hours = date_format(date_create($newUser['birthTime']),"G");
    $minutes = date_format(date_create($newUser['birthTime']),"i");
    $latitude = $newUser['latitude'];
    $longitude = $newUser['longitude'];
    
    $postFields = [
        "year" => (int)$year,
        "month" => (int)$month,
        "date" => (int)$date,
        "hours" => (int)$hours,
        "minutes" => (int)$minutes,
        "seconds" => 0,
        "latitude" => (float)$latitude,
        "longitude" => (float)$longitude,
        "timezone" => 5.5,
        "settings" => [
            "observation_point" => "topocentric",
            "ayanamsha" => "lahiri",
            "language" => "en"
        ]
    ];
    
    /* Preparing Astrology API request */
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://json.apiastro.com/planets/extended',
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
        if (isset($astroData["output"]['Ascendant']['zodiac_sign_name'])) {
            $zodiacSign = $astroData["output"]['Ascendant']['zodiac_sign_name'];
            $updateSql = "UPDATE users SET zodiacSign = :zodiacSign WHERE userId = :userId";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                ':zodiacSign' => $zodiacSign,
                ':userId'     => $userId
            ]);
        }
    }
    curl_close($curl);

    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: ../user_view_profile.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Something went wrong: " . $e->getMessage();
    header("Location: ../user_edit_profile.php");
    exit();
}
