<?php 
session_start();
require_once __DIR__ . "/includes/config.php";

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
    $stmt = null; // Close the statement
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoulScope</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/registration.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/homepage.css">
    <link rel="stylesheet" href="css/inbox.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>

<body>
  <header>
    <div class="header-container">
      <a href="index.php">
        <img src="assets/images/logo/logo_name.png" alt="Logo" class="logo">
      </a>
      <nav>
          <?php if (isset($_SESSION["user_id"])) { ?>
                  <div class="loggedInMenu">
                      <div class="userAvatar">
                          <a href="user_dashboard.php">
                              <img src="<?=$profilePic;?>" alt="<?=$userFirstName . ' ' . $userFirstName ?>">
                          </a>
                      </div>
                      <div class="menuItems">
                          <ul class="menu">
                              <li class="menu-item dropdown">
                                  <a href="#">Hello  <?=$userFirstName?><span class="arrow">▼</span></a>
                                  <ul class="dropdown-menu">
                                      <li><a href="user_dashboard.php">Dashboard</a></li>
                                      <li><a href="inbox.php">Inbox</a></li>
                                      <li><a href="user_view_profile.php">My Profile</a></li>
                                      <li><a href="user_edit_profile.php">Edit Profile</a></li>
                                      <li><a href="includes/logout.php">Logout</a></li>
                                  </ul>
                              </li>
                          </ul>
                      </div>
                  </div>
          <?php } else { ?>
              <ul class="nav-links">
                  <li><a href="register.php">Register</a></li>
                  <li><a href="login.php">Login</a></li>
              </ul>
          <?php } ?>
      </nav>
    </div>
  </header>
