<?php
session_start();
require_once "pdo.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Akshit Rao's Profile Registry</title>
<meta charset="utf-8">
</head>
<body>
<h1>Profile Registry</h1>
<?php
if (isset($_SESSION['success'])) {
    echo('<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
}

$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM Profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['name'])) {
    echo('<p><a href="login.php">Please log in</a></p>');
    if ($rows) {
        echo('<table border="1">'.
             '<tr><th>Name</th><th>Headline</th><th>Action</th></tr>');
        foreach ($rows as $row) {
            echo('<tr><td>');
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
            echo('</td><td>'.htmlentities($row['headline']).'</td><td>View</td></tr>');
        }
        echo('</table>');
    }
} else {
    echo('<p><a href="logout.php">Logout</a></p>');
    echo('<p><a href="add.php">Add New Entry</a></p>');
    if ($rows) {
        echo('<table border="1">'.
             '<tr><th>Name</th><th>Headline</th><th>Action</th></tr>');
        foreach ($rows as $row) {
            echo('<tr><td>');
            echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
            echo('</td><td>'.htmlentities($row['headline']).'</td>');
            echo('<td><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a></td></tr>');
        }
        echo('</table>');
    }
}
?>
</body>
</html>