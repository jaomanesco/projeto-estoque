<?php
session_start(); // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de configuração para obter $conn (conexão com o banco de dados)
    require_once "config.php";

    // Recebe dados do formulário
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $nome = $_POST['nome'];

    // Verifica se o nome de usuário já está em uso
    $stmt_verifica = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt_verifica->bind_param("s", $username);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        $error_message = "Nome de usuário já está em uso.";
    } else {
        // Hash da senha
        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

        // Insere o novo usuário no banco de dados
        $stmt = $conn->prepare("INSERT INTO usuarios (username, senha, nome) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $nome);

        if ($stmt->execute()) {
            echo "Usuário criado com sucesso!";
            // Redireciona para a página de login, por exemplo
            header("Location: ../login.php");
            exit();
        } else {
            $error_message = "Erro ao criar usuário: " . $conn->error;
        }

        // Fecha a statement
        $stmt->close();
    }

    // Fecha a statement de verificação
    $stmt_verifica->close();

    // Fecha a conexão
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Criar Usuário</h2>
        <?php if (isset($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Nome de Usuário:</label>
            <input type="text" id="username" name="username" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <input type="submit" value="Criar Usuário">
        </form>
    </div>
</body>
</html>
