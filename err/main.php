<?php

session_start();

$DEBUG_ERRORLEVEL = 0;
if ($DEBUG_ERRORLEVEL == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simula um processamento e redirecionamento com AJAX
    $_SESSION['post_redirect'] = true;
    $response = [
        'status' => 'success',
        'message' => 'Formulário processado com sucesso!',
        'redirect' => '/index.html' // URL para redirecionamento
    ];
    $_SESSION['response'] = json_encode($response);
    exit();
}

http_response_code(400);
$response = [
    'status' => 'error',
    'message' => 'Requisição inválida'
];
$_SESSION['response'] = json_encode($response);

$error_code = isset($_GET['code']) ? intval($_GET['code']) : 0;
$default_img_path = "/err/img/";
$img_path = $default_img_path . "emoji" . $error_code . ".png";

switch ($error_code) {
    case 0:
        header("Location: /index.html");
        break;
    case 400:
        $title = "400 Bad Request";
        $sub = "";
        break;
    case 401:
        $title = "400 Bad Request";
        $sub = "";
        break;
    case 403:
        $title = "Achou que ia conseguir acessar né? (A página está bloqueada)";
        $sub = "Pois tá na hora de voltar. <('.')>";
        break;
    case 404:
        $title = "Ih! Algo de errado parece não estar certo...";
        $sub = "Que tal voltar desde o início? O﹏o";
        break;
    case 500:
        $title = "Eita! Deu um erro interno aqui ou eu estou sobrecarregado.";
        $sub = "Ninguém tem paciência comigo... (╥︣﹏᷅╥)";
        break;
    default:
        $message = "Unknown Error";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eita!</title>
    <link rel="stylesheet" href="/err/default.css">
</head>
<body>
    <main>
        <div class="separador">
            <div class="separador">
                <div class="container image-container">
                    <img src="<?php echo $img_path?>" alt="Emogi representativo" class="image">
                </div>
                <div class="container text-container">
                    <h1><?php echo $title?> <i><?php echo $error_code?></i></h1>
                    <h2><?php echo $sub?></h2>
                    <form method="POST">
                        <button class="btn" type="submit" name="action" value="voltar">Voltar</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="/err/script.js"></script>
</body>
</html>
