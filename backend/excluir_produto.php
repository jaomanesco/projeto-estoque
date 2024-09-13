<?php
include 'config.php';

session_start();

header('Content-Type: application/json'); // Define o tipo de conteúdo como JSON

if (!isset($_SESSION['usuario_logado'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não está logado.']);
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Produto não encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'ID do produto não especificado ou inválido.']);
}

$conn->close();
