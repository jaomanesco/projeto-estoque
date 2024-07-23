<?php
include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];

    // Insere os dados no banco de dados
    $sql = "INSERT INTO produtos (nome, descricao, quantidade, preco) VALUES ('$nome', '$descricao', '$quantidade', '$preco')";

    if ($conn->query($sql) === TRUE) {
        echo "Produto inserido com sucesso!";
    } else {
        echo "Erro ao inserir produto: " . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
}
?>
