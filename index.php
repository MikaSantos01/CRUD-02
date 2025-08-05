<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Viva Saúde</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Clínica Médica Viva Saúde</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>Médico: 
                        <a href="/php/create-paciente.php">Adicionar</a> | 
                        <a href="/php/index-paciente.php">Listar</a>
                    </li>
                    <li>Paciente: 
                        <a href="/php/create-medico.php">Adicionar</a> | 
                        <a href="/php/index-medico.php">Listar</a>
                    </li>
                    <li>Consulta: 
                        <a href="/php/agendar-consulta.php">Adicionar</a> | 
                        <a href="/php/index-consulta.php">Listar</a>
                    </li>
                    <li><a href="/php/logout.php">Logout (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li><a href="/php/user-login.php">Login</a></li>
                    <li><a href="php/user-register.php">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>

    </header>

    <main>
        <h2>Bem-vindo a Clínica Viva Saúde</h2>
        <p>Utilize o menu acima para navegar pelo sistema.</p>
    </main>

    <footer>
        <p>&copy; 2025 - Clínica Viva Saúde
          |  CEO - Mikaias Marinho 
        </p>
    </footer>
</body>
</html>
