<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = 'localhost'; // Substitua pelo seu host
        $dbname = 'sysrootAllData'; // Substitua pelo nome do seu banco de dados
        $username = 'root'; // Substitua pelo seu nome de usuário
        $password = '[*RO3(c06Iql.Kk/'; // Substitua pela sua senha

        $this->conn = mysqli_connect($host, $username, $password, $dbname);

        // Verifica a conexão
        if ($this->conn->connect_error) {
            die("Conexão falhou: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
