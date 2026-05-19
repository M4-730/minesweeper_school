<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "buscaminas_db";

$conn = mysql_connect($host, $user, $pass) or die("Error de conexión: " . mysql_error());
mysql_select_db($db, $conn) or die("Error al seleccionar la base de datos: " . mysql_error());
mysql_set_charset('utf8mb4', $conn);

$username = mysql_real_escape_string($_POST['username'], $conn);
$score = (int)$_POST['score'];

$sql = "SELECT id FROM users WHERE name = '$username'";
$result = mysql_query($sql, $conn) or die("Error en consulta: " . mysql_error());
$row = mysql_fetch_assoc($result);

$id_user = $row['id'];

$fecha = date('Y-m-d');
$sql = "INSERT INTO score (user_id, score, date) VALUES ($id_user, $score, '$fecha')";
mysql_query($sql, $conn) or die("Error al guardar score: " . mysql_error());

header('Content-Type: application/json');
echo json_encode([
    'exito' => true,
    'mensaje' => 'Score guardado correctamente 🏆'
]);

mysql_close($conn);
?>
