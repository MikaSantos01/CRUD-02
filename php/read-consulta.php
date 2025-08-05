<?php
require_once 'db.php';
require_once 'authenticate.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index-consulta.php');
    exit();
}

$id = $_GET['id'];

// Consulta os dados da consulta com JOIN para médico e paciente
$stmt = $pdo->prepare("
    SELECT 
        consultas.*, 
        medicos.nome AS medico_nome, 
        pacientes.nome AS paciente_nome,
        pacientes.email AS paciente_email,
        pacientes.data_nascimento AS paciente_nascimento,
        medicos.especialidade AS medico_especialidade
    FROM consultas
    LEFT JOIN medicos ON consultas.medico_id = medicos.id
    LEFT JOIN pacientes ON consultas.paciente_id = pacientes.id
    WHERE consultas.id = ?
");
$stmt->execute([$id]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consulta) {
    echo "Consulta não encontrada.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalhes da Consulta</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
    <h1>Detalhes da Consulta</h1>
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
    <h2>Consulta ID: <?= htmlspecialchars($consulta['id']) ?></h2>
    <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($consulta['data'])) ?></p>
    <p><strong>Hora:</strong> <?= date('H:i', strtotime($consulta['hora'])) ?></p>

    <h3>Paciente</h3>
    <p><strong>Nome:</strong> <?= htmlspecialchars($consulta['paciente_nome']) ?></p>
    <p><strong>E-mail:</strong> <?= htmlspecialchars($consulta['paciente_email']) ?></p>
    <p><strong>Data de Nascimento:</strong> <?= htmlspecialchars($consulta['paciente_nascimento']) ?></p>

    <h3>Médico</h3>
    <p><strong>Nome:</strong> <?= htmlspecialchars($consulta['medico_nome']) ?></p>
    <p><strong>Especialidade:</strong> <?= htmlspecialchars($consulta['medico_especialidade']) ?></p>

    <p>
        <a href="update-consulta.php?id=<?= urlencode($consulta['id']) ?>">Editar</a> |
        <a href="delete-consulta.php?id=<?= urlencode($consulta['id']) ?>" onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">Excluir</a> |
        <a href="index-consulta.php">Voltar</a>
    </p>
</main>
</body>
</html>
