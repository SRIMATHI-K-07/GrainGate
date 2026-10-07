<?php
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $farmer_name = $_POST["farmer_name"];
    $variety = $_POST["variety"];
    $weight = $_POST["weight"];
    $moisture = $_POST["moisture"];

    // Calculate deduction
    $deduction = ($weight * $moisture) / 100;

    // Calculate net weight
    $net_weight = $weight - $deduction;

    // Get rate from database
    $stmt = $pdo->prepare("SELECT rate_per_kg FROM rates WHERE variety = ?");
    $stmt->execute([$variety]);
    $rate = $stmt->fetchColumn();

    if ($rate !== false) {

        // Calculate amount
        $amount = $net_weight * $rate;

        // Insert into database
        $stmt = $pdo->prepare("
            INSERT INTO paddy_intake
            (farmer_name, variety, weight, moisture, deduction, net_weight, amount)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $farmer_name,
            $variety,
            $weight,
            $moisture,
            $deduction,
            $net_weight,
            $amount
        ]);

        $message = "✅ Intake saved successfully! Payment: ₹" . number_format($amount, 2);

    } else {
        $message = "❌ Rate not found for this variety.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GrainGate – Intake</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<nav>
    <h2>🌾 GrainGate</h2>

    <div>
        <a href="index.html">Home</a>
        <a href="intake.php">Intake</a>
        <a href="stock.html">Stock</a>
        <a href="about.html">About</a>
    </div>
</nav>

<div class="container">

    <h1>Paddy Intake Entry</h1>

    <?php if ($message != ""): ?>
        <div class="result">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Farmer Name</label>
        <input
            type="text"
            name="farmer_name"
            placeholder="Enter farmer name"
            required
        >

        <label>Variety</label>
        <select name="variety" required>
            <option value="">Select Variety</option>
            <option value="Ponni">Ponni</option>
            <option value="ADT-37">ADT-37</option>
            <option value="CO-51">CO-51</option>
        </select>

        <label>Weight (kg)</label>
        <input
            type="number"
            name="weight"
            step="0.01"
            placeholder="Enter weight"
            required
        >

        <label>Moisture (%)</label>
        <input
            type="number"
            name="moisture"
            step="0.01"
            placeholder="Enter moisture percentage"
            required
        >

        <button type="submit">Save Intake</button>

    </form>

</div>

<footer>
    © 2026 GrainGate | Rice Mill Intake System
</footer>

</body>
</html>