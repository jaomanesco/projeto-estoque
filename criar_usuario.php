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
        <form method="POST" action="backend/processar_criar_usuario.php">
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
