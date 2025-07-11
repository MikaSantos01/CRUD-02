<?php
require_once 'db.php';
require_once 'authenticate.php';

// Seleciona todas as consultas com nomes do médico e paciente
$stmt = $pdo->query("
    SELECT 
        consultas.*, 
        medicos.nome AS medico_nome, 
        pacientes.nome AS paciente_nome 
    FROM consultas 
    LEFT JOIN medicos ON consultas.medico_id = medicos.id
    LEFT JOIN pacientes ON consultas.paciente_id = pacientes.id
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
                        <a href="/php/create-consulta.php">Agendar</a> |
                        <a href="/php/index-consulta.php">Listar</a>
                    </li>
                    <li><a href="/php/logout.php">Logout (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li><a href="/php/user-login.php">Login</a></li>
                    <li><a href="/php/user-register.php">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td><?= $consulta['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($consulta['data'])) ?></td>
                        <td><?= date('H:i', strtotime($consulta['hora'])) ?></td>
                        <td><?= htmlspecialchars($consulta['paciente_nome']) ?></td>
                        <td><?= htmlspecialchars($consulta['medico_nome']) ?></td>
                        <td>
                            <a href="read-consulta.php?id=<?= $consulta['id'] ?>">Visualizar</a>
                            <a href="update-consulta.php?id=<?= $consulta['id'] ?>">Editar</a>
                            <a href="delete-consulta.php?id=<?= $consulta['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta consulta?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($consultas)): ?>
                    <tr><td colspan="6">Nenhuma consulta encontrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
