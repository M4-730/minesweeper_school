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

$username = $_POST['username'];
$score = $_POST['score'];

$stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
$stmt->execute([$username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$id_user = $row['id'];

$stmt = $conn->prepare("INSERT INTO score (user_id, score, date) VALUES (?, ?, ?)");
$stmt->execute([$id_user, $score, date('Y-m-d')]);

header('Content-Type: application/json');
echo json_encode([
    'exito' => true,
    'mensaje' => 'Score guardado correctamente 🏆'
]);

$conn = null;
?>
