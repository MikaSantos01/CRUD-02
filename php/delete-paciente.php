<?php
require_once 'db.php';
require_once 'authenticate.php';

// Verifica se o ID foi passado na URL
if (!isset($_GET['id'])) {
    echo "ID do paciente não fornecido.";
    exit();
}

$id = $_GET['id'];

// Prepara a instrução SQL para excluir o paciente pelo ID
$stmt = $pdo->prepare("DELETE FROM paciente WHERE id = ?");
$stmt->execute([$id]);

// Redireciona de volta para a lista de pacientes
header('Location: index-paciente.php');
exit();
?>
