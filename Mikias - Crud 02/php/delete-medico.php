<?php
require_once 'db.php';
require_once 'authenticate.php';

// Verifica se o ID foi passado na URL
if (!isset($_GET['id'])) {
    echo "ID do médico não fornecido.";
    exit();
}

$id = $_GET['id'];

// Prepara a instrução SQL para excluir o médico pelo ID
$stmt = $pdo->prepare("DELETE FROM medico WHERE id = ?");
$stmt->execute([$id]);

// Redireciona para a página de listagem de médicos
header('Location: index-medico.php');
exit();
?>
