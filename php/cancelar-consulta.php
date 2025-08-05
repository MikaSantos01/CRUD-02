<?php
require_once 'db.php';
require_once 'authenticate.php';

// Verifica se os parâmetros foram fornecidos
if (!isset($_GET['id_medico'], $_GET['id_paciente'], $_GET['data_hora'])) {
    echo "Parâmetros da consulta não fornecidos.";
    exit();
}

$id_medico = $_GET['id_medico'];
$id_paciente = $_GET['id_paciente'];
$data_hora = $_GET['data_hora'];

// Prepara a instrução SQL para excluir a consulta
$stmt = $pdo->prepare("DELETE FROM consulta WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
$stmt->execute([$id_medico, $id_paciente, $data_hora]);

// Redireciona para a lista de consultas
header('Location: index-consulta.php');
exit();
?>
