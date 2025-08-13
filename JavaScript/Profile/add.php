<?php
session_start();
require_once "pdo.php";
if (!isset($_SESSION['name'])) die("Not logged in");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
        strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 ||
        strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        return;
    }
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Profile</title>
<style>
form {
        max-width: 500px; /* controls width of the form */
    }

    form p {
        display: flex;
        align-items: center; /* vertically center inputs with labels */
        margin: 8px 0;
    }

    form label {
        flex: 0 0 120px;      /* fixed width for labels */
        font-weight: bold;
    }

    form input[type="text"],
    form input[type="email"],
    form textarea {
        flex: 1;              /* inputs take remaining space */
        padding: 6px;
        box-sizing: border-box;
    }

    form textarea {
        resize: vertical;
    }

    input[type="submit"], a {
        margin-top: 10px;
    }
</style>


</head>
<body>
<h1>Adding Profile for <?= htmlentities($_SESSION['name']) ?></h1>
<?php
if (isset($_SESSION['error'])) {
    echo('<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>First Name: <input type="text" name="first_name"></p>
<p>Last Name: <input type="text" name="last_name"></p>
<p>Email: <input type="text" name="email"></p>
<p>Headline:<input type="text" name="headline"></p>
<p>Summary:<br><textarea name="summary" rows="8" cols="80"></textarea></p>
<p><input type="submit" value="Add">
<a href="index.php">Cancel</a></p>
</form>
</body>
</html>