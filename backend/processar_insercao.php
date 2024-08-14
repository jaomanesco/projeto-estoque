<?php
include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $quantidade = $_POST['quantidade'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    // Prepara a query utilizando prepared statements para prevenir SQL Injection
    $stmt = $conn->prepare("INSERT INTO produtos (nome, quantidade, categoria, descricao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $quantidade, $categoria, $descricao);

    // Executa a query
    if ($stmt->execute()) {
        header('Location: ../index.php?msg=Produto inserido com sucesso!');
    } else {
        echo "Erro ao inserir produto: " . $stmt->error;
    }

    // Fecha a statement
    $stmt->close();

    // Fecha a conexão
    $conn->close();
}

