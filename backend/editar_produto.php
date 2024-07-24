<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
</head>
<body>
    <h2>Editar Produto</h2>
    <?php
    include 'config.php'; // Arquivo de configuração com a conexão ao banco de dados

    // Verifica se foi passado um ID via GET
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Obtém os dados do produto pelo ID
        $sql = "SELECT id, nome, descricao, quantidade, preco FROM produtos WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
    ?>
            <form action="processar_atualizacao.php" method="post">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $row['nome']; ?>" required><br><br>
                
                <label for="descricao">Descrição:</label><br>
                <textarea id="descricao" name="descricao" rows="4" cols="50"><?php echo $row['descricao']; ?></textarea><br><br>
                
                <label for="quantidade">Quantidade:</label>
                <input type="number" id="quantidade" name="quantidade" value="<?php echo $row['quantidade']; ?>" required><br><br>
                
                <label for="preco">Preço:</label>
                <input type="text" id="preco" name="preco" value="<?php echo $row['preco']; ?>" required><br><br>
                
                <input type="submit" value="Atualizar Produto">
            </form>
    <?php
        } else {
            echo "Produto não encontrado.";
        }
    } else {
        echo "ID do produto não especificado.";
    }

    // Fecha a conexão
    $conn->close();
    ?>
</body>
</html>
