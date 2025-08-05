<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
require_once 'authenticate.php';

// Buscar médicos e pacientes
$medicos = $pdo->query("SELECT id, nome FROM medico")->fetchAll(PDO::FETCH_ASSOC);
$pacientes = $pdo->query("SELECT id, nome FROM paciente")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_hora_raw = $_POST['data_hora'];
    $data_hora = str_replace('T', ' ', $data_hora_raw) . ':00';
    $paciente_id = $_POST['paciente_id'];
    $medico_id = $_POST['medico_id'];
    $observacoes = $_POST['observacoes'] ?? '';

    // Verifica duplicidade
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM consulta WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
    $stmt->execute([$medico_id, $paciente_id, $data_hora]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        $erro = "Já existe uma consulta nesse horário para esse médico e paciente.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO consulta (id_medico, id_paciente, data_hora, observacoes) VALUES (?, ?, ?, ?)");
            $stmt->execute([$medico_id, $paciente_id, $data_hora, $observacoes]);
            header('Location: index-consulta.php');
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao inserir consulta: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Agendar Consulta</h1>
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
                    <li><a href="/php/logout.php">Logout (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li><a href="/php/user-login.php">Login</a></li>
                    <li><a href="/php/user-register.php">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (!empty($erro)): ?>
            <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

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

            <label for="observacoes">Observações:</label>
            <textarea id="observacoes" name="observacoes" rows="4"></textarea>

            <button type="submit">Agendar</button>
        </form>
    </main>
</body>
</html>
