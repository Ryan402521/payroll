<?php include 'includes/header.php'; ?>
<div class="main-content">
    <h2>Add New Position</h2>

    <?php
    require 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $rate = $_POST['rate'];

        $stmt = $pdo->prepare("INSERT INTO positions (title, rate_per_hour) VALUES (?, ?)");
        $stmt->execute([$title, $rate]);
        echo "<p style='color: green;'>✅ Position added successfully!</p>";
    }
    ?>

    <form method="post" style="max-width: 400px;">
        <label>Title:</label>
        <input type="text" name="title" required><br><br>

        <label>Rate per Hour:</label>
        <input type="number" step="0.01" name="rate" required><br><br>

        <button type="submit">➕ Add Position</button>
    </form>

    <p><a href="positions.php">⬅ Back to Positions</a></p>
</div>
<?php include 'includes/footer.php'; ?>
