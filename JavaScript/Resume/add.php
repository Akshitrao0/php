<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

// Position validation helper
function validatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i]) && !isset($_POST['desc'.$i])) continue;
        if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) return "All fields are required";
        $year = trim($_POST['year'.$i]);
        $desc = trim($_POST['desc'.$i]);
        if (strlen($year) < 1 || strlen($desc) < 1) return "All fields are required";
        if (!is_numeric($year)) return "Year must be numeric";
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Base profile validation
    $fn = trim($_POST['first_name'] ?? '');
    $ln = trim($_POST['last_name'] ?? '');
    $em = trim($_POST['email'] ?? '');
    $he = trim($_POST['headline'] ?? '');
    $su = trim($_POST['summary'] ?? '');

    if ($fn === '' || $ln === '' || $em === '' || $he === '' || $su === '') {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        exit;
    }
    if (strpos($em, '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        exit;
    }

    // Positions validation
    $posCheck = validatePos();
    if ($posCheck !== true) {
        $_SESSION['error'] = $posCheck;
        header("Location: add.php");
        exit;
    }

    // Insert Profile
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn'  => $fn,
        ':ln'  => $ln,
        ':em'  => $em,
        ':he'  => $he,
        ':su'  => $su
    ]);
    $profile_id = $pdo->lastInsertId();

    // Insert Positions (by rank, in the order on screen)
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) continue;
        $year = trim($_POST['year'.$i]);
        $desc = trim($_POST['desc'.$i]);
        if ($year === '' || $desc === '') continue; // already validated; safe-guard

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
                               VALUES (:pid, :rk, :yr, :ds)');
        $stmt->execute([
            ':pid' => $profile_id,
            ':rk'  => $rank,
            ':yr'  => $year,
            ':ds'  => $desc
        ]);
        $rank++;
    }

    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Akshit Rao — Add Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<style>
  .form-row { display:flex;  margin:8px 0; }
  .form-row label { width:140px; font-weight:bold; text-align:right; padding-right:10px; }
  .form-row input[type="text"] { flex:1; padding:6px; box-sizing:border-box; }
  .form-row textarea { flex:1; padding:6px; box-sizing:border-box; min-height:100px; resize:vertical; }
  #position_fields .pos-block { border:1px solid #ddd; padding:5px; margin-bottom:8px; border-radius:4px; background:#fafafa; }
</style>
</head>
<body class="container">
<h1>Adding Profile for <?= htmlentities($_SESSION['name']) ?></h1>
<?php
if (isset($_SESSION['error'])) {
    echo('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="post">
  <div class="form-row">
    <label for="fn">First Name:</label>
    <input type="text" name="first_name" id="fn">
  </div>
  <div class="form-row">
    <label for="ln">Last Name:</label>
    <input type="text" name="last_name" id="ln">
  </div>
  <div class="form-row">
    <label for="em">Email:</label>
    <input type="text" name="email" id="em">
  </div>
  <div class="form-row">
    <label for="he">Headline:</label>
    <input type="text" name="headline" id="he">
  </div>
  <div class="form-row" style="align-items:flex-start;">
    <label for="su">Summary:</label>
    <textarea name="summary" id="su" rows="8" cols="80"></textarea>
  </div>

  <div class="form-row">
    <label>Position:</label>
    <button id="addPos" class="btn btn-default">+ Add Position</button>
  </div>
  <div id="position_fields"></div>

  <p>
    <input type="submit" class="btn btn-primary" value="Add">
    <a href="index.php" class="btn btn-default">Cancel</a>
  </p>
</form>

<script>
let countPos = 0;
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