<?php
include 'config.php'; // Inclua o arquivo de configuração com a conexão ao banco de dados

// Verifica se o ID do produto foi passado
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Converte para inteiro

    // Prepara e executa a consulta para obter os dados do produto
    $stmt = $conn->prepare("SELECT nome, categoria, descricao, quantidade FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    // Verifica se o produto foi encontrado
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($nome, $categoria, $descricao, $quantidade);
        $stmt->fetch();
    } else {
        echo "Produto não encontrado.";
        exit();
    }

    // Fecha a statement
    $stmt->close();
} else {
    echo "ID do produto não especificado ou inválido.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h2>Editar Produto</h2>
    <form action="processar_atualizacao.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required><br><br>
        
        <label for="categoria">Categoria:</label>
        <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($categoria); ?>" required><br><br>
        
        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="4" cols="50"><?php echo htmlspecialchars($descricao); ?></textarea><br><br>
        
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" value="<?php echo htmlspecialchars($quantidade); ?>" required><br><br>
        
        <input type="submit" value="Atualizar Produto">
    </form>
</body>
</html>
