<?php
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
  <title>Detalhes do Médico</title>
</head>
<body>
  <?php if ($medico): ?>
    <h1>Detalhes do Médico</h1>
    <p><strong>ID:</strong> <?= htmlspecialchars($medico['id']) ?></p>
    <p><strong>Nome:</strong> <?= htmlspecialchars($medico['nome']) ?></p>
    <p><strong>Especialidade:</strong> <?= htmlspecialchars($medico['especialidade']) ?></p>
  <?php else: ?>
    <p>Médico não encontrado.</p>
  <?php endif; ?>
</body>
</html>
