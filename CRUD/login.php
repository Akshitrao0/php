<?php
session_start();
require_once "pdo.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        exit;
    }
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['pass']  ?? '';
    if (strlen($email) < 1 || strlen($pass) < 1) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: index.php");
        exit;
    }
    if (strpos($email, '@') === false) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        exit;
    }

    $salt     = 'XyZzy12*_';
    $check    = hash('md5', $salt . $pass);
    $expected = '1a52e17fa899cf40fb04cfc42e6352f1';
    if ($check !== $expected) {
    error_log("DEBUG: wrong password path hit");
    $_SESSION['error'] = "Incorrect password";
    header("Location: login.php");
    exit;
}
    error_log("Login success $email");
    $_SESSION['name'] = $email;
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
      <style>
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
  <title>Akshit Rao — Autos Login</title>
</head>
<body>
  <h1>Please Log In</h1>
  <?php
  if (isset($_SESSION['error'])) {
      echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n";
      unset($_SESSION['error']);
  }
  ?>
  <form method="post">
    <p>
      <label for="email">Email</label>
      <input type="text" name="email" id="email">
    </p>
    <p>
      <label for="pass">Password</label>
      <input type="password" name="pass" id="pass">
    </p>
    <p>
      <input type="submit" value="Log In">
      <input type="submit" name="cancel" value="Cancel">
    </p>
  </form>
</body>
</html>