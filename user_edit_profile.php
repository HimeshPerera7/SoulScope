<?php require_once "header.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id']; // Get logged-in user ID

try {
    // Fetch user details from database including profilePic
    $sql = "SELECT u.*, c.id AS cityId, c.district_id, c.name_en, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':userId' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found!");
    }

    // Corrected profilePic path
    $profilePic = "assets/images/avatar/" . htmlspecialchars($user['profilePic']);
    
    /* Loading birth districts */
    $sql = "SELECT * FROM districts;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $districts = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}

?>

    <main class="dashboard-user">
        <!-- Top Bar -->
        <div class="dashboard-header">
            <h1>Edit Profile Details</h1>
            <ul class="dash-links">
                <li><a href="user_view_profile.php">Profile</a></li>
            </ul>
        </div>

        <!-- Profile -->
        <div class="profile-page">
            <div class="profile-container">
                <div class="profile-sidebar">
                    <!-- Edit Profile Picture -->
                    <form action="includes/update_profile_pic.php" method="POST" enctype="multipart/form-data">
                        <div class="profile-avatar">
                            <div class="view-profile-avatar">
                                <img src="<?php echo $profilePic; ?>" alt="Profile Avatar">
                            </div>
                            <input type="file" id="avatar-upload" name="avatar" accept="image/*" required>
                            <label for="avatar-upload" class="avatar-upload-btn">Change Photo</label>
                        </div>
                        <button type="submit" class="profile-save-btn">Upload</button>
                    </form>
                    <br>
                    <nav class="profile-nav">
                        <a href="#edit-profile" class="active">Edit Profile Details</a>
                        <a href="#change-password">Change Password</a>
                        <a href="#delete-account">Delete Account</a>
                    </nav>
                </div>
                
                <div class="profile-content">
                    <!-- Edit Profile Section -->
                    <div id="edit-profile" class="profile-section active">
                        <h2>Edit Profile</h2>
                        <form class="edit-profile-form" method="POST" action="includes/update_profile.php">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($userFirstName); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($userLastName); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Birth Date</label>
                                    <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($birthDate); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Birth Time</label>
                                    <input type="time" id="birth_time" name="birth_time" value="<?php echo htmlspecialchars($birthTime); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>District</label>
                                    <select id="district" name="district" required onchange="loadCities(this.value, <?=(int)$user['cityId'];?>)">
                                        <?php
                                        if (count($districts) > 0) {
                                            foreach ($districts as $district) {
                                                echo '<option value="' . (int)$district['id'] . '">' . htmlspecialchars($district['name_en']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Town/City</label>
                                    <select id="city" name="city" required>
                                        <option value="">Select Town/City</option>
                                    </select>
                                </div>
                            </div>


                            <button type="submit" class="profile-save-btn">Save Changes</button>
                        </form>
                    </div>

 
                    <!-- Change Password Section -->
                    <div id="change-password" class="profile-section">
                        <h2>Change Password</h2>
                        <form class="change-password-form" action="includes/update_password.php" method="POST">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <br>
                            <button type="submit" class="profile-save-btn">Update Password</button>
                        </form>
                    </div>

                    <!-- Delete Account Section -->
                    <div id="delete-account" class="profile-section">
                        <h2>Delete Account</h2>
                        <form class="delete-account-form" action="includes/delete_account.php" method="POST">
                            <h1>Are you sure you want to delete your account?</h1>
                            <h2>This action cannot be undone.</h2>
                            <p>Enter your password to confirm deletion:</p>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="profile-save-btn">Confirm Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.profile-nav a');
            const sections = document.querySelectorAll('.profile-section');

            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);

                    // Remove active classes
                    navLinks.forEach(l => l.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));

                    // Add active classes to clicked link and corresponding section
                    link.classList.add('active');
                    document.getElementById(targetId).classList.add('active');
                });
            });
        });
        
        
        $('#district').val(<?=(int)$user['district_id'];?>);
        $('#district').val(<?=(int)$user['district_id']; ?>)[0].onchange();
        
        function loadCities(districtId, cityId=0) {
            let district = parseInt(districtId);
            let cityz = parseInt(cityId);
            
            $.ajax({
                url: "api/city.php?id="+district,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let $select = $("#city");
                    // $select.empty();
                    $.each(data.data, function (index, city) {
                        $select.append($("<option>", {
                            value: city.id,
                            text: city.name_en
                        }));
                    });
                    if (cityz != 0) {
                        // console.log(cityz);
                        $('#city').val(cityz);
                    }
                },
                error: function () {
                    console.error("Error fetching cities data");
                }
            });
        }

        
    </script>

<?php require_once "footer.php"; ?>
