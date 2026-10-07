<?php
require "db.php";

$message = "";

/* DELETE RECORD */
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $stmt = $pdo->prepare("DELETE FROM paddy_intake WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: stock.php");
    exit;
}

/* GET ALL RECORDS */
$stmt = $pdo->query("SELECT * FROM paddy_intake ORDER BY id DESC");
$records = $stmt->fetchAll();

/* CALCULATE TOTALS */
$total_weight = 0;
$total_net_weight = 0;
$total_amount = 0;

foreach ($records as $record) {
    $total_weight += $record["weight"];
    $total_net_weight += $record["net_weight"];
    $total_amount += $record["amount"];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GrainGate – Stock</title>

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

    <h1>📦 Paddy Stock Records</h1>


    <!-- SUMMARY CARDS -->

    <div class="cards">

        <div class="card">

            Total Records

            <p>
                <?php echo count($records); ?>
            </p>

        </div>


        <div class="card">

            Total Weight

            <p>
                <?php echo number_format($total_weight, 2); ?> kg
            </p>

        </div>


        <div class="card">

            Net Weight

            <p>
                <?php echo number_format($total_net_weight, 2); ?> kg
            </p>

        </div>


        <div class="card">

            Total Amount

            <p>
                ₹<?php echo number_format($total_amount, 2); ?>
            </p>

        </div>

    </div>


    <h3>Intake Records</h3>


    <?php if (count($records) > 0): ?>


    <div style="overflow-x:auto;">

        <table>

            <tr>

                <th>ID</th>

                <th>Farmer</th>

                <th>Variety</th>

                <th>Weight (kg)</th>

                <th>Moisture (%)</th>

                <th>Deduction (kg)</th>

                <th>Net Weight (kg)</th>

                <th>Amount (₹)</th>

                <th>Action</th>

            </tr>


            <?php foreach ($records as $record): ?>

            <tr>

                <td>
                    <?php echo $record["id"]; ?>
                </td>


                <td>
                    <?php echo htmlspecialchars($record["farmer_name"]); ?>
                </td>


                <td>
                    <?php echo htmlspecialchars($record["variety"]); ?>
                </td>


                <td>
                    <?php echo number_format($record["weight"], 2); ?>
                </td>


                <td>
                    <?php echo number_format($record["moisture"], 2); ?>
                </td>


                <td>
                    <?php echo number_format($record["deduction"], 2); ?>
                </td>


                <td>
                    <?php echo number_format($record["net_weight"], 2); ?>
                </td>


                <td>
                    ₹<?php echo number_format($record["amount"], 2); ?>
                </td>


                <td>

                    <a
                        href="edit_intake.php?id=<?php echo $record["id"]; ?>"
                        class="edit-btn">
                        ✏️ Edit
                    </a>


                    <a
                        href="stock.php?delete=<?php echo $record["id"]; ?>"
                        class="delete-btn"
                        onclick="return confirm('Are you sure you want to delete this record?');">
                        🗑️ Delete
                    </a>

                </td>

            </tr>

            <?php endforeach; ?>

        </table>

    </div>


    <?php else: ?>

        <p>No intake records found.</p>

    <?php endif; ?>


</div>


<footer>

    © 2026 GrainGate | Rice Mill Intake System

</footer>


</body>

</html>