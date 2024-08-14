<?php
// Arquivo de configuração com a conexão ao banco de dados
include 'backend/config.php';

// Inicia a sessão
session_start();

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario_logado'])) {
    // Se já estiver logado, redireciona para a página principal
    header('Location: index.php');
    exit();
}

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém o nome de usuário e senha do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query para obter o hash da senha do usuário
    $sql = "SELECT id, username, senha FROM usuarios WHERE username = '$username'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        // Usuário encontrado, verifica a senha usando password_verify()
        $row = $result->fetch_assoc();
        $hashed_password = $row['senha'];

        if (password_verify($password, $hashed_password)) {
            // Senha correta, inicia a sessão e redireciona para a página principal
            $_SESSION['usuario_logado'] = true;
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit();
        } else {
            // Senha incorreta
            $error_message = "Usuário ou senha incorretos.";
        }
    } else {
        // Usuário não encontrado
        $error_message = "Não encontramos seu usuario.";
    }
}

// Início do HTML
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Login</h1>
        </header>
        <main>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <?php
                if (isset($error_message)) {
                    echo "<div class='error'>$error_message</div>";
                }
                ?>
                <button type="submit">Login</button>
            </form>
        </main>
    </div>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
