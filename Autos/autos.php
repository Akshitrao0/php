<?php
require_once "pdo.php";

if (! isset($_GET['name'])) {
    die("Name parameter missing");
}
$name = htmlentities($_GET['name']);
$msg = "";

if (isset($_POST['logout'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['make'], $_POST['year'], $_POST['mileage'])) {
    if (strlen($_POST['make']) < 1) {
        $msg = "Make is required";
    } elseif (! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])) {
        $msg = "Mileage and year must be numeric";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO autos (make, year, mileage)
             VALUES (:mk, :yr, :mi)"
        );
        $stmt->execute([
            ":mk" => $_POST['make'],
            ":yr" => $_POST['year'],
            ":mi" => $_POST['mileage']
        ]);
        $msg = "Record inserted";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your Name – Autos Database</title>
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
</head>
<body>
  <h1>Tracking Autos for <?= $name ?></h1>

  <?php if ($msg !== ""): ?>
    <?php $color = ($msg === "Record inserted") ? "green" : "red"; ?>
    <p style="color:<?= $color ?>"><?= htmlentities($msg) ?></p>
  <?php endif; ?>

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
      <input type="submit" name="logout" value="Logout">
    </p>
  </form>

  <h2>Automobiles</h2>
  <ul>
  <?php
    $stmt = $pdo->query("SELECT year, make, mileage FROM autos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>"
           . htmlentities($row['year']) . " "
           . htmlentities($row['make']) . " / "
           . htmlentities($row['mileage'])
           . "</li>\n";
    }
  ?>
  </ul>
</body>
</html>