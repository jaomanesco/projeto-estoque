<?php
include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

// Verifica se foi passado um ID via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Exclui o produto do banco de dados
    $sql = "DELETE FROM produtos WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Produto excluído com sucesso!";
    } else {
        echo "Erro ao excluir produto: " . $conn->error;
    }
} else {
    echo "ID do produto não especificado.";
}

// Fecha a conexão
$conn->close();



