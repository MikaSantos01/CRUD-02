<?php
// Exibir erros na tela (apenas para desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'authenticate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $dataNascimento = $_POST['data_nascimento'];
    $tipoSanguineo = $_POST['tipo_sanguineo'];
    $imagem_id = null;

    // Verifica se uma imagem foi enviada
    if (!empty($_FILES['imagem']['name'])) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao;
        $caminho = __DIR__ . '/../images/' . $novoNome;

        // Move a imagem para o diretório
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
            $stmt->execute([$novoNome]);
            $imagem_id = $pdo->lastInsertId();
        }
    }

    // Insere o paciente com ou sem imagem
    $stmt = $pdo->prepare("INSERT INTO paciente (nome, data_nascimento, tipo_sanguineo, imagem_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $dataNascimento, $tipoSanguineo, $imagem_id]);

    header('Location: index-paciente.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adicionar Paciente</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <header>
        <h1>Adicionar Paciente</h1>
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
                    <li><a href="/php/logout.php">Logout (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li><a href="/php/user-login.php">Login</a></li>
                    <li><a href="/php/user-register.php">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- enctype é essencial para upload -->
        <form method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required />

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" required />

            <label for="tipo_sanguineo">Tipo Sanguíneo:</label>
            <input
                type="text"
                id="tipo_sanguineo"
                name="tipo_sanguineo"
                required
                maxlength="3"
                placeholder="Ex: A+, O-"
            />

            <label for="imagem">Imagem de Perfil:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*">

            <button type="submit">Adicionar</button>
        </form>
    </main>
</body>
</html>
