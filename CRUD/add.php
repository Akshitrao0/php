<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        exit;
    }
    if (!is_numeric($_POST['year'])) {
        $_SESSION['error'] = "Year must be an integer";
        header("Location: add.php");
        exit;
    }
    if (!is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage must be an integer";
        header("Location: add.php");
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO autos1 (make, model, year, mileage) VALUES (:mk, :md, :yr, :mi)");
    $stmt->execute([
        ':mk' => $_POST['make'],
        ':md' => $_POST['model'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage']
    ]);
    $_SESSION['success'] = "Record added";
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
<title>Akshit Rao — Add Automobile</title>
</head>
<body>
<h1>Add A New Automobile</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>Make: <input type="text" name="make"></p>
<p>Model: <input type="text" name="model"></p>
<p>Year: <input type="text" name="year"></p>
<p>Mileage: <input type="text" name="mileage"></p>
<p><input type="submit" value="Add"> <a href="index.php">Cancel</a></p>
</form>
</body>
</html>