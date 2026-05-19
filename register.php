<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "buscaminas_db";

$conn = mysql_connect($host, $user, $pass) or die("Error de conexión: " . mysql_error());
mysql_select_db($db, $conn) or die("Error al seleccionar la base de datos: " . mysql_error());
mysql_set_charset('utf8mb4', $conn);

$usuario   = mysql_real_escape_string($_POST['usuario'], $conn);
$password  = mysql_real_escape_string($_POST['password'], $conn);
$password2 = mysql_real_escape_string($_POST['password2'], $conn);

// Verificar que las contraseñas coincidan
if ($password !== $password2) {
    die("Las contraseñas no coinciden. <a href='registration.html'>Volver</a>");
}

// Verificar que el usuario no exista ya
$sql = "SELECT id FROM users WHERE name = '$usuario'";
$result = mysql_query($sql, $conn) or die("Error en consulta: " . mysql_error());
$existing = mysql_fetch_assoc($result);

if ($existing) {
    die("Ese usuario ya existe. <a href='registration.html'>Volver</a>");
}

// Insertar el nuevo usuario
$sql = "INSERT INTO users (name, pass) VALUES ('$usuario', '$password')";
if (mysql_query($sql, $conn)) {
    header("Location: login.html?registered=1");
    exit();
} else {
    echo "Error al registrar. <a href='registration.html'>Volver</a>";
}

mysql_close($conn);
?>