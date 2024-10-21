<?php
include 'config.php'; // Inclua o arquivo de configuração com a conexão ao banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = intval($_POST['id']);
        $nome = $_POST['nome'];
        $categoria = $_POST['categoria'];
        $descricao = $_POST['descricao'];
        $quantidade = intval($_POST['quantidade']);

        // Prepara a consulta de atualização
        $stmt = $conn->prepare("UPDATE produtos SET nome = ?, categoria = ?, descricao = ?, quantidade = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nome, $categoria, $descricao, $quantidade, $id);

        if ($stmt->execute()) {
            // Redireciona para a página de listagem com uma mensagem de sucesso
            header("Location: ../index.php?msg=" . urlencode("Produto atualizado com sucesso!"));
            exit();
        } else {
            echo "Erro ao atualizar produto: " . htmlspecialchars($stmt->error);
        }

        // Fecha a statement
        $stmt->close();
    } else {
        echo "ID do produto não especificado ou inválido.";
    }
} else {
    echo "Método de requisição inválido.";
}

// Fecha a conexão
$conn->close();
?>
