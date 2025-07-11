<?php
require_once 'db.php';
require_once 'authenticate.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    echo "ID da consulta não fornecido.";
    exit();
}

$id = $_GET['id'];

// Prepara a instrução SQL para excluir a consulta
$stmt = $pdo->prepare("DELETE FROM consultas WHERE id = ?");
$stmt->execute([$id]);

// Redireciona para a lista de consultas
header('Location: index-consulta.php');
exit();
?>
