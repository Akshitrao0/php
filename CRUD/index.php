<?php
session_start();
require_once "pdo.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Akshit Rao — Automobile Database</title>
</head>
<body>
<h1>Welcome to the Automobiles Database</h1>
<?php
if (!isset($_SESSION['name'])) {
    echo '<p><a href="login.php">Please log in</a></p>';
    exit;
}

if (isset($_SESSION['success'])) {
    echo '<p style="color:green;">'.htmlentities($_SESSION['success'])."</p>\n";
    unset($_SESSION['success']);
}

$stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos1");
$rows = $stmt->fetchAll();

if (count($rows) > 0) {
    echo '<table border="1"><tr>
            <th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th>
          </tr>';
    foreach ($rows as $r) {
        echo '<tr><td>'.htmlentities($r['make']).'</td>
              <td>'.htmlentities($r['model']).'</td>
              <td>'.htmlentities($r['year']).'</td>
              <td>'.htmlentities($r['mileage']).'</td>
              <td><a href="edit.php?autos_id='.$r['autos_id'].'">Edit</a> / 
                  <a href="delete.php?autos_id='.$r['autos_id'].'">Delete</a></td></tr>';
    }
    echo '</table>';
} else {
    echo '<p>No rows found</p>';
}
?>
<p><a href="add.php">Add New Entry</a> | <a href="logout.php">Logout</a></p>
</body>
</html>