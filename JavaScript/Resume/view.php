<?php
session_start();
require_once "pdo.php";

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $_GET['profile_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false) {
    $_SESSION['error'] = "Profile not found";
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT year, description FROM Position
                       WHERE profile_id = :pid
                       ORDER BY rank");
$stmt->execute([':pid' => $_GET['profile_id']]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Akshit Rao — View Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" crossorigin="anonymous">
</head>
<body class="container">
<h1>Profile Information</h1>
<p>First Name: <?= htmlentities($profile['first_name']) ?></p>
<p>Last Name: <?= htmlentities($profile['last_name']) ?></p>
<p>Email: <?= htmlentities($profile['email']) ?></p>
<p>Headline:<br><?= htmlentities($profile['headline']) ?></p>
<p>Summary:<br><?= nl2br(htmlentities($profile['summary'])) ?></p>

<?php if ($positions): ?>
  <p>Positions:</p>
  <ul>
    <?php foreach ($positions as $p): ?>
      <li>
        <strong><?= htmlentities($p['year']) ?>:</strong>
        <?= nl2br(htmlentities($p['description'])) ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<p><a class="btn btn-default" href="index.php">Done</a></p>
</body>
</html>