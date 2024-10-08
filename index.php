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
            <button id="busca" type="submit">Buscar</button>
            <div class="add-button-container-ins">
                <button type="submit" id="openInsertModal" class="add-button-ins">Inserir</button>
            </div>
        </form>
    </header>

    <div class="container">
        <table class="produtos-tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Quant.</th>
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
                        echo "<tr data-id='$id'>
                        <td>$nome</td>
                        <td>$quantidade</td>
                        <td>$categoria</td>
                        <td>
                            <button class='btn-editar'>
                                <img src='frontend/assets/info.png' alt='Editar'/>
                            </button>
                        </td>
                      </tr>";                
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum produto encontrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Editar item</h2>
        <form id="editForm">
            <div class="form-group flex-row">
                <label for="editNome">Nome:</label>
                <input type="text" id="editNome" name="nome" required>
            </div>
            <div class="form-group flex-row">
                <label for="editQuantidade">Qntd:</label>
                <input type="number" id="editQuantidade" name="quantidade" required>
                <label for="editCategoria">Grupo:</label>
                <input type="text" id="editCategoria" name="categoria" required>
            </div>
            <div class="form-group flex-row">
                <label for="editDescricao">Descrição:</label>
                <textarea id="editDescricao" name="descricao" rows="4" required></textarea>
            </div>
            <div class="button-container">
                <input type="submit" value="Salvar" class="btn-add">
                <input type="button" value="Excluir" class="btn-delete" id="btnDeleteEdit" data-id="">

            </div>
        </form>
    </div>
</div>

<div id="insertModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="closeInsertModal">&times;</span>
        <h2>Adicionar item</h2>
        <form id="insertForm">
            <div class="form-group flex-row">
                <label for="insertNome">Nome:</label>
                <input type="text" id="insertNome" name="nome" required>
            </div>
            <div class="form-group flex-row">
                <label for="insertQuantidade">Qntd:</label>
                <input type="number" class="qntd-input" name="quantidade" required>
                <label for="insertCategoria">Grupo:</label>
                <input type="text" id="insertCategoria" name="categoria" required>
            </div>
            <div class="form-group flex-row">
                <label for="editDescricao">Desc:</label>
                <textarea id="texte" name="story" rows="4" required></textarea>
            </div>
            <div class="button-container">
                <input type="submit" value="Adicionar" class="btn-add-full">
            </div>
        </form>
    </div>
</div>


    <script>
        // Adiciona evento ao botão "Excluir"
document.getElementById('btnDeleteEdit').addEventListener('click', function() {
    const id = document.getElementById('btnDeleteEdit').getAttribute('data-id'); // Obter o ID do produto
    
    if (confirm('Tem certeza que deseja excluir este produto?')) {
        fetch(`excluir_produto.php?id=${id}`, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produto excluído com sucesso!');
                // Atualize a tabela ou remova a linha correspondente
                const row = document.querySelector(`tr[data-id='${id}']`);
                if (row) {
                    row.remove(); // Remove a linha da tabela
                }
                closeModal('editModal'); // Feche o modal após a ação
            } else {
                alert(`Erro: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Ocorreu um erro ao excluir o produto.');
        });
    }
});

// Função para fechar o modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function openModal(nome, quantidade, categoria, id) {
    document.getElementById('editNome').value = nome;
    document.getElementById('editQuantidade').value = quantidade;
    document.getElementById('editCategoria').value = categoria;
    document.getElementById('btnDeleteEdit').setAttribute('data-id', id); // Adicione esta linha
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

// Fecha o modal ao clicar no "X"
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
