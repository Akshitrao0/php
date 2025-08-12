
<?php
require_once "pdo.php";
$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';
$msg = "";

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (isset($_POST['who']) && isset($_POST['pass'])) {
    if (strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1) {
        $msg = "Email and password are required";
    } elseif (strpos($_POST['who'], '@') === false) {
        $msg = "Email must have an at-sign (@)";
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ($check !== $stored_hash) {
            error_log("Login fail ".$_POST['who']." $check");
            $msg = "Incorrect password";
        } else {
            error_log("Login success ".$_POST['who']);
            header("Location: autos.php?name=".urlencode($_POST['who']));
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Akshit's Autos Database Login</title>
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

</head>
<body>
  <h1>Please Log In</h1>
  <?php
    if ($msg !== "") {
        echo '<p style="color:red">'.htmlentities($msg)."</p>\n";
    }
  ?>
  <form method="post">
    <label for="who"><b>Email</b></label>
    <input type="text" name="who" id="who"><br/>
    <label for="pass"><b>Password</b></label>
    <input type="password" name="pass" id="pass"><br/>
    <input type="submit" value="Log In">
    <input type="submit" name="cancel" value="Cancel">
  </form>
</body>
</html>