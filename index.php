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
            <input type="text" id="searchInput" name="search" placeholder="Buscar" value="<?php echo htmlspecialchars($searchTerm); ?>">
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
            <button type="submit">Buscar</button>
            <div class="add-button-container-ins">
                <button id="openInsertModal" class="add-button-ins">Inserir</button>
            </div>
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
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = htmlspecialchars($row["id"]);
                        $nome = htmlspecialchars($row["nome"]);
                        $quantidade = htmlspecialchars($row["quantidade"]);
                        $categoria = htmlspecialchars($row["categoria"]);
                        echo "<tr>
                                <td>$nome</td>
                                <td>$quantidade</td>
                                <td>$categoria</td>
                                <td>
                                    <button class='btn-editar'>editar</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum produto encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <!-- Modal para editar produto -->
        <div id="editModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2>Detalhes</h2>
                <form id="editForm">
                    <div class="form-group">
                        <label for="editNome">Nome</label>
                        <input type="text" id="editNome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="editQuantidade">Quantidade</label>
                        <input type="number" id="editQuantidade" name="quantidade" required>
                    </div>
                    <div class="form-group">
                        <label for="editCategoria">Categoria</label>
                        <input type="text" id="editCategoria" name="categoria" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescricao">Descrição</label>
                        <textarea id="texte" name="story" rows="5" cols="33"></textarea>
                    </div>
                    <div class="button-container">
                        <input type="submit" value="Salvar">
                        <input type="submit" value="Excluir">
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Inserir Produto -->
        <div id="insertModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" id="closeInsertModal">&times;</span>
                <h2>Adicionar item</h2>
                <form id="insertForm">
                    <div class="form-group">
                        <label for="insertNome">Nome:</label>
                        <input type="text" id="insertNome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="insertQuantidade">Quantidade:</label>
                        <input type="number" id="insertQuantidade" name="quantidade" required>

                        <label for="insertCategoria">Categoria:</label>
                        <input type="text" id="insertCategoria" name="categoria" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescricao">Descrição</label>
                        <textarea id="texte" name="story" rows="5" cols="33"></textarea>
                    </div>
                    <div class="button-container">
                        <input type="submit" value="Salvar">
                        <input type="submit" value="Excluir">
                </form>
            </div>
        </div>

        <a href="backend/inserir_produto.php" class="btn-adicionar">Adicionar Novo Produto</a>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Empresa XYZ</p>
            <p><a href="backend/logout.php">Logout</a></p>
        </footer>
    </div>
    <script>
        // Função para abrir o modal
        function openModal(nome, quantidade, categoria) {
            document.getElementById('editNome').value = nome;
            document.getElementById('editQuantidade').value = quantidade;
            document.getElementById('editCategoria').value = categoria;
            document.getElementById('editModal').style.display = 'flex';
        }

        // Adiciona evento de clique a cada botão "Editar"
        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', function() {
                const row = button.closest('tr');
                const nome = row.cells[0].innerText;
                const quantidade = row.cells[1].innerText;
                const categoria = row.cells[2].innerText;

                openModal(nome, quantidade, categoria);
            });
        });

        // Fecha o modal
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('editModal').style.display = 'none';
        });

        // Fecha o modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                document.getElementById('editModal').style.display = 'none';
            }
        };

        // Função para abrir o modal de inserir
        document.getElementById('openInsertModal').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('insertModal').style.display = 'flex';
        });

        // Fechar o modal de inserir
        document.getElementById('closeInsertModal').addEventListener('click', function() {
            document.getElementById('insertModal').style.display = 'none';
        });

        // Fechar o modal de inserir ao clicar fora dele
        window.onclick = function(event) {
            if (event.target == document.getElementById('insertModal')) {
                document.getElementById('insertModal').style.display = 'none';
            }
        };
    </script>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
