// Configuración del juego
const DIFFICULTIES = {
    easy: { rows: 8, cols: 8, mines: 10, name: 'Fácil' },
    medium: { rows: 12, cols: 12, mines: 20, name: 'Medio' },
    hard: { rows: 16, cols: 16, mines: 40, name: 'Difícil' }
};

// Variables globales del juego
let gameState = {
    difficulty: null,
    username: '',
    board: [],
    rows: 0,
    cols: 0,
    totalMines: 0,
    minesLeft: 0,
    flagsUsed: 0,
    revealedCells: 0,
    timer: 0,
    timerInterval: null,
    gameStarted: false,
    gameOver: false,
    firstClick: true,
    saveScoreError: ''
};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

function setupEventListeners() {
    // Botones de dificultad
    document.querySelectorAll('.difficulty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const difficulty = this.dataset.difficulty;
            const username = SESSION_USERNAME; // 👈 viene de la sesión PHP
            startNewGame(difficulty, username);
        });
    });
    
    // Botón reiniciar
    document.getElementById('restartBtn').addEventListener('click', function() {
        restartGame();
    });
    
    // Botones del modal
    document.getElementById('playAgainBtn').addEventListener('click', function() {
        closeModal();
        restartGame();
    });
    
    document.getElementById('changeDifficultyBtn').addEventListener('click', function() {
        closeModal();
        showSetupPanel();
    });
}

function startNewGame(difficulty, username) {
    // Guardar configuración
    gameState.difficulty = DIFFICULTIES[difficulty];
    gameState.username = username;
    gameState.rows = gameState.difficulty.rows;
    gameState.cols = gameState.difficulty.cols;
    gameState.totalMines = gameState.difficulty.mines;
    gameState.minesLeft = gameState.totalMines;
    gameState.flagsUsed = 0;
    gameState.revealedCells = 0;
    gameState.timer = 0;
    gameState.gameStarted = false;
    gameState.gameOver = false;
    gameState.firstClick = true;
    
    // Mostrar panel de juego
    document.getElementById('setupPanel').style.display = 'none';
    document.getElementById('gamePanel').style.display = 'block';
    
    // Actualizar información
    document.getElementById('displayUsername').textContent = username;
    document.getElementById('displayDifficulty').textContent = gameState.difficulty.name;
    
    // Inicializar tablero
    initBoard();
    updateStats();
}

function initBoard() {
    gameState.board = [];
    const boardDiv = document.getElementById('board');
    boardDiv.innerHTML = '';
    
    // Configurar grid
    boardDiv.style.gridTemplateColumns = `repeat(${gameState.cols}, 35px)`;
    boardDiv.style.gridTemplateRows = `repeat(${gameState.rows}, 35px)`;
    
    // Crear celdas
    for (let row = 0; row < gameState.rows; row++) {
        gameState.board[row] = [];
        for (let col = 0; col < gameState.cols; col++) {
            const cell = {
                row: row,
                col: col,
                isMine: false,
                isRevealed: false,
                isFlagged: false,
                adjacentMines: 0
            };
            
            gameState.board[row][col] = cell;
            
            // Crear elemento DOM
            const cellDiv = document.createElement('div');
            cellDiv.className = 'cell';
            cellDiv.dataset.row = row;
            cellDiv.dataset.col = col;
            
            // Event listeners
            cellDiv.addEventListener('click', function() {
                handleCellClick(row, col);
            });
            
            cellDiv.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                handleRightClick(row, col);
            });
            
            boardDiv.appendChild(cellDiv);
        }
    }
}

function placeMines(excludeRow, excludeCol) {
    let minesPlaced = 0;
    
    const safePositions = new Set();
    safePositions.add(`${excludeRow},${excludeCol}`);
    
    const neighbors = getNeighbors(excludeRow, excludeCol);
    neighbors.forEach(([r, c]) => {
        safePositions.add(`${r},${c}`);
    });
    
    while (minesPlaced < gameState.totalMines) {
        const row = Math.floor(Math.random() * gameState.rows);
        const col = Math.floor(Math.random() * gameState.cols);
        
        const posKey = `${row},${col}`;
        
        if (!gameState.board[row][col].isMine && !safePositions.has(posKey)) {
            gameState.board[row][col].isMine = true;
            minesPlaced++;
        }
    }
    
    calculateAdjacentMines();
}

