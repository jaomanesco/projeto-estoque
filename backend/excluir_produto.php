<?php
include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

// Verifica se foi passado um ID via GET e se é um número inteiro válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Converte para inteiro

    // Prepara a query utilizando prepared statements para prevenir SQL Injection
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Executa a query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Produto excluído com sucesso!";
        } else {
            echo "Produto com ID $id não encontrado.";
        }
    } else {
        echo "Erro ao excluir produto: " . $stmt->error;
    }

    // Fecha a statement
    $stmt->close();
} else {
    echo "ID do produto não especificado ou inválido.";
}

// Fecha a conexão
$conn->close();