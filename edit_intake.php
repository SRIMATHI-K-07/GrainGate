<?php
require "db.php";

$id = $_GET["id"] ?? null;

if (!$id) {
    die("Invalid record ID.");
}

/* Get existing record */
$stmt = $pdo->prepare("SELECT * FROM paddy_intake WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    die("Record not found.");
}

$message = "";

/* Update record */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $farmer_name = $_POST["farmer_name"];
    $variety = $_POST["variety"];
    $weight = $_POST["weight"];
    $moisture = $_POST["moisture"];

    // Recalculate deduction
    $deduction = ($weight * $moisture) / 100;

    // Recalculate net weight
    $net_weight = $weight - $deduction;

    // Get current rate
    $stmt = $pdo->prepare(
        "SELECT rate_per_kg FROM rates WHERE variety = ?"
    );

    $stmt->execute([$variety]);

    $rate = $stmt->fetchColumn();

    if ($rate !== false) {

        // Recalculate amount
        $amount = $net_weight * $rate;

        // Update database
        $stmt = $pdo->prepare("
            UPDATE paddy_intake
            SET
                farmer_name = ?,
                variety = ?,
                weight = ?,
                moisture = ?,
                deduction = ?,
                net_weight = ?,
                amount = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $farmer_name,
            $variety,
            $weight,
            $moisture,
            $deduction,
            $net_weight,
            $amount,
            $id
        ]);

        header("Location: stock.php");
        exit;

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

    <title>GrainGate – Edit Intake</title>

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

    <h1>✏️ Edit Paddy Intake</h1>

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
            value="<?php echo htmlspecialchars($record["farmer_name"]); ?>"
            required
        >


        <label>Variety</label>

        <select name="variety" required>

            <option value="Ponni"
                <?php if ($record["variety"] == "Ponni") echo "selected"; ?>>
                Ponni
            </option>

            <option value="ADT-37"
                <?php if ($record["variety"] == "ADT-37") echo "selected"; ?>>
                ADT-37
            </option>

            <option value="CO-51"
                <?php if ($record["variety"] == "CO-51") echo "selected"; ?>>
                CO-51
            </option>

        </select>


        <label>Weight (kg)</label>

        <input
            type="number"
            name="weight"
            step="0.01"
            value="<?php echo $record["weight"]; ?>"
            required
        >


        <label>Moisture (%)</label>

        <input
            type="number"
            name="moisture"
            step="0.01"
            value="<?php echo $record["moisture"]; ?>"
            required
        >


        <button type="submit">
            Update Intake
        </button>

    </form>

</div>


<footer>

    © 2026 GrainGate | Rice Mill Intake System

</footer>


</body>

</html>