<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'authenticate.php';

if (!isset($_GET['id_medico'], $_GET['id_paciente'], $_GET['data_hora'])) {
    echo "Parâmetros da consulta não fornecidos.";
    exit;
}

$id_medico = $_GET['id_medico'];
$id_paciente = $_GET['id_paciente'];
$data_hora = $_GET['data_hora'];

// Buscar consulta específica
$stmt = $pdo->prepare("SELECT * FROM consulta WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
$stmt->execute([$id_medico, $id_paciente, $data_hora]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consulta) {
    echo "Consulta não encontrada.";
    exit;
}

// Buscar pacientes e médicos para selects
$pacientes = $pdo->query("SELECT id, nome FROM paciente")->fetchAll(PDO::FETCH_ASSOC);
$medicos = $pdo->query("SELECT id, nome FROM medico")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_id_paciente = $_POST['paciente_id'];
    $novo_id_medico = $_POST['medico_id'];
    $nova_data_hora_raw = $_POST['data_hora'];
    $nova_data_hora = str_replace('T', ' ', $nova_data_hora_raw) . ':00'; // Ajusta formato para MySQL DATETIME
    $observacoes = $_POST['observacoes'] ?? '';

    $pdo->beginTransaction();

    try {
        // Deleta o registro antigo
        $del = $pdo->prepare("DELETE FROM consulta WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
        $del->execute([$id_medico, $id_paciente, $data_hora]);

        // Insere o novo
        $ins = $pdo->prepare("INSERT INTO consulta (id_medico, id_paciente, data_hora, observacoes) VALUES (?, ?, ?, ?)");
        $ins->execute([$novo_id_medico, $novo_id_paciente, $nova_data_hora, $observacoes]);

        $pdo->commit();

        header("Location: read-consulta.php?id_medico=$novo_id_medico&id_paciente=$novo_id_paciente&data_hora=" . urlencode($nova_data_hora));
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro ao atualizar consulta: " . $e->getMessage();
        exit;
    }
}

$dataHoraFormatada = date('Y-m-d\TH:i', strtotime($consulta['data_hora']));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Consulta</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
    <h1>Editar Consulta</h1>
    <nav>
        <ul>
            <li><a href="index-consulta.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li>Pacientes: 
                    <a href="/php/create-paciente.php">Adicionar</a> | 
                    <a href="/php/index-paciente.php">Listar</a>
                </li>
                <li>Médicos: 
                    <a href="/php/create-medico.php">Adicionar</a> | 
                    <a href="/php/index-medico.php">Listar</a>
                </li>
                <li>Consultas: 
                    <a href="/php/create-consulta.php">Agendar</a> | 
                    <a href="/php/index-consulta.php">Listar</a>
                </li>
                <li><a href="/php/logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
            <?php else: ?>
                <li><a href="/php/user-login.php">Login</a></li>
                <li><a href="/php/user-register.php">Registrar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>
    <form method="POST">
        <label for="paciente_id">Paciente:</label>
        <select id="paciente_id" name="paciente_id" required>
            <option value="">Selecione o paciente</option>
            <?php foreach ($pacientes as $paciente): ?>
                <option value="<?= $paciente['id'] ?>" <?= $paciente['id'] == $consulta['id_paciente'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($paciente['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="medico_id">Médico:</label>
        <select id="medico_id" name="medico_id" required>
            <option value="">Selecione o médico</option>
            <?php foreach ($medicos as $medico): ?>
                <option value="<?= $medico['id'] ?>" <?= $medico['id'] == $consulta['id_medico'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($medico['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="data_hora">Data e Hora da Consulta:</label>
        <input type="datetime-local" id="data_hora" name="data_hora" value="<?= htmlspecialchars($dataHoraFormatada) ?>" required />

        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes"><?= htmlspecialchars($consulta['observacoes']) ?></textarea>

        <button type="submit">Atualizar Consulta</button>
    </form>
</main>
</body>
</html>
