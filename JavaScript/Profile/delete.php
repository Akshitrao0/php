<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("Not logged in");
}

if (!isset($_GET['profile_id']) && !isset($_POST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$pid = $_GET['profile_id'] ?? $_POST['profile_id'];

// On POST, actually delete after verifying ownership
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // Check the row exists and belongs to this user
    $stmt = $pdo->prepare("SELECT user_id FROM Profile WHERE profile_id = :pid");
    $stmt->execute([':pid' => $pid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        $_SESSION['error'] = "Profile not found";
        header("Location: index.php");
        return;
    }
    if ($row['user_id'] != $_SESSION['user_id']) {
        die("Access denied");
    }

    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :pid");
    $stmt->execute([':pid' => $pid]);
    $_SESSION['success'] = "Profile deleted";
    header("Location: index.php");
    return;
}

// On GET, show confirmation screen
$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name, user_id 
                       FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $pid]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = "Bad value for profile_id";
    header("Location: index.php");
    return;
}
if ($row['user_id'] != $_SESSION['user_id']) {
    die("Access denied");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Delete Profile</title>
<meta charset="utf-8">
</head>
<body>
<h1>Confirm: Deleting Profile</h1>
<p>Name: <?= htmlentities($row['first_name']) . " " . htmlentities($row['last_name']) ?></p>
<form method="post">
    <input type="hidden" name="profile_id" value="<?= htmlentities($row['profile_id']) ?>">
    <p>
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="cancel" value="Cancel" formaction="index.php">
    </p>
</form>
</body>
</html>