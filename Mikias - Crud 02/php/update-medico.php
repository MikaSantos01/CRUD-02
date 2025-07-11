<?php
require_once 'db.php';
require_once 'authenticate.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do médico não fornecido.";
    exit;
}

// Buscar o médico pelo ID
$stmt = $pdo->prepare("SELECT * FROM medico WHERE id = ?");
$stmt->execute([$id]);
$medico = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medico) {
    echo "Médico não encontrado.";
    exit;
}

// Buscar usuários para associar ao médico
$usuarios = $pdo->query("SELECT id, username FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $especialidade = $_POST['especialidade'];

    // Atualizar o médico no banco
    $stmt = $pdo->prepare("UPDATE medicos SET nome = ?, especialidade = ?, WHERE id = ?");
    $stmt->execute([$nome, $especialidade, $id]);

    header("Location: read-medico.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Médico</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
    <h1>Editar Médico</h1>
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
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($medico['nome']) ?>" required>

        <label for="especialidade">Especialidade:</label>
        <input type="text" id="especialidade" name="especialidade" value="<?= htmlspecialchars($medico['especialidade']) ?>" required>

        <button type="submit">Atualizar</button>
    </form>
</main>
</body>
</html>
