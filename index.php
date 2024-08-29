<?php
// Arquivo de configuração com a conexão ao banco de dados
include 'backend/config.php';

// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

// Recebe os parâmetros de filtro
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Prepara a consulta SQL com base nos filtros
$sql = "SELECT id, nome, quantidade, categoria, descricao FROM produtos WHERE 1=1";
if ($searchTerm) {
    $searchTerm = $conn->real_escape_string($searchTerm);
    $sql .= " AND nome LIKE '%$searchTerm%'";
}
if ($categoryFilter) {
    $categoryFilter = $conn->real_escape_string($categoryFilter);
    $sql .= " AND categoria = '$categoryFilter'";
}

// Executa a consulta
$result = $conn->query($sql);

// Início do HTML
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Produtos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="frontend/css/output.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="frontend/assets/logo-no-bc.png" alt="logo saine">
        </div>
        <div class="links">
            <a href="#">Chamados</a>
            <a href="#">Ativos TI</a>
            <a href="#">Estoque</a>
            <a href="#">Faq</a>
        </div>
        <div class="profile">
            <img src="frontend/assets/circulo.png" alt="perfil">
        </div>
    </div>

<header>
    <form id="filtersForm" method="GET" action="">
        <div>
            <label for="searchInput">Buscar:</label>
            <input type="text" id="searchInput" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
        </div>
        <div>
            <label for="categoryFilter">Categoria:</label>
            <select id="categoryFilter" name="category">
                <option value=""></option>
                <?php
                // Consulta para obter categorias distintas
                $categoryResult = $conn->query("SELECT DISTINCT categoria FROM produtos");
                while ($category = $categoryResult->fetch_assoc()) {
                    $categoria = htmlspecialchars($category['categoria']);
                    $selected = ($categoria == $categoryFilter) ? 'selected' : '';
                    echo "<option value='$categoria' $selected>$categoria</option>";
                }
                ?>
            </select>    
        </div>

        <button type="submit">Buscar</button>
    </form>
</header>

<div class="container">
    <table class="produtos-tabela">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Qtd.</th>
                <th>Categoria</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="productTableBody">
            <?php
            // Verifica se há produtos para listar
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = htmlspecialchars($row["id"]);
                    $nome = htmlspecialchars($row["nome"]);
                    $quantidade = htmlspecialchars($row["quantidade"]);
                    $categoria = htmlspecialchars($row["categoria"]);
                    echo "<tr>";
                    echo "<td>$nome</td>";
                    echo "<td>$quantidade</td>";
                    echo "<td>$categoria</td>";
                    echo "<td>";
                    echo "<a href='#' class='btn-editar' data-id='$id' data-nome='$nome' data-categoria='$categoria' data-descricao='" . htmlspecialchars($row["descricao"]) . "' data-quantidade='$quantidade'><img src='frontend/assets/lapis.png' alt='Editar' /></a>";
                    echo "<a href='#' class='btn-excluir' data-id='$id' data-nome='$nome'> </a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum produto encontrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="backend/inserir_produto.php" class="btn-adicionar">Adicionar Novo Produto</a>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Empresa XYZ</p>
        <p><a href="backend/logout.php">Logout</a></p>
    </footer>
</div>

<!-- Modal para Editar Produto -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Produto</h2>
        <form id="editProductForm" method="post" action="backend/processar_atualizacao.php">
            <input type="hidden" id="modal-id" name="id">
            
            <label for="modal-nome">Nome:</label>
            <input type="text" id="modal-nome" name="nome" required><br><br>
            
            <label for="modal-categoria">Categoria:</label>
            <input type="text" id="modal-categoria" name="categoria" required><br><br>
            
            <label for="modal-descricao">Descrição:</label><br>
            <textarea id="modal-descricao" name="descricao" rows="4" cols="50"></textarea><br><br>
            
            <label for="modal-quantidade">Quantidade:</label>
            <input type="number" id="modal-quantidade" name="quantidade" required><br><br>
            
            <input type="submit" value="Atualizar Produto">
        </form>
    </div>
</div>

<script>
    // Obtém o modal e o botão de fechar
    var modal = document.getElementById("editProductModal");
    var span = document.getElementsByClassName("close")[0];

    // Função para abrir o modal e preencher os campos
    function openEditModal(id, nome, categoria, descricao, quantidade) {
        document.getElementById("modal-id").value = id;
        document.getElementById("modal-nome").value = nome;
        document.getElementById("modal-categoria").value = categoria;
        document.getElementById("modal-descricao").value = descricao;
        document.getElementById("modal-quantidade").value = quantidade;
        
        modal.style.display = "block";
    }

    // Função para fechar o modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Fecha o modal se o usuário clicar fora do modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Adiciona evento de clique aos botões de edição
    document.querySelectorAll(".btn-editar").forEach(button => {
        button.onclick = function(event) {
            event.preventDefault();
            // Obtém os dados do produto
            var id = this.getAttribute("data-id");
            var nome = this.getAttribute("data-nome");
            var categoria = this.getAttribute("data-categoria");
            var descricao = this.getAttribute("data-descricao");
            var quantidade = this.getAttribute("data-quantidade");
            
            // Abre o modal e preenche os dados
            openEditModal(id, nome, categoria, descricao, quantidade);
        }
    });
</script>

</body>

</html>

<?php
// Fecha a conexão
$conn->close();
?>
