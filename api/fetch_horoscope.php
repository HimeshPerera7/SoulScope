<?php

$errors = array();
$result = array();

require_once '../includes/config.php';  // Database connection

$currentUserId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($currentUserId == 0) {
    $errors[] = "Invalid user selected";
    sendResponse($errors, $results);
    exit();
}

// Fetch user's details
$query = "SELECT u.*, c.name_en, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":userId", $currentUserId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$year = date_format(date_create($user['birthDate']),"Y");
$month = date_format(date_create($user['birthDate']),"n");
$date = date_format(date_create($user['birthDate']),"j");
$hours = date_format(date_create($user['birthTime']),"G");
$minutes = date_format(date_create($user['birthTime']),"i");
$latitude = $user['latitude'];
$longitude = $user['longitude'];

/* Add user's horoscope to database 'horoscope_images' table using astrology API */
$postFieldsNew = [
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
    ],
    "chart_config" =>  [
        "font_family"=>"Mallanna",
        "hide_time_location"=>"False",
        "hide_outer_planets"=>"False",
        "chart_style"=>"south_india",
        "native_name"=>$user['firstName'] . ' ' . $user['lastName'],
        "native_name_font_size"=>"20px",
        "native_details_font_size"=>"15px",
        "chart_border_width"=>1,
        "planet_name_font_size"=>"20px",
        "chart_heading_font_size"=>"25px",
        "chart_background_color"=>"#FEE1C7",
        "chart_border_color"=>"#B5A886",
        "native_details_font_color"=>"#000",
        "native_name_font_color"=>"#231F20",
        "planet_name_font_color"=>"#BC412B",
        "chart_heading_font_color"=>"#2D3319"
    ]
];

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://json.freeastrologyapi.com/horoscope-chart-svg-code',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($postFieldsNew),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'x-api-key:' . API_KEY
    ),
));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
$response2 = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'Curl error: ' . curl_error($curl);
} else {
    $horImage = json_decode($response2, true);
    if (isset($horImage)) {
        $results = $horImage["output"];
    }
}
curl_close($curl);

sendResponse($errors, $results);

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




