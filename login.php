<?php
session_start();

$dbHost = "db.unxarpvzdpgdueyhfzwm.supabase.co";
$dbPort = "5432";
$dbName = "postgres";
$dbUser = "postgres";
$dbPass = "ZDqreY6uQMelMrx9";

$dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
try {
    $conn = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$usuario  = $_POST['usuario'];
$password = $_POST['password'];

$sql = "SELECT id, pass FROM users WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    if ($password === $row['pass']) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['activa']  = true;
        $_SESSION['user_id'] = $row['id'];
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.html?error=1");
        exit();
    }
} else {
    header("Location: login.html?error=1");
    exit();
}

$conn = null;
?>