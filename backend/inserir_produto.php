<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Produto</title>
</head>
<body>
<button class="back-button" onclick="window.history.back();">
    <i class="fas fa-arrow-left"></i>
</button>
    <h2>Inserir Novo Produto</h2>
    <form action="processar_insercao.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>
        
        <label for="categoria">Categoria:</label>
        <input type="text" id="categoria" name="categoria" required><br><br>
        
        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="4" cols="50"></textarea><br><br>
        
        <label for="observacoes">Observações:</label><br>
        <textarea id="observacoes" name="observacoes" rows="4" cols="50"></textarea><br><br>
        
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" required><br><br>
        
        <input type="submit" value="Inserir Produto">
    </form>
</body>
</html>
