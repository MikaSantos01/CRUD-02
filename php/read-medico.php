<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'authenticate.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index-medico.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM medico WHERE id = ?");
$stmt->execute([$id]);
$medico = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalhes do Médico</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
  <header>
    <h1>Detalhes do Médico</h1>
    <nav>
        <ul>
            <li><a href="/php/index.php">Home</a></li>
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
    <?php if ($medico): ?>
      <p><strong>ID:</strong> <?= htmlspecialchars($medico['id']) ?></p>
      <p><strong>Nome:</strong> <?= htmlspecialchars($medico['nome']) ?></p>
      <p><strong>Especialidade:</strong> <?= htmlspecialchars($medico['especialidade']) ?></p>

      <p>
        <a href="update-medico.php?id=<?= urlencode($medico['id']) ?>">Editar</a> |
        <a href="delete-medico.php?id=<?= urlencode($medico['id']) ?>" onclick="return confirm('Tem certeza que deseja excluir este médico?');">Excluir</a> |
        <a href="index-medico.php">Voltar</a>
      </p>
    <?php else: ?>
      <p>Médico não encontrado.</p>
    <?php endif; ?>
  </main>
</body>
</html>
