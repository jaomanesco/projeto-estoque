<?php
include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.php');
    exit();
}

// Verifica se foi passado um ID via GET e se é um número inteiro válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Converte para inteiro

    // Prepara a query utilizando prepared statements para prevenir SQL Injection
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Executa a query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Redireciona para a página index.php com uma mensagem de sucesso
            header('Location: ../index.php?msg=Produto excluído com sucesso!');
        } else {
            // Redireciona para a página index.php com uma mensagem de erro se o produto não foi encontrado
            header('Location: ../index.php?msg=Produto com ID ' . $id . ' não encontrado.');
        }
    } else {
        // Redireciona para a página index.php com uma mensagem de erro se houver um erro na execução
        header('Location: ../index.php?msg=Erro ao excluir produto: ' . $stmt->error);
    }

    // Fecha a statement
    $stmt->close();
} else {
    // Redireciona para a página index.php com uma mensagem de erro se o ID não for especificado ou inválido
    header('Location: ../index.php?msg=ID do produto não especificado ou inválido.');
}

// Fecha a conexão
$conn->close();
