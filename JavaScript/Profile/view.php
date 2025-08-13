<?php
session_start();
require_once "pdo.php";

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = "Profile not found";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head><title>View Profile</title></head>
<body>
<h1>Profile Information</h1>
<p>First Name: <?= htmlentities($row['first_name']) ?></p>
<p>Last Name: <?= htmlentities($row['last_name']) ?></p>
<p>Email: <?= htmlentities($row['email']) ?></p>
<p>Headline:<br><?= htmlentities($row['headline']) ?></p>
<p>Summary:<br><?= htmlentities($row['summary']) ?></p>
<p><a href="index.php">Done</a></p>
</body>
</html>