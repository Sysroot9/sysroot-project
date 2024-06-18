<?php
session_start();
$DEBUG_ERRORLEVEL = 1;
if ($DEBUG_ERRORLEVEL == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


// Inclua o arquivo de configuração do banco de dados
require_once('../../assets/server/config.php');
$db = Database::getInstance();
$conn = $db->getConnection();

// Função para evitar ataques de injeção SQL
function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'];
    if ($action == 'reset_email') {
        
        $user_email = trim($_POST['email']);
        $extra_id = trim($_POST['exid']);

        $stmt = $conn->prepare("SELECT * FROM tbUsers WHERE email = ? AND extraId = ?");
        $stmt->bind_param("ss", $user_email, $extra_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica se o usuário existe
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            $_SESSION['user_id'] = $user_id;
            $_SESSION['exid'] = $extra_id;
            header("Location: reset-password.php");
            exit();
        } else {
            $_SESSION['error'] = "Usuário não encontrado. Por favor, verifique o e-mail e o códido de segurança.";
        }

    } elseif ($action == 'cancelar') {

        header("Location: ../logout.php");
        exit();

    } elseif ($action == 'esqueci') {

        $errorlevel = -1;

    } elseif ($action == 'voltar') {
    
        $errorlevel = 0;

    } else {

        header("Location: reset-password-request.php");
        exit();

    }

}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../form.css">
</head>
<body>
    <div class="page">
        <?php if (empty($errorlevel) || $errorlevel == 0) {?>
            <form method="POST" class="formLogin" id="forgotPassword">
                <img src="../../assets/img/logo_mini.png" alt="Logo do Sysroot" id="logosysroot">
                <h1 class="roboto-bold">Redefina sua senha</h1>
                <label for="id">E-mail </label>
                <input type="email" id="email" placeholder="Digite seu e-mail" name="email">
                <label for="id">Código de segurança</label>
                <input type="password" id="exid" placeholder="Digite seu código de segurança" name="exid">
                <a id="forgotPassCode" name="action" value="esqueci">Esqueci meu código de segurança</a>
                <input type="hidden" name="action" id="actionInput">
                <button class="btn" type="submit" name="action" value="cancelar" href="../logout.php">Cancelar</button>
                <button class="btn-destructive" type="submit" name="action" value="reset_email">Avançar</button>
            </form>
        <?php } elseif ($errorlevel == -1) {?>
            <form method="POST" class="formLogin">
                <img src="../../assets/img/logo_mini.png" alt="Logo do Sysroot" id="logosysroot">
                <h1 class="roboto-bold">Se esqueceu?</h1>
                <p>Infelizmente não podemos lhe disponibilizar sem saber se é você mesmo. Por isso, entre em contato conosco via email (sysroot9@gmail.com).</p>
                <button class="btn" type="submit" name="action" value="voltar">Voltar</button>
            </form>
        <?php }?>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="uiMiniMsgBox">
                <p class="noto-color-emoji-regular">⛔️ <?php echo $_SESSION['error']; ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>
    <script src="script.js"></script>
</body>
</html>
