<?php
// Start output buffering
ob_start();

require_once 'SVGGraph/autoloader.php';

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

function getSystemUptime() {
    $output = shell_exec("net stats srv");
    if (!$output) return "Unavailable";
    
    if (preg_match("/since (.+)/i", $output, $matches)) {
        return trim($matches[1]);
    }
    return "Could not determine uptime.";
}

function getCPUUsage() {
    $output = shell_exec("wmic cpu get loadpercentage");
    preg_match('/\d+/', $output, $matches);
    return isset($matches[0]) ? (int)$matches[0] : null;
}

function getMemoryInfo() {
    $total = shell_exec("wmic computersystem get totalphysicalmemory");
    $free = shell_exec("wmic os get freephysicalmemory");
    
    preg_match('/\d+/', $total, $t);
    preg_match('/\d+/', $free, $f);
    
    $totalBytes = $t[0] ?? 0;
    $freeKB = $f[0] ?? 0;
    
    return [
        'Total RAM' => formatBytes($totalBytes),
        'Free RAM' => formatBytes($freeKB * 1024),
        'Used Bytes' => $totalBytes - ($freeKB * 1024),
        'Free Bytes' => $freeKB * 1024,
        'Total Bytes' => $totalBytes
    ];
}

function getMysqlUptime() {
    $conn = new mysqli("localhost", "root", "", "mysql");
    if ($conn->connect_error) return ['formatted' => 'Unavailable', 'seconds' => 0];
    
    $result = $conn->query("SHOW STATUS LIKE 'Uptime'");
    if ($row = $result->fetch_assoc()) {
        $seconds = $row['Value'];
        return ['formatted' => gmdate("H:i:s", $seconds), 'seconds' => $seconds];
    }
    return ['formatted' => 'Unavailable', 'seconds' => 0];
}

$ram = getMemoryInfo();
$cpu = getCPUUsage();
$mysqlUptime = getMysqlUptime();

$diskTotal = disk_total_space("C:/");
$diskFree = disk_free_space("C:/");
$diskUsed = $diskTotal - $diskFree;

$metrics = [
    'Server Time'        => date('Y-m-d H:i:s'),
    'System Uptime'      => getSystemUptime(),
    'CPU Usage'          => $cpu !== null ? "$cpu%" : 'N/A',
    'Total RAM'          => $ram['Total RAM'],
    'Free RAM'           => $ram['Free RAM'],
    'Disk Free Space'    => formatBytes($diskFree),
    'Disk Total Space'   => formatBytes($diskTotal),
    'MySQL Uptime'       => $mysqlUptime['formatted'],
    'PHP Version'        => phpversion(),
    'Server Software'    => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
];

?>

<h2>Platform Performance</h2>
<br>
<p>This section displays system analytics and performance charts.</p>
<br>
<h3>Server Performance Dashboard</h3>
<br>
<table style="width:100%">
    <thead>
    <tr>
        <th>Metric</th>
        <th>Value</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($metrics as $key => $value): ?>
        <tr>
            <td><?= htmlspecialchars($key) ?></td>
            <td><?= htmlspecialchars($value) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3>Visual Performance Charts</h3>
<br>

<div class="charts">

    <!-- CPU Usage -->
    <div class="chart-container">
        <h3>CPU Usage</h3>
        <br>
        <?php
        $cpuGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $cpuGraph->colours(['#e74c3c', '#2ecc71']);
        $cpuGraph->values([
            "Used" => $cpu,
            "Free" => 100 - $cpu
        ]);
        $cpuGraph->render('PieGraph');
        ?>
    </div>

    <!-- RAM Usage -->
    <div class="chart-container">
        <h3>RAM Usage</h3>
        <br>
        <?php
        $ramGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $ramGraph->colours(['#fd7e14', '#28a745']);
        $ramGraph->values([
            "Used RAM" => $ram['Used Bytes'],
            "Free RAM" => $ram['Free Bytes']
        ]);
        $ramGraph->render('PieGraph');
        ?>
    </div>

    <!-- Disk Usage -->
    <div class="chart-container">
        <h3>Disk Usage</h3>
        <br>
        <?php
        $diskGraph = new Goat1000\SVGGraph\SVGGraph(300, 200);
        $diskGraph->colours(['#6f42c1', '#20c997']);
        $diskGraph->values([
            "Used" => $diskUsed,
            "Free" => $diskFree
        ]);
        $diskGraph->render('PieGraph');
        ?>
    </div>

</div>

<style>
    table {
        background: #fff;
        border-collapse: collapse;
        width: 80%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    th, td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background: #efefef;
    }
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