<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="menu.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: white;
            overflow: auto;
        }

        .contenido {
            padding: 40px;
            transition: margin-left 0.3s;
        }

        .bienvenida {
            text-align: center;
            padding: 0px 20px;
        }

        .bienvenida h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .bienvenida p {
            color: #aaa;
            font-size: 18px;
            margin-bottom: 40px;
        }

    </style>
</head>
<body>

    <div id="menuContainer"></div>

    <div class="contenido" id="contenido">
        <div class="bienvenida">
            <h1>🎮 Bienvenido</h1>
            <p>Selecciona una opción del menú para comenzar</p>
            <?php include 'scoreboard.php'; ?>
        </div>
    </div>

    <script>
        fetch('menu.html')
            .then(r => r.text())
            .then(html => {
                document.getElementById('menuContainer').innerHTML = html;
                const links = document.querySelectorAll('.menu-lateral a');
                links.forEach(link => {
                    if (link.href === window.location.href) {
                        link.classList.add('activo');
                    }
                });
            });

        function toggleMenu() {
            document.getElementById('menuLateral').classList.toggle('abierto');
        }
    </script>

</body>
</html>