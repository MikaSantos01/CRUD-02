<?php
// Configurações do banco de dados
$host = 'localhost:3306';
$db = 'clinica';
$user = 'root';
$pass = 'root';

try {
    // Conexão com o banco usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Configura para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Exibe erro caso a conexão falhe
    echo 'Erro na conexão: ' . $e->getMessage();
    exit;
}
?>
