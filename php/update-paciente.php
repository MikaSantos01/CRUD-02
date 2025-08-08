<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'authenticate.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID do paciente não fornecido.";
    exit;
}

// Buscar paciente atual
$stmt = $pdo->prepare("SELECT * FROM paciente WHERE id = ?");
$stmt->execute([$id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    echo "Paciente não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $dataNascimento = $_POST['data_nascimento'];
    $tipoSanguineo = $_POST['tipo_sanguineo'];
    $imagem_id = $paciente['imagem_id'];

    // Se o usuário enviou nova imagem
    if (!empty($_FILES['imagem']['name'])) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao;
        $caminho = __DIR__ . '/../images/' . $novoNome;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            // Insere nova imagem
            $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
            $stmt->execute([$novoNome]);
            $imagem_id = $pdo->lastInsertId();
        }
    }

    // Atualiza paciente com nova imagem (ou mantém a anterior)
    $stmt = $pdo->prepare("UPDATE paciente SET nome = ?, data_nascimento = ?, tipo_sanguineo = ?, imagem_id = ? WHERE id = ?");
    $stmt->execute([$nome, $dataNascimento, $tipoSanguineo, $imagem_id, $id]);

    header("Location: index-paciente.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Paciente</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<header>
    <h1>Editar Paciente</h1>
    <nav>
        <ul>
            <li><a href="index-paciente.php">Home</a></li>
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
    <form method="POST" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($paciente['nome']) ?>" required />

        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($paciente['data_nascimento']) ?>" required />

        <label for="tipo_sanguineo">Tipo Sanguíneo:</label>
        <input type="text" id="tipo_sanguineo" name="tipo_sanguineo" value="<?= htmlspecialchars($paciente['tipo_sanguineo']) ?>" required maxlength="3" />

        <label for="imagem">Nova Imagem (opcional):</label>
        <input type="file" id="imagem" name="imagem" accept="image/*">

        <button type="submit">Salvar</button>
    </form>
</main>
</body>
</html>