function calculateAdjacentMines() {
    for (let row = 0; row < gameState.rows; row++) {
        for (let col = 0; col < gameState.cols; col++) {
            if (!gameState.board[row][col].isMine) {
                const neighbors = getNeighbors(row, col);
                let count = 0;
                neighbors.forEach(([r, c]) => {
                    if (gameState.board[r][c].isMine) {
                        count++;
                    }
                });
                gameState.board[row][col].adjacentMines = count;
            }
        }
    }
}

function getNeighbors(row, col) {
    const neighbors = [];
    
    for (let dr = -1; dr <= 1; dr++) {
        for (let dc = -1; dc <= 1; dc++) {
            if (dr === 0 && dc === 0) continue;
            
            const newRow = row + dr;
            const newCol = col + dc;
            
            if (newRow >= 0 && newRow < gameState.rows && 
                newCol >= 0 && newCol < gameState.cols) {
                neighbors.push([newRow, newCol]);
            }
        }
    }
    
    return neighbors;
}

function handleCellClick(row, col) {
    if (gameState.gameOver) return;
    
    const cell = gameState.board[row][col];
    
    if (cell.isRevealed || cell.isFlagged) return;
    
    if (gameState.firstClick) {
        placeMines(row, col);
        startTimer();
        gameState.firstClick = false;
        gameState.gameStarted = true;
    }
    
    revealCell(row, col);
}

function revealCell(row, col) {
    const cell = gameState.board[row][col];
    
    if (cell.isRevealed || cell.isFlagged) return;
    
    cell.isRevealed = true;
    gameState.revealedCells++;
    
    const cellDiv = getCellDiv(row, col);
    cellDiv.classList.add('revealed');
    
    if (cell.isMine) {
        cellDiv.classList.add('mine-hit');
        cellDiv.textContent = '💣';
        endGame(false);
    } else {
        if (cell.adjacentMines > 0) {
            cellDiv.textContent = cell.adjacentMines;
            cellDiv.dataset.number = cell.adjacentMines;
        } else {
            floodFill(row, col);
        }
        
        checkWin();
    }
}

function floodFill(row, col) {
    const neighbors = getNeighbors(row, col);
    
    neighbors.forEach(([r, c]) => {
        const cell = gameState.board[r][c];
        
        if (!cell.isRevealed && !cell.isFlagged && !cell.isMine) {
            cell.isRevealed = true;
            gameState.revealedCells++;
            
            const cellDiv = getCellDiv(r, c);
            cellDiv.classList.add('revealed');
            
            if (cell.adjacentMines > 0) {
                cellDiv.textContent = cell.adjacentMines;
                cellDiv.dataset.number = cell.adjacentMines;
            } else {
                floodFill(r, c);
            }
        }
    });
}

function handleRightClick(row, col) {
    if (gameState.gameOver) return;
    
    const cell = gameState.board[row][col];
    
    if (cell.isRevealed) return;
    
    const cellDiv = getCellDiv(row, col);
    
    if (cell.isFlagged) {
        cell.isFlagged = false;
        cellDiv.classList.remove('flagged');
        cellDiv.textContent = '';
        gameState.flagsUsed--;
        gameState.minesLeft++;
    } else {
        cell.isFlagged = true;
        cellDiv.classList.add('flagged');
        cellDiv.textContent = '🚩';
        gameState.flagsUsed++;
        gameState.minesLeft--;
    }
    
    updateStats();
}

function getCellDiv(row, col) {
    return document.querySelector(`[data-row="${row}"][data-col="${col}"]`);
}

function startTimer() {
    if (gameState.timerInterval) {
        clearInterval(gameState.timerInterval);
    }
    
    gameState.timerInterval = setInterval(function() {
        gameState.timer++;
        updateStats();
    }, 1000);
}

function stopTimer() {
    if (gameState.timerInterval) {
        clearInterval(gameState.timerInterval);
        gameState.timerInterval = null;
    }
}

