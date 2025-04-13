<?php
// Start output buffering
ob_start();

require_once __DIR__ . "/../../includes/config.php";
require_once 'SVGGraph/autoloader.php';

// Gender Pie Chart
$genderQuery = "SELECT gender, COUNT(*) AS count FROM users GROUP BY gender";
$genderStmt = $pdo->query($genderQuery);
$genderData = $genderStmt->fetchAll();

// Districts Pie Chart (Top 10)
$districtQuery = "
    SELECT d.name_en, COUNT(u.userId) AS count
    FROM users u
    JOIN cities c ON u.city = c.id      -- Join users to cities
    JOIN districts d ON c.district_id = d.id   -- Join cities to districts
    GROUP BY d.name_en
    ORDER BY count DESC
    LIMIT 10
";
$districtStmt = $pdo->query($districtQuery);
$districtData = $districtStmt->fetchAll();

// Provinces Pie Chart
$provinceQuery = "
    SELECT p.name_en, COUNT(u.userId) AS count
    FROM users u
    JOIN cities c ON u.city = c.id       -- Join users to cities
    JOIN districts d ON c.district_id = d.id  -- Join cities to districts
    JOIN provinces p ON d.province_id = p.id  -- Join districts to provinces
    GROUP BY p.name_en
";
$provinceStmt = $pdo->query($provinceQuery);
$provinceData = $provinceStmt->fetchAll();

// Age Group Pie Chart
$ageQuery = "
    SELECT
        CASE
            WHEN TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) < 20 THEN 'Below 20'
            WHEN TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) BETWEEN 20 AND 29 THEN '20-30'
            WHEN TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) BETWEEN 30 AND 39 THEN '30-40'
            WHEN TIMESTAMPDIFF(YEAR, birthDate, CURDATE()) BETWEEN 40 AND 49 THEN '40-50'
            ELSE 'Over 50'
        END AS age_group,
        COUNT(*) AS count
    FROM users
    GROUP BY age_group
";
$ageStmt = $pdo->query($ageQuery);
$ageData = $ageStmt->fetchAll();

?>

<h2>Overview</h2>
<br>
<p>This section displays an overview of all activities.</p>
<br>

<div class="charts">

    <!-- Gender Pie Chart -->
    <div class="chart-container">
        <h3>Gender Distribution</h3>
        <br>
        <?php
        $genderGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $genderGraph->colours(['#3498db', '#e74c3c']);
        $genderGraph->values(array_column($genderData, 'count', 'gender'));
        $genderGraph->render('PieGraph');
        ?>
    </div>

    <!-- Districts Pie Chart -->
    <div class="chart-container">
        <h3>Top 10 Districts</h3>
        <br>
        <?php
        $districtGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $districtGraph->colours(['#9b59b6', '#1abc9c']);
        $districtGraph->values(array_column($districtData, 'count', 'name_en'));
        $districtGraph->render('PieGraph');
        ?>
    </div>

    <!-- Provinces Pie Chart -->
    <div class="chart-container">
        <h3>Provinces Distribution</h3>
        <br>
        <?php
        $provinceGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $provinceGraph->colours(['#f39c12', '#2ecc71']);
        $provinceGraph->values(array_column($provinceData, 'count', 'name_en'));
        $provinceGraph->render('PieGraph');
        ?>
    </div>

    <!-- Age Group Pie Chart -->
    <div class="chart-container">
        <h3>Age Group Distribution</h3>
        <br>
        <?php
        $ageGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $ageGraph->colours(['#f1c40f', '#e67e22', '#16a085', '#2980b9', '#8e44ad']);
        $ageGraph->values(array_column($ageData, 'count', 'age_group'));
        $ageGraph->render('PieGraph');
        ?>
    </div>

</div>

    <style>
        .charts {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            /*width: calc(100vw/4);*/
        }
    </style>

<?php
// End output buffering and flush output
ob_end_flush();