<?php
$conn = new mysqli("localhost", "root", "", "buscaminas_db");

$result = $conn->query("SELECT user.name AS username, score.score, score.date AS created_at
                        FROM score 
                        INNER JOIN user ON score.user_id = user.id
                        ORDER BY score.score DESC 
                        LIMIT 10");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Scoreboard</title>

    <!-- Tus estilos -->
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .scoreboard-container {
            max-width: 900px;
            margin: 40px auto;
        }

        .tabla-score {
            width: 100%;
            border-collapse: collapse;
            background: #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .tabla-score th {
            background-color: #444;
            padding: 15px;
            text-align: left;
            color: #ddd;
            border-bottom: 2px solid #555;
        }

        .tabla-score td {
            padding: 12px 15px;
            border-bottom: 1px solid #555;
            color: white;
        }

        .tabla-score tr:hover {
            background-color: #555;
        }

        .tabla-score tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>

<?php include("menu.html"); ?>

<div class="contenido">
    <div class="scoreboard-container panel">

        <h1>🏆 Scoreboard</h1>

        <table class="tabla-score">
            <tr>
                <th>Jugador</th>
                <th>Puntuación</th>
                <th>Fecha</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['username'] ?></td>
                <td><?= $row['score'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>

        </table>

    </div>
</div>

</body>
</html>
