<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

$stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY auto_id DESC");
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Akshit Rao — Autos Database</title>
</head>
<body>
  <h1>Tracking Autos for <?= htmlentities($_SESSION['name']) ?></h1>

  <?php
  if (isset($_SESSION['success'])) {
      echo '<p style="color:green;">' . htmlentities($_SESSION['success']) . "</p>\n";
      unset($_SESSION['success']);
  }
  ?>

  <?php if (count($rows) > 0): ?>
    <ul>
      <?php foreach ($rows as $r): ?>
        <li>
          <?= htmlentities($r['year']) ?> <?= htmlentities($r['make']) ?> /
          Mileage: <?= htmlentities($r['mileage']) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No rows found</p>
  <?php endif; ?>

  <p>
    <a href="add.php">Add New</a> |
    <a href="logout.php">Logout</a>
  </p>
</body>
</html>