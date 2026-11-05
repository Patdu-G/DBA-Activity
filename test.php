
<?php
$host = "localhost";   // later: the IP of the other computer
$port = "5432";
$dbname = "Enrollment_Activity";
$user = "postgres";
$password = "041205";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>