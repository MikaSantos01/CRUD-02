<?php
require_once 'db.php';
require_once 'authenticate.php';

// Seleciona todas as consultas com nomes do médico e paciente
$stmt = $pdo->query("
    SELECT 
        consulta.id_medico,
        consulta.id_paciente,
        consulta.data_hora,
        consulta.observacoes,
        medico.nome AS medico_nome, 
        paciente.nome AS paciente_nome 
    FROM consulta 
    LEFT JOIN medico ON consulta.id_medico = medico.id
    LEFT JOIN paciente ON consulta.id_paciente = paciente.id
");
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista de Consultas</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <header>
        <h1>Lista de Consultas</h1>
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
                        <a href="/php/agendar-consulta.php">Agendar</a> |
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
        <table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Médico</th>
                    <th>Paciente</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($consultas)): ?>
                    <tr><td colspan="6">Nenhuma consulta encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td><?= htmlspecialchars($consulta['medico_nome']) ?></td>
                            <td><?= htmlspecialchars($consulta['paciente_nome']) ?></td>
                            <td><?= date('d/m/Y', strtotime($consulta['data_hora'])) ?></td>
                            <td><?= date('H:i', strtotime($consulta['data_hora'])) ?></td>
                            <td><?= htmlspecialchars($consulta['observacoes']) ?></td>
                            <td>
                                <a href="read-consulta.php?id_medico=<?= $consulta['id_medico'] ?>&id_paciente=<?= $consulta['id_paciente'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>">Visualizar</a> |
                                <a href="update-consulta.php?id_medico=<?= $consulta['id_medico'] ?>&id_paciente=<?= $consulta['id_paciente'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>">Editar</a> |
                                <a href="cancelar-consulta.php?id_medico=<?= $consulta['id_medico'] ?>&id_paciente=<?= $consulta['id_paciente'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>" onclick="return confirm('Tem certeza que deseja cancelar esta consulta?');">Cancelar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
