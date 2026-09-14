// no 3
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Debug Session</title>
</head>
<body>
    <h2>Isi $_SESSION Saat Ini:</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    <a href="reset_session.php">Reset Data Session</a>
</body>
</html>