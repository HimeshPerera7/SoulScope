<?php
session_start();
require_once __DIR__ . "/../includes/config.php";

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}
$userId = $_SESSION['user_id']; // Get logged-in user ID

try {
    // Fetch user details from database including profilePic
    $query = "SELECT u.*, c.name_en AS cityName, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found!");
    }

    // Format age from birthDate
    $birthDate = new DateTime($user['birthDate']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;

    // Get profile picture
    $profilePic = "../assets/images/avatar/" . htmlspecialchars($user['profilePic']);

} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin - SoulScope</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="stylesheet" href="css/dashboard.css"/>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>
<body>
    <header>
        <div class="header-container">
            <img src="../assets/images/logo/logo_name.png" alt="Logo" class="logo">
            <nav>
                    <ul class="nav-links">
                        <li><a href="admin_dashboard.php">Dashboard</a></li>
                        <li><a href="../includes/logout.php">Logout</a></li>
                    </ul>
            </nav>
        </div>
    </header>
    <main class="dashboard-user">
        <!-- Top Bar -->
        <div class="dashboard-header">
            <h1><?php echo htmlspecialchars($user['firstName']); ?>'s Profile</h1>
            <ul class="dash-links">
                <li><a href="admin_edit_profile.php">Edit Profile</a></li>
            </ul>
        </div>

        <!-- Profile -->
        <div class="view-profile">
            <div class="view-profile-container">
                <div class="view-profile-sidebar">
                    <div class="view-profile-avatar">
                        <img src="<?php echo $profilePic; ?>" alt="Profile Avatar">
                    </div>
                </div>

                <div class="view-profile-content">
                    <!-- View Profile Section -->
                    <div class="view-profile-section">
                        <h2>Profile Details</h2>
                        <br>
                        <div class="view-profile-details">
                            <div class="detail-row">
                                <span class="detail-label">Full Name</span>
                                <span class="detail-value"><?php echo htmlspecialchars($user['firstName'] . " " . $user['lastName']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Age</span>
                                <span class="detail-value"><?php echo $age; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Zodiac Sign</span>
                                <span class="detail-value"><?php echo htmlspecialchars($user['zodiacSign']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Location</span>
                                <span class="detail-value"><?=($user['cityName'] != null) ? htmlspecialchars($user['cityName']) : ''; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email</span>
                                <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </main>

</body>
</html>
