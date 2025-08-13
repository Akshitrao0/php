<?php
session_start();
require_once "pdo.php";
if (!isset($_SESSION['name'])) {
    die('Not logged in');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: view.php");
        exit;
    }
    $make    = $_POST['make']    ?? '';
    $year    = $_POST['year']    ?? '';
    $mileage = $_POST['mileage'] ?? '';
    if (strlen($make) < 1) {
        $_SESSION['error'] = "Make is required";
        header("Location: add.php");
        exit;
    }
    if (!is_numeric($year) || !is_numeric($mileage)) {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
    $stmt->execute([
        ':mk' => $make,
        ':yr' => (int)$year,
        ':mi' => (int)$mileage,
    ]);
    $_SESSION['success'] = "Record inserted";
    header("Location: view.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
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
      
    }
  </style>
  <title>Akshit Rao — Add Automobile</title>
</head>
<body>
  <h1>Add A New Automobile</h1>
  <?php
  if (isset($_SESSION['error'])) {
      echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n";
      unset($_SESSION['error']);
  }
  ?>
  <form method="post">
    <p>
      <label for="make">Make</label>
      <input type="text" name="make" id="make">
    </p>
    <p>
      <label for="year">Year</label>
      <input type="text" name="year" id="year">
    </p>
    <p>
      <label for="mileage">Mileage</label>
      <input type="text" name="mileage" id="mileage">
    </p>
    <p>
      <input type="submit" value="Add">
      <input type="submit" name="cancel" value="Cancel">
    </p>
  </form>
</body>
</html>