function updateStats() {
    document.getElementById('minesLeft').textContent = gameState.minesLeft;
    document.getElementById('timer').textContent = formatTime(gameState.timer);
    document.getElementById('flagsUsed').textContent = gameState.flagsUsed;
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function checkWin() {
    const totalCells = gameState.rows * gameState.cols;
    const cellsToReveal = totalCells - gameState.totalMines;
    
    if (gameState.revealedCells === cellsToReveal) {
        endGame(true);
    }
}

function endGame(won) {
    gameState.gameOver = true;
    stopTimer();
    
    if (won) {
        sendScoreOnWin()
            .catch(() => {})
            .finally(() => showResultModal(true));
    } else {
        revealAllMines();
        showResultModal(false);
    }
}

function sendScoreOnWin() {
    const timeSeconds = Math.max(1, gameState.timer); // evitar división por cero

    // Mapear dificultad a valor numérico: Fácil=1, Medio=2, Difícil=3
    const name = gameState.difficulty.name || '';
    let difficultyValue = 1;
    if (name.toLowerCase().includes('medio')) difficultyValue = 2;
    if (name.toLowerCase().includes('dif')) difficultyValue = 3;

    const rawScore = (difficultyValue * 2000) / timeSeconds;
    const score = Math.round(rawScore);

    const formData = new FormData();
    formData.append('username', gameState.username);
    formData.append('score', score);

    gameState.saveScoreError = '';

    return fetch('save_score.php', {
        method: 'POST',
        body: formData
    })
    .then(async resp => {
        const text = await resp.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (jsonErr) {
            throw new Error(`HTTP ${resp.status} ${resp.statusText}: ${text || 'Respuesta no válida'}`);
        }

        if (!resp.ok) {
            throw new Error(data.mensaje || `HTTP ${resp.status} ${resp.statusText}`);
        }

        if (data.exito === false) {
            throw new Error(data.mensaje || 'Guardado inválido');
        }

        console.log('Puntuación guardada correctamente:', data.mensaje || data);
    })
    .catch(err => {
        gameState.saveScoreError = err.message || String(err);
        console.error('Error saving score:', gameState.saveScoreError);
    });
}

function revealAllMines() {
    for (let row = 0; row < gameState.rows; row++) {
        for (let col = 0; col < gameState.cols; col++) {
            const cell = gameState.board[row][col];
            if (cell.isMine && !cell.isRevealed) {
                const cellDiv = getCellDiv(row, col);
                cellDiv.classList.add('mine');
                cellDiv.textContent = '💣';
            }
        }
    }
}

function showResultModal(won) {
    const modal = document.getElementById('resultModal');
    const title = document.getElementById('resultTitle');
    const message = document.getElementById('resultMessage');
    
    if (won) {
        title.textContent = '🎉 ¡Felicitaciones!';
        message.innerHTML = `
            <p><strong>${gameState.username}</strong>, ¡has ganado!</p>
            <p>Tiempo: <strong>${formatTime(gameState.timer)}</strong></p>
            <p>Dificultad: <strong>${gameState.difficulty.name}</strong></p>
        `;
    } else {
        title.textContent = '💥 ¡Boom!';
        message.innerHTML = `
            <p>¡Pisaste una mina!</p>
            <p>Tiempo jugado: <strong>${formatTime(gameState.timer)}</strong></p>
            <p>Celdas reveladas: <strong>${gameState.revealedCells}</strong></p>
        `;
    }
    
    modal.classList.add('show');
}

function closeModal() {
    const modal = document.getElementById('resultModal');
    modal.classList.remove('show');
}

function restartGame() {
    startNewGame(
        Object.keys(DIFFICULTIES).find(key => 
            DIFFICULTIES[key].name === gameState.difficulty.name
        ),
        gameState.username
    );
}

function showSetupPanel() {
    document.getElementById('gamePanel').style.display = 'none';
    document.getElementById('setupPanel').style.display = 'block';
    
    if (gameState.timerInterval) {
        clearInterval(gameState.timerInterval);
        gameState.timerInterval = null;
    }
}

// Prevenir menú contextual en todo el documento
document.addEventListener('contextmenu', function(e) {
    if (e.target.classList.contains('cell')) {
        e.preventDefault();
    }
});