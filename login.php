<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "buscaminas_db";

$conn = mysql_connect($host, $user, $pass) or die("Error de conexión: " . mysql_error());
mysql_select_db($db, $conn) or die("Error al seleccionar la base de datos: " . mysql_error());
mysql_set_charset('utf8mb4', $conn);

$usuario  = mysql_real_escape_string($_POST['usuario'], $conn);
$password = mysql_real_escape_string($_POST['password'], $conn);

$sql = "SELECT id, pass FROM users WHERE name = '$usuario'";
$result = mysql_query($sql, $conn) or die("Error en consulta: " . mysql_error());
$row = mysql_fetch_assoc($result);

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

mysql_close($conn);
?>