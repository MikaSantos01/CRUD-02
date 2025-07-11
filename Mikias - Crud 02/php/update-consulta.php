<?php
require_once 'db.php';
require_once 'authenticate.php';

if (!isset($_GET['id'])) {
    echo "ID da consulta não fornecido.";
    exit;
}

$id = $_GET['id'];

// Buscar a consulta pelo ID
$stmt = $pdo->prepare("SELECT * FROM consultas WHERE id = ?");
$stmt->execute([$id]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consulta) {
    echo "Consulta não encontrada.";
    exit;
}

// Buscar pacientes e médicos para os selects
$pacientes = $pdo->query("SELECT id, nome FROM pacientes")->fetchAll(PDO::FETCH_ASSOC);
$medicos = $pdo->query("SELECT id, nome FROM medicos")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paciente_id = $_POST['paciente_id'];
    $medico_id = $_POST['medico_id'];
    $data_consulta = $_POST['data_consulta'];
    $hora_consulta = $_POST['hora_consulta'];

    // Atualizar a consulta
    $stmt = $pdo->prepare("UPDATE consultas SET paciente_id = ?, medico_id = ?, data_consulta = ?, hora_consulta = ? WHERE id = ?");
    $stmt->execute([$paciente_id, $medico_id, $data_consulta, $hora_consulta, $id]);

    header("Location: read-consulta.php?id=$id");
    exit();
}
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
            <li><a href="index.php">Home</a></li>
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
                    <a href="/php/index-consulta.php">Ver todas</a>
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
                <option value="<?= $paciente['id'] ?>" <?= $paciente['id'] == $consulta['paciente_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($paciente['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="medico_id">Médico:</label>
        <select id="medico_id" name="medico_id" required>
            <option value="">Selecione o médico</option>
            <?php foreach ($medicos as $medico): ?>
                <option value="<?= $medico['id'] ?>" <?= $medico['id'] == $consulta['medico_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($medico['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="data_consulta">Data da Consulta:</label>
        <input type="date" id="data_consulta" name="data_consulta" value="<?= htmlspecialchars($consulta['data_consulta']) ?>" required />

        <label for="hora_consulta">Hora da Consulta:</label>
        <input type="time" id="hora_consulta" name="hora_consulta" value="<?= htmlspecialchars($consulta['hora_consulta']) ?>" required />

        <button type="submit">Atualizar Consulta</button>
    </form>
</main>
</body>
</html>
