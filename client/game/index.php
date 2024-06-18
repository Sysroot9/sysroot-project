<?php 
session_start();

// Ativa exibição de erros em modo de depuração
$DEBUG_ERRORLEVEL = 1;
if ($DEBUG_ERRORLEVEL == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once('../../assets/server/config.php');
$db = Database::getInstance();
$conn = $db->getConnection();

// Verifica se o usuário tem um gameId na sessão
if (empty($_SESSION['gameId']) || !isset($_SESSION['gameId'])) {
    header("Location: ../home.php");
    exit();
}

$gameId = $_SESSION['gameId'];
$playerId = $_SESSION['userId'];
$gamesDirectory = '../../games';
$settingsPath = $gamesDirectory . '/' . $gameId . '/settings/data.json';

// Carrega as informações do jogo
if (file_exists($settingsPath)) {
    $data = json_decode(file_get_contents($settingsPath), true);
} else {
    die('ERRO CRÍTICO: Não foi possível obter as informações do jogo.');
}

/* VERIFICAÇÃO LEGADA
// Verifica se o jogo já está na tabela `game_status`
$stmt = $conn->prepare("SELECT COUNT(*) FROM game_status WHERE game_id = ?");
$stmt->bind_param("i", $gameId);
$stmt->execute();
$stmt->bind_result($gameExists);
$stmt->fetch();
$stmt->close();

if ($gameExists == 0) {
    // Insere o jogo se ele não existir
    $stmt = $conn->prepare("INSERT INTO game_status (game_id, state) VALUES (?, 'waiting')");
    $stmt->bind_param("i", $gameId);
    $stmt->execute();
    $stmt->close();
}

// Verifica se o jogador já está na tabela `player_status`
$stmt = $conn->prepare("SELECT COUNT(*) FROM player_status WHERE player_id = ? AND game_id = ?");
$stmt->bind_param("ii", $playerId, $gameId);
$stmt->execute();
$stmt->bind_result($playerExists);
$stmt->fetch();
$stmt->close();

if ($playerExists == 0) {
    // Insere o jogador se ele não existir
    $stmt = $conn->prepare("INSERT INTO player_status (player_id, game_id, is_online) VALUES (?, ?, TRUE)");
    $stmt->bind_param("si", $playerId, $gameId);
    $stmt->execute();
    $stmt->close();
} else {
    // Atualiza o status para online se o jogador já existir
    $stmt = $conn->prepare("UPDATE player_status SET is_online = TRUE WHERE player_id = ? AND game_id = ?");
    $stmt->bind_param("si", $playerId, $gameId);
    $stmt->execute();
    $stmt->close();
}
*/
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <link rel="stylesheet" href="/css/global.css">
    <?php
        $gameCssPath = $gamesDirectory . '/' . $gameId . '/settings/styles.css';
        if (file_exists($gameCssPath)) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($gameCssPath) . '">';
        }
    ?>
    <script>
        const playerId = <?php echo json_encode($playerId); ?>;
        const gameId = <?php echo json_encode($gameId); ?>;
    </script>
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>

    <div id="gameStart" class="hidden">
        <!-- Jogo iniciado com sucesso -->
        <h1>O jogo começou!</h1>
        <p>Se você está vendo essa tela, parabeńs! Todos os jogadores entraram na partida.</p>
        <p>Lembre-se de que este é apenas um teste. Até mais! ;)</p>
    </div>

    <div id="waitingGameStart" class="hidden">
        <!-- Aguandando inicialização do jogo -->
        <h1>Aguarde</h1>
        <p>Aguandando os outros jogadores entrarem</p>
    </div>

    <div id="alreadyInUse" class="hidden">
        <!-- Jogo não iniciado, usuário já conectado -->
        <h1>Erro de Conexão</h1>
        <p>Este usuário já está conectado em outra sessão.</p>
    </div>

    <div id="notRegistredInGame" class="hidden">
        <!-- Jogo não iniciado, usuário não cadastrado no jogo -->
        <h1>Erro de Conexão</h1>
        <p>Este usuário não está cadastrado para poder usar o jogo.</p>
    </div>

    <div id="disconnected" class="hidden">
        <!-- Jogo não inciado, websocket desconectado -->
        <h1>Desconectado</h1>
        <p>Você foi desconectado do servidor.</p>
    </div>

    <script src="./game.js"></script>
</body>
</html>
