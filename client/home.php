<?php

$DEBUG_ERRORLEVEL = 1;
if ($DEBUG_ERRORLEVEL == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


include('../auth/fn/verify.php');
include('./operations.php');
require_once('../assets/server/config.php');
$db = Database::getInstance();
$conn = $db->getConnection();

$userId = $_SESSION['userId'];
$email = $_SESSION['email'];
$name = $_SESSION['name'];
$username = $_SESSION['username'];
$upload_file_errorlevel = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == 'sair') {
        header('Location: ../auth/logout.php');
        exit;
    } elseif ($_POST['action'] == 'continuar-tlltip') {
        $errorlevel = 1;
    } elseif ($_POST['action'] == 'iniciar') {
        header("Location: ./game/index.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['game_id'])) {
    $gameId = htmlspecialchars($_POST['game_id']);
    // Processar o ID do jogo conforme necessário
    echo 'ID do jogo recebido: ' . $gameId;
    $_SESSION['gameId'] = $gameId;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
</head>
<body>
    <?php if (empty($errorlevel) || $errorlevel == 0) {?>
        <link rel="stylesheet" href="../auth/form.css">
        <div class="page">
            <form method="POST">
                <div class="container">
                    <div>
                        <h3>Sistema</h3>
                        <p>Muitos recursos deste site ainda estão em desenvolvimento e podem não funcionar corretamente.</p>
                        <button class="btn" type="submit" name="action" value="continuar-tlltip">Continuar</button>
                    </div>
                </div>
            </form>
        </div>
    <?php } elseif ($errorlevel == 1) {?>
        <link rel="stylesheet" href="home.css">
        <div class="menu-hamburguer active" id="menu">
            <!--<img src="../assets/img/logo_mini.png" alt="Logo do Sysroot" id="logosysroot">-->
            <div class="user-info">
                <?php $path = file_operations($userId, 'profile_picture', null, null); ?>
                <img src="<?php echo $path; ?>" alt="Foto de perfil" class="profile-picture">
                <p class="user-name"><?php echo $name; ?></p>
                <p class="user-email"><?php echo $email; ?></p>
            </div>
            <nav class="menu-options">
                <ul>
                    <li><a href="#jogos" class="menu-link" data-section="jogos">Jogos</a></li>
                    <li><a href="#personagens" class="menu-link" data-section="personagens">Personagens</a></li>
                    <li><a href="#configuracoes" class="menu-link" data-section="configuracoes">Configurações</a></li>
                    <li><a href="#sair" class="menu-link" data-section="sair">Sair</a></li>
                </ul>
            </nav>
        </div>
        <div class="content">
            <header>
                <button id="menu-toggle">☰<!--⚙️--></button>
                <!--<h1>Página Inicial</h1>-->
            </header>
            <main>
                <section id="jogos" class="active-section">
                    <h2>Jogos disponíveis</h2>
                    <div class="container">
                        <div class="cont-page-grid">
                        <?php
                            $gamesDirectory = '../games';
                            $games = scandir($gamesDirectory);
                            
                            foreach ($games as $game) {
                                if ($game === '.' || $game === '..') {
                                    continue;
                                }

                                $settingsPath = $gamesDirectory . '/' . $game . '/settings/data.json';
                                $bannerPath = $gamesDirectory . '/' . $game . '/settings/banner.png';

                                if (file_exists($settingsPath)) {
                                    $data = json_decode(file_get_contents($settingsPath), true);
                                    echo '<li id="' . htmlspecialchars($data['id']) . '" class="card game">';
                                    echo '<a href="#jogo" data-section="jogo">';
                                    echo '<img src="' . $gamesDirectory . '/' . $game . '/settings/banner.png" alt="' . htmlspecialchars($data['title']) . '">';
                                    echo '<div>';
                                    echo '<h3>' . htmlspecialchars($data['title']) . '</h3>';
                                    echo '<p>' . htmlspecialchars($data['description']) . '</p>';
                                    echo '</div>';
                                    echo '<form id="hiddenForm" method="POST" style="display:none;">';
                                    echo '<input type="hidden" name="game_id" id="game_id_input">';
                                    echo '</form>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                            }
                        ?>
                        </div>
                    </div>
                </section>
                <section id="jogo">
                    <h2>Iniciar a partida online</h2>
                    <form method="post">
                        <p>Quer iniciar o jogo?</p>
                        <button class="btn" type="submit" name="action" value="iniciar">Iniciar</button>
                    </form>
                </section>
                <section id="configuracoes">
                        <h2>Configurações da sua conta</h2>
                        <div class="container">
                            <!--<form id="config-form">
                                <div class="form-group">
                                    <label for="profile-photo">Foto do Perfil:</label>
                                    <input type="file" id="profile-photo" name="profile-photo">
                                </div>
                                <div class="form-group">
                                    <label for="user-name">Nome:</label>
                                    <input type="text" id="user-name" name="user-name" value="<?php echo $name?>">
                                </div>
                                <div class="form-group">
                                    <label for="user-email">Email:</label>
                                    <input type="email" id="user-email" name="user-email" value="<?php echo $email?>">
                                </div>
                                <button type="submit" class="btn">Salvar Alterações</button>
                            </form>-->
                            <h1>🚫 Epa! Parece que essa opção foi bloqueada pelo administrador.</h1>
                        </div>
                    <!--</div>-->
                </section>
                <section id="sair">
                    <h2>Sair da sua conta</h2>
                    <form method="post">
                        <p>Deseja mesmo sair?</p>
                        <button class="btn-destructive" type="submit" name="action" value="sair">Sair</button>
                    </form>
                </section>
                <section id="personagens">
                    <h2>Seus personagens</h2>
                    <div class="container">
                        <div class="cont-page-grid">
                            <?php
                                $sql = "SELECT id, name, level, class FROM tbCharacters WHERE userId = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $userId);
                                //echo $userId;
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<div class="card user ' . htmlspecialchars($row['id']) . '">';
                                        echo '<a href="#" class="user-card-link" data-id="' . htmlspecialchars($row['id']) . '">';
                                        echo '<div>';
                                        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                                        echo '<p><strong>Nível:</strong> ' . htmlspecialchars($row['level']) . '</p>';
                                        echo '<p><strong>Classe:</strong> ' . htmlspecialchars($row['class']) . '</p>';
                                        echo '<form method="POST" id="' . htmlspecialchars($row['id']) . '"';
                                        echo '</form>';
                                        echo '</div>';
                                        echo '</a>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo "<p>Nenhum personagem encontrado.</p>";
                                }                                
                                $stmt->close();
                                $conn->close();
                            ?>
                        </div>
                    </div>
                </section>
                <section id="personagem">
                    <h2>Personagem <?php echo $username;?></h2>
                    <div class="container">
                        <p>PÁGINA EM MANUTENÇÃO</p>
                    </div>
                </section>
            </main>
        </div>
    <?php } ?>
    <script src="./script.js"></script>
</body>
</html>