<?php
include 'config.php';
session_start();

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

// Verifica se os dados foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $quantidade = $_POST['quantidade'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];

    // Validações básicas
    if ($nome && $quantidade && $categoria && $descricao) {
        $stmt = $conn->prepare("INSERT INTO produtos (nome, quantidade, categoria, descricao) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $nome, $quantidade, $categoria, $descricao);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao adicionar produto.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Todos os campos são obrigatórios.']);
    }
}
