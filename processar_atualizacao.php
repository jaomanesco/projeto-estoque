<?php
include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];

    // Atualiza os dados no banco de dados
    $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', quantidade='$quantidade', preco='$preco' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Produto atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar produto: " . $conn->error;
    }

    // Fecha a conexão
    $conn->close();
}
