<?php
// Exemplo de listagem de produtos (listar_produtos.php)

include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

$sql = "SELECT id, nome FROM produtos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Lista de Produtos</h2>";
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<strong>ID:</strong> " . $row["id"]. " - <strong>Nome:</strong> " . $row["nome"];
        echo " <a href='editar_produto.php?id=" . $row["id"] . "'>Editar</a>";
        echo " <a href='excluir_produto.php?id=" . $row["id"] . "'>Excluir</a>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum produto encontrado.";
}

// Fecha a conexão
$conn->close();

