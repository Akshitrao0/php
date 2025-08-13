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

    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?autos_id=" . urlencode($autos_id));
        exit;
    }
    if (!is_numeric($_POST['year'])) {
        $_SESSION['error'] = "Year must be an integer";
        header("Location: edit.php?autos_id=" . urlencode($autos_id));
        exit;
    }
    if (!is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage must be an integer";
        header("Location: edit.php?autos_id=" . urlencode($autos_id));
        exit;
    }

    $stmt = $pdo->prepare("UPDATE autos1 SET make = :mk, model = :md, year = :yr, mileage = :mi 
                            WHERE autos_id = :id");
    $stmt->execute([
        ':mk' => $_POST['make'],
        ':md' => $_POST['model'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage'],
        ':id' => $autos_id
    ]);
    $_SESSION['success'] = "Record edited";
    header("Location: index.php");
    exit;
}

// Load existing row to populate form
$stmt = $pdo->prepare("SELECT * FROM autos1 WHERE autos_id = :id");
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
    form label {
      display: inline-block;
      width: 80px;
      margin-right: 10px;
      font-weight: bold;
    }
    form p {
      margin: 5px 0;
      margin-left: 5px;
      display: block;
    }
</style>
<title>Akshit Rao — Edit Automobile</title>
</head>
<body>
<h1>Edit Automobile</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>Make: <input type="text" name="make" value="<?= htmlentities($row['make']) ?>"></p>
<p>Model: <input type="text" name="model" value="<?= htmlentities($row['model']) ?>"></p>
<p>Year: <input type="text" name="year" value="<?= htmlentities($row['year']) ?>"></p>
<p>Mileage: <input type="text" name="mileage" value="<?= htmlentities($row['mileage']) ?>"></p>
<input type="hidden" name="autos_id" value="<?= htmlentities($row['autos_id']) ?>">
<p>
    <input type="submit" value="Save">
    <input type="submit" name="cancel" value="Cancel">
</p>
</form>
</body>
</html>