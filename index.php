<?php
require "db.php";

/* Get total intake */
$stmt = $pdo->query("
    SELECT 
        COALESCE(SUM(weight), 0) AS total_weight,
        COALESCE(SUM(net_weight), 0) AS total_net_weight
    FROM paddy_intake
");

$stock = $stmt->fetch();

$total_weight = $stock["total_weight"];
$total_net_weight = $stock["total_net_weight"];

/* Opening stock */
$opening_stock = 10000;

/* Current stock */
$current_stock = $opening_stock + $total_net_weight;

/* Today's intake */
$stmt = $pdo->query("
    SELECT COALESCE(SUM(weight), 0)
    FROM paddy_intake
");

$today_intake = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GrainGate – Home</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<nav>
    <h2>🌾 GrainGate</h2>

    <div>
        <a href="index.php">Home</a>
        <a href="intake.php">Intake</a>
        <a href="stock.php">Stock</a>
        <a href="about.html">About</a>
    </div>
</nav>

<div class="container">

    <h1>Sri Murugan Rice Mill – Paddy Intake Register</h1>

    <div class="cards">

        <div class="card">
            Opening Stock
            <p>
                <?php echo number_format($opening_stock, 2); ?> kg
            </p>
        </div>

        <div class="card">
            Today's Intake
            <p>
                <?php echo number_format($today_intake, 2); ?> kg
            </p>
        </div>

        <div class="card">
            Current Stock
            <p>
                <?php echo number_format($current_stock, 2); ?> kg
            </p>
        </div>

    </div>

    <h3>Today's Rate Board</h3>

    <table>

        <tr>
            <th>Variety</th>
            <th>Rate (₹/kg)</th>
        </tr>

        <?php
        $stmt = $pdo->query("
            SELECT variety, rate_per_kg
            FROM rates
            ORDER BY id
        ");

        $rates = $stmt->fetchAll();

        foreach ($rates as $rate):
        ?>

        <tr>
            <td>
                <?php echo htmlspecialchars($rate["variety"]); ?>
            </td>

            <td>
                ₹<?php echo number_format($rate["rate_per_kg"], 2); ?>
            </td>
        </tr>

        <?php endforeach; ?>

    </table>

</div>

<footer>
    © 2026 GrainGate | Rice Mill Intake System
</footer>

</body>
</html>