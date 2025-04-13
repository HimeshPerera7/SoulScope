<?php
session_start();
require_once "config.php"; // Include database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION["errors"] = []; // Initialize errors array

    // Collect form data
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["c_password"];
    $gender = $_POST["gender"];
    $birthDate = $_POST["birth_date"];
    $birthTime = $_POST["birth_time"];
    $city = $_POST["city"];

    // Validate input fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION["errors"][] = "All fields are required.";
    }

    if ($password !== $confirmPassword) {
        $_SESSION["errors"][] = "Passwords do not match.";
    }

    // Check if email is already registered
    $checkEmail = $pdo->prepare("SELECT email FROM users WHERE email = :email");
    $checkEmail->bindParam(":email", $email);
    $checkEmail->execute();
    if ($checkEmail->rowCount() > 0) {
        $_SESSION["errors"][] = "Email is already registered.";
    }

    // If there are errors, redirect back to the form
    if (!empty($_SESSION["errors"])) {
        header("Location: ../register.php");
        exit();
    }

    // Assign default avatar based on gender
    $profilePic = ($gender === "Female") ? "femaleAvatar.png" : "maleAvatar.png";

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Prepare SQL statement
        $sql = "INSERT INTO users (firstName, lastName, email, password, gender, birthDate, birthTime, city, profilePic)
                VALUES (:firstName, :lastName, :email, :password, :gender, :birthDate, :birthTime, :city, :profilePic)";

        $stmt = $pdo->prepare($sql);

        // Bind values
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":lastName", $lastName);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":birthDate", $birthDate);
        $stmt->bindParam(":birthTime", $birthTime);
        $stmt->bindParam(":city", $city);
        $stmt->bindParam(":profilePic", $profilePic);

        // Execute query
        if ($stmt->execute()) {
            $_SESSION["success"] = "Registration successful!";
            
            /* Update user's zodiac sign using astrology API */
            $newUserId = (int)$pdo->lastInsertId();
            $sqlNewUser = "SELECT u.*, c.name_en, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId AND deleted = 0";
            $stmtNewUser = $pdo->prepare($sqlNewUser);
            $stmtNewUser->execute([':userId' => $newUserId]);
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
                        ':userId'     => $newUserId
                    ]);
                }
            }
            curl_close($curl);
            
            header("Location: ../login.php"); // Redirect to login page
            exit();
        } else {
            $_SESSION["errors"][] = "Could not complete registration.";
            header("Location: ../register.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION["errors"][] = "Database error: " . $e->getMessage();
        header("Location: ../register.php");
        exit();
    }
} else {
    $_SESSION["errors"][] = "Invalid request.";
    header("Location: ../register.php");
    exit();
}
