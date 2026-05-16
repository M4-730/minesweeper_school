<?php

$conn = new mysqli("localhost", "root", "", "buscaminas_db");

if ($conn->connect_error) {
    die("Error de conexión");
}

$username = $_POST['username'];
$score = $_POST['score'];

$stmt = $conn->prepare("SELECT ID FROM `user` WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

$id_user = $result->fetch_assoc()['ID'];

$stmt = $conn->prepare("INSERT INTO score (user_id, score, date) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $id_user, $score, date('Y-m-d'));
$stmt->execute();

header('Content-Type: application/json');
echo json_encode([
    'exito' => true,
    'mensaje' => 'Score guardado correctamente 🏆'
]);

$conn->close();
?>
