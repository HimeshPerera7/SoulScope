<?php
require_once __DIR__ . "/../../includes/config.php";

/* Get all user reports */
$reports = [];
$users = [];

try {
    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT * FROM profile_reports WHERE deleted=0 ORDER BY id ASC");
    $stmt->execute();
    
    // Fetch all results into $reports
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Handle the error if needed
    echo "Error: " . $e->getMessage();
}

try {
    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT userId,firstName,lastName,profilePic,deleted FROM users ORDER BY userId ASC");
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

?>

<h2>User Reported Profiles</h2>
<br>

<?php if (count($reports) > 0 ) { ?>
<table style="width:100%">
    <thead>
        <th>Report ID</th>
        <th>Reported User</th>
        <th>Profile</th>
        <th>Reason</th>
        <th>Reported Date</th>
        <th>Actions</th>
    </thead>
    <?php foreach ($reports as $report) { ?>
        <?php
            $reportedProfilePic = "../assets/images/avatar/" . $users[$report['reported_user']]['profilePic'];
            $ProfilePic = "../assets/images/avatar/" . $users[$report['profile']]['profilePic'];
        ?>
        <tr class="<?=( (int) $users[$report['profile']]['deleted'] == 1) ? 'deletedUser' : ''?>">
            <td><?= $report['id']; ?></td>
            <td>
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                    <img class="profilePic" src="<?=$reportedProfilePic?>">
                    <?= $users[$report['reported_user']]['firstName'] . ' ' .  $users[$report['reported_user']]['lastName']; ?>
                </div>
            </td>
            <td>
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                    <img class="profilePic" src="<?=$ProfilePic?>">
                    <a class="profileLink" href="http://localhost/soulscope/partner_view_profile.php?id=<?=(int) $report['profile']?>" target="_blank">
                        <?= $users[$report['profile']]['firstName'] . ' ' .  $users[$report['profile']]['lastName']; ?>
                    </a>
                </div>
            </td>
            <td><?= $report['reason']; ?></td>
            <td><?=date('Y-m-d h:i:s A', strtotime($report['reported_date'])); ?></td>
            <td><?= ( (int) $users[$report['profile']]['deleted'] == 1) ? 'PROFILE DELETED' : ''?></td>
        </tr>
    <?php } ?>
</table>
<?php } ?>

<style>
    
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
        background-color: #ffffff;
        transition: all 0.3s ease-in-out;
    }
    
    tr:hover {
        background-color: #ececec;
    }
    
    .deletedUser {
        background-color: #ffeef8 !important;
        border-left: 5px solid #d10000 !important;
    }
    
</style>

