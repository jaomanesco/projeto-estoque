<?php
session_start(); // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de configuração para obter $conn (conexão com o banco de dados)
    require_once "backend/config.php";

    // Recebe dados do formulário
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    // Prepara a query utilizando prepared statements para prevenir SQL Injection
    $stmt = $conn->prepare("SELECT id, username, senha FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Executa a query
    $stmt->execute();

    // Associa variáveis para armazenar os resultados da query
    $stmt->bind_result($id, $db_username, $db_senha);

    // Verifica se o usuário existe e verifica a senha
    if ($stmt->fetch() && password_verify($senha, $db_senha)) {
        // Autenticação bem-sucedida, cria sessão
        $_SESSION['usuario_id'] = $id;
        $_SESSION['username'] = $username;

        // Redireciona para a página inicial ou outra página segura
        header("Location: index.php");
        exit();
    } else {
        // Autenticação falhou
        $error_message = "Nome de usuário ou senha incorretos.";
    }

    // Fecha a statement
    $stmt->close();

    // Fecha a conexão
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Nome de Usuário:</label>
            <input type="text" id="username" name="username" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>