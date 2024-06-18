<?php
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['exid'])) {
    header("Location: reset-password-request.php");
    exit();
}

// Inclua o arquivo de configuração do banco de dados
require_once('../../assets/server/config.php');
$db = Database::getInstance();
$conn = $db->getConnection();

// Função para evitar ataques de injeção SQL
function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    if ($action == 'refresh') {
        $new_password = isset($_POST['new_password']) ? sanitize($_POST['new_password']) : "";
        $confirm_password = isset($_POST['confirm_password']) ? sanitize($_POST['confirm_password']) : "";

        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "As senhas não coincidem. Tente novamente.";
        } else {
            // Atualiza a senha no banco de dados
            // Verifica se o usuário existe
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT id FROM tbUsers WHERE id = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                // O usuário existe, procede com a atualização da senha
                $stmt->close();
    
                // Hash da nova senha
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
                // Atualiza a senha no banco de dados
                $stmt = $conn->prepare("UPDATE tbUsers SET password = ? WHERE id = ?");
                $stmt->bind_param("ss", $hashed_password, $user_id);
    
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Senha redefinida com sucesso!";
                    $_SESSION['errorlevel'] = 1;

                    // Armazena um novo extra-code
                    $stmt->close();
                    $new_extra_id = bin2hex(random_bytes(10 / 2));
                    $stmt = $conn->prepare("UPDATE tbUsers SET extraId = ? WHERE id = ?");
                    $stmt->bind_param("ss", $new_extra_id, $user_id);
                    if ($stmt->execute()) {
                        $_SESSION['exid'] = $new_extra_id;
                    } else {
                        $ui_exid = $_SESSION['exid'];
                    }

                    header("Location: reset-password.php");
                    exit();

                } else {
                    $_SESSION['error'] = "Ocorreu um erro ao enviar as informações ao servidor. Tente novamente mais tarde.";
                    $_SESSION['errorlevel'] = 1;
                    header("Location: reset-password.php");
                    exit();
                }
    
                $stmt->close();
            } else {
                // Usuário não encontrado
                $_SESSION['error'] = "Usuário não encontrado.";
                $_SESSION['errorlevel'] = 1;
                header("Location: reset-password.php");
                exit();
            }
        }

    } elseif ($action == 'sair' || $action == 'cancelar') {
        header("Location: ../logout.php");
        exit();

    } else {

        header("Location: reset-password.php");
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
        <?php if (empty($_SESSION['errorlevel'])) {?>
            <form method="post" class="formLogin">
                <img src="../../assets/img/logo_mini.png" alt="Logo do Sysroot" id="logosysroot">
                <h1 class="roboto-bold">Redefinir sua senha</h1>
                <label for="new_password">Nova senha</label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="confirm_password">Confirme a nova senha</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button class="btn" type="submit" name="action" value="cancelar" href="../logout.php">Cancelar</button>
                <button class="btn-destructive" type="submit" name="action" value="refresh">Redefinir</button>
            </form>
        <?php }?>
        <?php if ($_SESSION['errorlevel'] == 1) { ?>
            <form method="POST" class="formLogin">
                <img src="../../assets/img/logo_mini.png" alt="Logo do Sysroot" id="logosysroot">
                <h1 class="roboto-bold">Guarde seu novo cód. segurança:<br><i><?php echo $_SESSION['exid'];?></i></h1>
                <button class="btn" type="submit" name="action" value="sair">Sair</button>
            </form>
        <?php } ?>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="uiMiniMsgBox">
                <p class="noto-color-emoji-regular">⛔️ <?php echo $_SESSION['error']; ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="uiMiniMsgBox">
                <p class="noto-color-emoji-regular">✅ <?php echo $_SESSION['success']; ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </div>
</body>
</html>
