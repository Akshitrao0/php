<?php
session_start();
require_once "pdo.php";

$salt = 'XyZzy12*_';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        return;
    }
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    }
    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users1
                           WHERE email = :em AND password = :pw');
    $stmt->execute([
        ':em' => $_POST['email'],
        ':pw' => $check
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        header("Location: index.php");
        return;
    } else {
        $_SESSION['error'] = "Incorrect password";
        header("Location: login.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
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
<script>
function doValidate() {
    console.log('Validating...');
    try {
        var addr = document.getElementById('email').value;
        var pw = document.getElementById('id_1723').value;
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if (addr.indexOf('@') == -1) {
            alert("Email address must contain @");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
}
</script>
</head>
<body>
<h1>Please Log In</h1>
<?php
if (isset($_SESSION['error'])) {
    echo('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="post">
    <p>Email <input type="text" name="email" id="email"></p>
    <p>Password <input type="password" name="pass" id="id_1723"></p>
    <p><input type="submit" onclick="return doValidate();" value="Log In">
    <input type="submit" name="cancel" value="Cancel"></p>
</form>
</body>
</html>