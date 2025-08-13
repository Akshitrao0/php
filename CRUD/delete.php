<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Get the autos_id from GET or POST
if (!isset($_GET['autos_id']) && !isset($_POST['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header("Location: index.php");
    exit;
}
$autos_id = $_GET['autos_id'] ?? $_POST['autos_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM autos1 WHERE autos_id = :id");
        $stmt->execute([':id' => $autos_id]);
        $_SESSION['success'] = "Record deleted";
        header("Location: index.php");
        exit;
    }
}

// Load the record to show confirmation
$stmt = $pdo->prepare("SELECT make, model, year, mileage FROM autos1 WHERE autos_id = :id");
$stmt->execute([':id' => $autos_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = "Bad value for autos_id";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    *{
        font-family:Arial, Helvetica, sans-serif;
    }
</style>
<title>Akshit Rao — Delete Automobile</title>
</head>
<body>
<h1>Confirm Delete</h1>
<p>Make: <?= htmlentities($row['make']) ?></p>
<p>Model: <?= htmlentities($row['model']) ?></p>
<p>Year: <?= htmlentities($row['year']) ?></p>
<p>Mileage: <?= htmlentities($row['mileage']) ?></p>
<form method="post">
    <input type="hidden" name="autos_id" value="<?= htmlentities($autos_id) ?>">
    <input type="submit" name="delete" value="Delete">
    <input type="submit" name="cancel" value="Cancel">
</form>
</body>
</html>
