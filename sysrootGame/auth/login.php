
<?php
session_start();
require_once('./config.php');
include('./fn/verify.php');
$db = Database::getInstance();
$conn = $db->getConnection();
//include('./fn/verify.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $sql = "SELECT * FROM tbUsers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['password'])) {
            // A senha está correta
            $userId = $row['id'];
            $name = $row['name'];
            $username = $row['username'];
            $_SESSION['userId'] = $userId;
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['username'] = $username;
            header("Location: ./fn/verify.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos";
        }
    } else {
        $erro = "E-mail não cadastrado";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="form.css">
    <?php if ($errorlevel == -1) { ?>
        <meta http-equiv="refresh" content="0;url=./fn/reset-password.php">
    <?php } ?>
    <?php $secureCredentials = $_SESSION['secureCredentials'];?>
    <?php if ($secureCredentials == 1) { ?>
        <meta http-equiv="refresh" content="0;url=./logout.php">
    <?php }?>
</head>
<body>
    <div class="page">
        <form method="POST" class="formLogin">
            <img src="../assets/img/logo_mini.png" alt="Logo do Sysroot" id="logosysroot">
            <h1 class="roboto-bold">Acesse sua conta</h1>
            <label for="email">E-mail</label>
            <input type="email" placeholder="Digite seu e-mail" autofocus="true" id="email" name="email"/>
            <label for="password">Senha</label>
            <input type="password" placeholder="Digite sua senha" id="senha" name="senha"/>
            <a href="./fn/reset-password-request.php">Esqueci minha senha</a>
            <button class="btn" type="submit">Entrar</button>
        </form>
        <div class="uiMiniMsgBox">
            <?php if(!(empty($erro))) { ?>
                <p class="noto-color-emoji-regular"><?php echo "⛔️ " . $erro; ?></p>
            <?php } ?>
        </div>
    </div>
    <script src="./script.js"></script>
</body>
</html>