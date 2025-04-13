<?php
//require_once "header.php";

$postFields = [
    "year" => 1994,
    "month" => 10,
    "date" => 19,
    "hours" => 8,
    "minutes" => 33,
    "seconds" => 0,
    "latitude" => 6.87587000,
    "longitude" => 79.86067390,
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
        'x-api-key: 9m1VW6ZDBt7eJoLfe7RsG7hSjnfDakzs4wDcSqqb'
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
        echo $zodiacSign;
    }
}

curl_close($curl);
