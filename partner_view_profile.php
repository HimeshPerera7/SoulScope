<?php 
require_once "header.php"; 

/* Only logged-in users can view other's profiles */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* Get User ID from query*/
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($userId == 0) {
    header("Location: login.php");
    exit();
}

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
    $profilePic = "assets/images/avatar/" . htmlspecialchars($user['profilePic']);

} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}
?>

    <main class="dashboard-user">
        <!-- Top Bar -->
        <div class="dashboard-header">
            <h1><?php echo htmlspecialchars($user['firstName']); ?>'s Profile</h1>
            <ul class="dash-links">
                <li><a href="inbox.php?receiver=<?=(int) $user['userId']?>">Message</a></li>
                <li><a href="#" id="reportLink" onclick="reportProfile()">Report</a></li>
            </ul>
        </div>
        
        <!-- Report message -->
        <div id="infoCover" style="display: none">
            <div id="infoBox">
                Profile has been reported successfully..!
            </div>
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
    
    <style>
        
        #infoCover {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            background-color: rgba(0,0,0,0.7);
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        #infoBox {
            box-sizing: border-box;
            background-color: #ffffff;
            padding: 25px 50px;
            border-top: 25px solid #E91E63;
            border-radius: 5px;
            box-shadow: 0 0 5px 5px rgba(0,0,0,0.2);
        }
    </style>

    <script>
        
        $(document).ready(function() {
            $('#reportLink').click(function(e) {
                e.preventDefault(); // Prevent link behavior
            });
        });
        
        function reportProfile() {
            $(document).ready(function() {

                var reason = prompt("Are you sure you want to report this profile?", "Type the Reason");
                if (reason) {
                    var profileId = <?=(int) $user['userId']?>;
                    var reportUrl = "api/report.php?profile=" + profileId + "&reason=" + reason;

                    $.getJSON(reportUrl, function(response) {
                        if (response.success) {
                            $('#infoCover').fadeIn().delay(2500).fadeOut();
                        } else {
                            alert("Something went wrong. Try again.");
                        }
                    }).fail(function() {
                        alert("Error connecting to server.");
                    });
                }
            });
        }

        
    </script>

<?php require_once "footer.php"; ?>
