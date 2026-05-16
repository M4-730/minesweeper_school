<?php
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

$usuario   = $_POST['usuario'];
$password  = $_POST['password'];
$password2 = $_POST['password2'];

// Verificar que las contraseñas coincidan
if ($password !== $password2) {
    die("Las contraseñas no coinciden. <a href='registration.html'>Volver</a>");
}

// Verificar que el usuario no exista ya
$sql = "SELECT id FROM users WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    die("Ese usuario ya existe. <a href='registration.html'>Volver</a>");
}

// Insertar el nuevo usuario
$sql = "INSERT INTO users (name, pass) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt->execute([$usuario, $password])) {
    header("Location: login.html?registered=1");
    exit();
} else {
    echo "Error al registrar. <a href='registration.html'>Volver</a>";
}

$conn = null;
?>