<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['profile_id']) && !isset($_POST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    exit;
}
$pid = $_GET['profile_id'] ?? $_POST['profile_id'];

// Ownership check and fetch
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute([':pid' => $pid]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false) {
    $_SESSION['error'] = "Bad value for profile_id";
    header("Location: index.php");
    exit;
}
if ($profile['user_id'] != $_SESSION['user_id']) {
    die("ACCESS DENIED");
}

// Position validation helper
function validatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i]) && !isset($_POST['desc'.$i])) continue;
        if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) return "All fields are required";
        $year = trim($_POST['year'.$i]);
        $desc = trim($_POST['desc'.$i]);
        if ($year === '' || $desc === '') return "All fields are required";
        if (!is_numeric($year)) return "Year must be numeric";
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        exit;
    }

    // Base profile validation
    $fn = trim($_POST['first_name'] ?? '');
    $ln = trim($_POST['last_name'] ?? '');
    $em = trim($_POST['email'] ?? '');
    $he = trim($_POST['headline'] ?? '');
    $su = trim($_POST['summary'] ?? '');

    if ($fn === '' || $ln === '' || $em === '' || $he === '' || $su === '') {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$pid);
        exit;
    }
    if (strpos($em, '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$pid);
        exit;
    }

    // Positions validation
    $posCheck = validatePos();
    if ($posCheck !== true) {
        $_SESSION['error'] = $posCheck;
        header("Location: edit.php?profile_id=".$pid);
        exit;
    }

    // Update profile
    $stmt = $pdo->prepare('UPDATE Profile
                           SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su
                           WHERE profile_id=:pid');
    $stmt->execute([
        ':fn' => $fn, ':ln' => $ln, ':em' => $em, ':he' => $he, ':su' => $su, ':pid' => $pid
    ]);

    // Clear old positions
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute([':pid' => $pid]);

    // Insert positions in on-screen order (rank 1..n)
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) continue;
        $year = trim($_POST['year'.$i]);
        $desc = trim($_POST['desc'.$i]);
        if ($year === '' || $desc === '') continue;

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
                               VALUES (:pid, :rk, :yr, :ds)');
        $stmt->execute([
            ':pid' => $pid,
            ':rk'  => $rank,
            ':yr'  => $year,
            ':ds'  => $desc
        ]);
        $rank++;
    }

    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    exit;
}

// Load existing positions ordered by rank
$stmt = $pdo->prepare("SELECT position_id, year, description FROM Position
                       WHERE profile_id = :pid ORDER BY rank");
$stmt->execute([':pid' => $pid]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Akshit Rao — Edit Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<style>
  .form-row { display:flex; align-items:center; margin:8px 0; }
  .form-row label { width:140px; font-weight:bold; text-align:right; padding-right:10px; }
  .form-row input[type="text"] { flex:1; padding:6px; box-sizing:border-box; }
  .form-row textarea { flex:1; padding:6px; box-sizing:border-box; min-height:100px; resize:vertical; }
  #position_fields .pos-block { border:1px solid #ddd; padding:10px; margin-bottom:8px; border-radius:4px; background:#fafafa; }
</style>
</head>
<body class="container">
<h1>Editing Profile for <?= htmlentities($_SESSION['name']) ?></h1>
<?php
if (isset($_SESSION['error'])) {
    echo('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="post">
  <div class="form-row">
    <label for="fn">First Name:</label>
    <input type="text" name="first_name" id="fn" value="<?= htmlentities($profile['first_name']) ?>">
  </div>
  <div class="form-row">
    <label for="ln">Last Name:</label>
    <input type="text" name="last_name" id="ln" value="<?= htmlentities($profile['last_name']) ?>">
  </div>
  <div class="form-row">
    <label for="em">Email:</label>
    <input type="text" name="email" id="em" value="<?= htmlentities($profile['email']) ?>">
  </div>
  <div class="form-row">
    <label for="he">Headline:</label>
    <input type="text" name="headline" id="he" value="<?= htmlentities($profile['headline']) ?>">
  </div>
  <div class="form-row" style="align-items:flex-start;">
    <label for="su">Summary:</label>
    <textarea name="summary" id="su" rows="8" cols="80"><?= htmlentities($profile['summary']) ?></textarea>
  </div>

  <div class="form-row">
    <label>Position:</label>
    <button id="addPos" class="btn btn-default">+ Add Position</button>
  </div>
  <div id="position_fields">
    <?php
      $index = 0;
      foreach ($positions as $p) {
          $index++;
          echo '<div class="pos-block" id="position'.$index.'">';
          echo '<p>Year: <input type="text" name="year'.$index.'" value="'.htmlentities($p['year']).'"> ';
          echo '<input type="button" value="-" class="btn btn-xs btn-danger" onclick="$(\'#position'.$index.'\').remove(); return false;"></p>';
          echo '<textarea name="desc'.$index.'" rows="8" cols="80">'.htmlentities($p['description']).'</textarea>';
          echo "</div>\n";
      }
    ?>
  </div>

  <input type="hidden" name="profile_id" value="<?= htmlentities($pid) ?>">
  <p>
    <input type="submit" class="btn btn-primary" value="Save">
    <input type="submit" name="cancel" class="btn btn-default" value="Cancel">
  </p>
</form>

<script>
let countPos = <?= isset($index) ? (int)$index : 0 ?>;
const maxPos = 9;

function addPositionBlock() {
  if (countPos >= maxPos) {
    alert("Maximum of nine position entries exceeded");
    return false;
  }
  countPos++;
  const idx = countPos;
  const html =
    '<div class="pos-block" id="position'+idx+'">'+
      '<p>Year: <input type="text" name="year'+idx+'" value="" /> '+
      '<input type="button" value="-" class="btn btn-xs btn-danger" '+
      'onclick="$(\'#position'+idx+'\').remove(); return false;"></p>'+
      '<textarea name="desc'+idx+'" rows="8" cols="80"></textarea>'+
    '</div>';
  $("#position_fields").append(html);
  return false;
}

$('#addPos').on('click', function(e){
  e.preventDefault();
  addPositionBlock();
});
</script>
</body>
</html>