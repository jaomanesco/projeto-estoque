<?php
// Arquivo de configuração com a conexão ao banco de dados
include 'backend/config.php';

// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.php');
    exit();
}

// Consulta os produtos do banco de dados
$sql = "SELECT id, nome FROM produtos";
$result = $conn->query($sql);

// Início do HTML
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Produtos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Listagem de Produtos</h1>
        </header>
        <main>
            <section>
                <h2>Produtos Disponíveis:</h2>
                <ul class="produtos-lista">
                    <?php
                    // Verifica se há produtos para listar
                    if ($result->num_rows > 0) {
                        // Loop através dos resultados da consulta
                        while($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row["id"]);
                            $nome = htmlspecialchars($row["nome"]);
                            echo "<li>";
                            echo "<strong>ID:</strong> " . $id . " - <strong>Nome:</strong> " . $nome;
                            echo " <a href='backend/editar_produto.php?id=" . $id . "'>Editar</a>";
                            echo " <a href='backend/excluir_produto.php?id=" . $id . "' onclick='return confirmarExclusao(\"" . $nome . "\")'>Excluir</a>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li>Nenhum produto encontrado.</li>";
                    }
                    ?>
                </ul>
            </section>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Empresa XYZ</p>
            <p><a href="backend/logout.php">Logout</a></p> <!-- Adiciona o link de logout -->
        </footer>
    </div>

    <!-- Script JavaScript para confirmar exclusão -->
    <script>
        function confirmarExclusao(nomeProduto) {
            return confirm("Tem certeza que deseja excluir o produto '" + nomeProduto + "'?");
        }
    </script>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
