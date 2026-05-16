<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscaminas Pro</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="menu.css">
</head>
<body>
    <div id="menuContainer"></div>

    <script>
        fetch('menu.html')
            .then(r => r.text())
            .then(html => document.getElementById('menuContainer').innerHTML = html);

        function toggleMenu() {
            document.getElementById('menuLateral').classList.toggle('abierto');
        }

        // Usuario desde la sesión PHP
        const SESSION_USERNAME = "<?php echo $_SESSION['usuario']; ?>";
    </script>

    <div class="container">
        <h1>🎮 Buscaminas</h1>
        
        <!-- Panel de configuración inicial -->
        <div id="setupPanel" class="panel">
            <h2>Configuración del Juego</h2>

            <div class="difficulty-buttons">
                <button class="difficulty-btn" data-difficulty="easy">
                    <span class="difficulty-title">Fácil</span>
                    <span class="difficulty-info">8x8 - 10 minas</span>
                </button>
                <button class="difficulty-btn" data-difficulty="medium">
                    <span class="difficulty-title">Medio</span>
                    <span class="difficulty-info">12x12 - 20 minas</span>
                </button>
                <button class="difficulty-btn" data-difficulty="hard">
                    <span class="difficulty-title">Difícil</span>
                    <span class="difficulty-info">16x16 - 40 minas</span>
                </button>
            </div>
        </div>

        <!-- Panel de juego -->
        <div id="gamePanel" class="panel" style="display: none;">
            <div class="game-header">
                <div class="game-info">
                    <div class="info-item">
                        <span class="info-label">Jugador:</span>
                        <span id="displayUsername" class="info-value">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dificultad:</span>
                        <span id="displayDifficulty" class="info-value">-</span>
                    </div>
                </div>
                
                <div class="game-stats">
                    <div class="stat-item">
                        <span class="stat-icon">💣</span>
                        <span id="minesLeft" class="stat-value">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">⏱️</span>
                        <span id="timer" class="stat-value">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">🚩</span>
                        <span id="flagsUsed" class="stat-value">0</span>
                    </div>
                </div>

                <button id="restartBtn" class="btn-secondary">🔄 Nuevo Juego</button>
            </div>

            <div id="boardContainer">
                <div id="board"></div>
            </div>

            <div class="instructions">
                <p><strong>Clic izquierdo:</strong> Revelar celda | <strong>Clic derecho:</strong> Colocar/quitar bandera</p>
            </div>
        </div>

        <!-- Modal de resultado -->
        <div id="resultModal" class="modal">
            <div class="modal-content">
                <h2 id="resultTitle">🎉 ¡Felicitaciones!</h2>
                <div id="resultMessage"></div>
                <div class="modal-buttons">
                    <button id="playAgainBtn" class="btn-primary">Jugar de Nuevo</button>
                    <button id="changeDifficultyBtn" class="btn-secondary">Cambiar Dificultad</button>
                </div>
            </div>
        </div>
    </div>

    <script src="game.js"></script>
</body>
</html>