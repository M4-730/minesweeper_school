<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "buscaminas_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión");
}

$usuario   = $_POST['usuario'];
$password  = $_POST['password'];
$password2 = $_POST['password2'];

// Verificar que las contraseñas coincidan
if ($password !== $password2) {
    die("Las contraseñas no coinciden. <a href='registration.html'>Volver</a>");
}

// Verificar que el usuario no exista ya
$sql = "SELECT id FROM user WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Ese usuario ya existe. <a href='registration.html'>Volver</a>");
}

$stmt->close();

// Insertar el nuevo usuario
$sql = "INSERT INTO user (name, pass) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $password);

if ($stmt->execute()) {
    header("Location: login.html?registered=1");
    exit();
} else {
    echo "Error al registrar. <a href='registration.html'>Volver</a>";
}

$conn->close();
?>