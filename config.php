<?php
// Database connection settings -- update these to match your environment
$db_host = 'localhost';
$db_name = 'conferenceDB';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8",
        $db_user,
        $db_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<p style="color:red;font-family:sans-serif;">Database connection failed: '
        . htmlspecialchars($e->getMessage()) . '</p>');
}
?>
