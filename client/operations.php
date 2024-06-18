<?php
function file_operations($userId, $operation, $path, $content = null) {

    switch ($operation) {

        case 'write':
            $file = fopen($path, 'w');
            fwrite($file, $content);
            fclose($file);
            break;

        case 'read':
            if (file_exists($path)) {
                $file = fopen($path, 'r');
                $content = fread($file, filesize($path));
                fclose($file);
                return $content;
            } else {
                return false;
            }
            break;

        case 'delete':
            if (file_exists($path)) {
                unlink($path);
            }
            break;

        case 'create':
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            break;
        
        case 'profile_picture':
            $path = "../user/$userId/data";
            $defaultPath = '../user/default/data/profile.png';

            // Verifique se a pasta existe, se não, crie-a
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            // Verifique se a imagem PNG existe, se não, verifique a imagem JPG
            if (!file_exists("$path/profile.png")) {
                // Se a imagem PNG não existir, verifique a imagem JPG
                if (!file_exists("$path/profile.jpg")) {
                    // Se nenhum dos dois existir, use a imagem padrão
                    $path = $defaultPath;
                } else {
                    // Se a imagem JPG existir, atualize o caminho para a imagem JPG
                    $path = "$path/profile.jpg";
                }
            } else {
                // Se a imagem PNG existir, atualize o caminho para a imagem PNG
                $path = "$path/profile.png";
            }

            return $path;

        case 'upload_profile_picture':
            
            break;

        default:
            echo "Operação inválida";
    }
}

function upload_file($userId, $path) {

    // Verifica se o formulário foi submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
        $target_dir = "../user/$userId/data/"; // Diretório onde o arquivo será enviado
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]); // Caminho completo do arquivo
        $uploadOk = 0;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Extensão do arquivo
        
        // Verifique se a pasta existe, se não, crie-a
        if (!file_exists(dirname($target_dir))) {
            mkdir(dirname($path), 0777, true);
        }

        // Verifica se o arquivo é uma imagem real ou um arquivo fake
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check == false) {
                $uploadOk += 2;
            }
        }
        
        /* Verifica se o arquivo já existe
        if (file_exists($target_file)) {
            echo "Desculpe, o arquivo já existe.";
            $uploadOk = 0;
        }*/
        
        // Limita o tamanho do arquivo
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $uploadOk += 4;
        }
        
        // Limita os formatos permitidos
        if($imageFileType != "jpg" && $imageFileType != "png") {
            $uploadOk += 8;
        }
        
        // Se houver algum erro, exibe uma mensagem
        if ($uploadOk == 0) {
            $newFileName = $target_dir . "profile." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
            if (!(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newFileName))) {
                $uploadOk +=16;
            }
        }
    }
    return $uploadOk;
}
?>
