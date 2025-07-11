<?php
require_once 'db.php';
require_once 'authenticate.php';

// Obter médicos e pacientes para preencher o formulário
$medicos = $pdo->query("SELECT id, nome FROM medicos")->fetchAll(PDO::FETCH_ASSOC);
$pacientes = $pdo->query("SELECT id, nome FROM pacientes")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_hora = $_POST['data_hora'];
    $paciente_id = $_POST['paciente_id'];
    $medico_id = $_POST['medico_id'];

    $stmt = $pdo->prepare("INSERT INTO consultas (data_hora, paciente_id, medico_id) VALUES (?, ?, ?)");
    $stmt->execute([$data_hora, $paciente_id, $medico_id]);

    header('Location: index-consulta.php');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Agendar Consulta</h1>
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
        <form method="POST">
            <label for="data_hora">Data e Hora:</label>
            <input type="datetime-local" id="data_hora" name="data_hora" required>

            <label for="paciente_id">Paciente:</label>
            <select id="paciente_id" name="paciente_id" required>
                <option value="">Selecione o paciente</option>
                <?php foreach ($pacientes as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="medico_id">Médico:</label>
            <select id="medico_id" name="medico_id" required>
                <option value="">Selecione o médico</option>
                <?php foreach ($medicos as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Agendar Consulta</button>
        </form>
    </main>
</body>
</html>
