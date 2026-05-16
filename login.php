<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "buscaminas_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión");
}

$usuario  = $_POST['usuario'];
$password = $_POST['password'];

$sql = "SELECT id, PASS FROM user WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if ($password === $row['PASS']) {
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

$conn->close();
?>