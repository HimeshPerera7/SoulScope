<?php
    $host = "localhost";
    $dbname = "soulscope_db";
    $username = "root";
    $password = "";
    
    /* DEFINE ASTROLOGY API KEY */
    const API_KEY = "9m1VW6ZDBt7eJoLfe7RsG7hSjnfDakzs4wDcSqqb";

    // ini_set('display_errors', '1');
    // ini_set('display_startup_errors', '1');
    // error_reporting(E_ALL);

    try {
        // Create a PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database Connection Failed: " . $e->getMessage());
    }

    //define("BASE_URL", "/soulscope/");

