<?php
require_once __DIR__ . "/../../includes/config.php";

/* Get all users */
$users = [];
$locations = [];

try {
    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE deleted=0 ORDER BY userId ASC");
    $stmt->execute();
    
    // Fetch all users into users
    $originalUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($originalUsers as $originalUser) {
        $users[$originalUser['userId']] = $originalUser;
    }
    
} catch (Exception $e) {
    // Handle the error if needed
    echo "Error: " . $e->getMessage();
}

try {
    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT c.*, d.name_en AS district, p.name_en AS province FROM `cities` AS c LEFT JOIN districts as d ON c.district_id = d.id LEFT JOIN provinces AS p ON d.province_id = p.id ORDER BY id ASC");
    $stmt->execute();
    
    // Fetch all users into users
    $originalLocations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($originalLocations as $originalLocation) {
        $locations[$originalLocation['id']] = $originalLocation;
    }
    
} catch (Exception $e) {
    // Handle the error if needed
    echo "Error: " . $e->getMessage();
}

?>

<h2>View Registered Users</h2>
<br>

<?php if (count($users) > 0 ) { ?>
    
    <table style="width:100%">
        <thead>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Location</th>
            <th>Gender</th>
            <th>Date of Birth</th>
            <th>Zodiac Sign</th>
            <th>Profile Created At</th>
            <th>Role</th>
            <th></th>
        </thead>
        
        <?php foreach ($users as $user) { ?>
            <?php
            $ProfilePic = "../assets/images/avatar/" . $user['profilePic'];
            ?>
            <tr>
                <td><?= $user['userId']; ?></td>
                <td style="white-space: nowrap;">
                    <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                        <img class="profilePic" src="<?=$ProfilePic?>">
                        <a class="profileLink" href="http://localhost/soulscope/partner_view_profile.php?id=<?=(int) $user['userId']?>" target="_blank">
                            <?= $user['firstName'] . ' ' . $user['lastName']; ?>
                        </a>
                    </div>
                </td>
                <td style="white-space: nowrap;"><?= $user['email']; ?></td>
                <td>
                    <?php
                    if ($user['city'] > 0) { ?>
                        <p><?=strtoupper(htmlspecialchars($locations[$user['city']]['name_en']));?></p>
                        <small><?=strtoupper(htmlspecialchars($locations[$user['city']]['district']));?> DISTRICT</small>
                        <small><?=strtoupper(htmlspecialchars($locations[$user['city']]['province']));?> PROVINCE</small>
                    <?php } ?>
                </td>
                <td><?= strtoupper(htmlspecialchars($user['gender'])); ?></td>
                <td><?= date('Y-m-d', strtotime($user['birthDate'])); ?></td>
                <td><?=($user['zodiacSign']) ? strtoupper(htmlspecialchars($user['zodiacSign'])) : ''; ?></td>
                <td style="white-space: nowrap;"><?= date('Y-m-d h:i:s A', strtotime($user['createdAt'])); ?></td>
                <td><?= strtoupper(htmlspecialchars($user['role'])); ?></td>
                <td style="white-space: nowrap;"><a href="#" id="deleteLink" onclick="deleteProfile(<?=(int) $user['userId'];?>)">DELETE PROFILE</a></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<style>

    #deleteLink {
        display: block;
        text-decoration: none;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.8em;
        padding: 5px 10px;
        border-radius: 50px;
        background-color: #fd2a75;
        transition: all 0.3s;
        text-align: center;
    }

    #deleteLink:hover {
        background-color: #85002c;
    }

    .profileLink {
        /*text-decoration: none;*/
        font-size: 1em;
        line-height: 1em;
        color: #222222;
    }

    .profilePic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0;
        padding: 0;
        display: inline-block;
    }

    table {
        border-collapse: collapse;
        /*border-radius: 15px 15px 0 0 !important*/
    }

    table th {
        text-align: left;
        background-color: #efefef;
        padding: 10px;
        vertical-align: center;
    }

    table td {
        padding: 10px;
    }

    tr {
        background-color: #ffffff !important;
        transition: all 0.3s ease-in-out !important
    }

    tr:hover {
        background-color: #ececec !important
    }

</style>
