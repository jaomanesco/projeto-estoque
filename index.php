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
$params = [];
if ($searchTerm) {
    $sql .= " AND nome LIKE ?";
    $params[] = "%" . $searchTerm . "%";
}
if ($categoryFilter) {
    $sql .= " AND categoria = ?";
    $params[] = $categoryFilter;
}

// Prepara e executa a consulta
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
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
            <a href="#" class="active">Estoque</a>
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
                <option value="" disabled selected hidden>Filtrar</option>
                <option value="">Todos</option>
                <?php
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
                <button type="button" id="openModal" class="add-button-ins">Inserir</button>
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
                        $descricao = htmlspecialchars($row["descricao"]);
                        echo "<tr data-id='$id' data-descricao='$descricao'>
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

        <!-- Modal para Adicionar/Editar -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2 id="modalTitle">Adicionar item</h2>
                <form id="modalForm">
                    <div class="form-group">
                        <label for="modalNome">Nome</label>
                        <input type="text" id="modalNome" name="nome" required>
                    </div>
                    <div class="flex-row">
                        <div class="form-group">
                            <label for="modalQuantidade">Qntda</label>
                            <input type="number" id="modalQuantidade" name="quantidade" required>
                        </div>
                        <div class="form-group">
                            <label for="modalCategoria">Grupo</label>
                            <input type="text" id="modalCategoria" name="categoria" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="modalDescricao">Descr.</label>
                        <textarea id="modalDescricao" name="descricao" rows="4" required></textarea>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn-submit btn-add">Adicionar</button>
                        <button type="button" class="btn-delete" id="btnDelete" style="display: none;">Excluir</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div id="confirmDeletePopup" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" id="closeConfirmDeletePopup">&times;</span>
                <h2>Confirmar Exclusão</h2>
                <p>Você realmente deseja excluir este produto? Esta ação não pode ser desfeita.</p>
                <div class="button-container">
                    <button id="confirmDeleteButton" class="btn-delete">Sim, excluir</button>
                    <button id="cancelDeleteButton" class="btn-cancel">Cancelar</button>
                </div>
            </div>
        </div>

        <div id="message" style="display: none;"></div> <!-- Mensagem de feedback -->

        <script>
            const modal = document.getElementById('modal');
            const confirmDeletePopup = document.getElementById('confirmDeletePopup');
            const modalTitle = document.getElementById('modalTitle');
            const modalForm = document.getElementById('modalForm');
            const btnDelete = document.getElementById('btnDelete');
            const btnSubmit = modalForm.querySelector('.btn-submit');
            let editingId = null;

            // Mensagem de confirmação
            function showMessage(message) {
                const messageDiv = document.getElementById('message');
                messageDiv.textContent = message;
                messageDiv.style.display = 'block';

                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 3000);
            }

            // Abre o modal para adicionar
            document.getElementById('openModal').addEventListener('click', function() {
                modalTitle.innerText = 'Adicionar item';
                btnSubmit.innerText = 'Adicionar';
                modalForm.reset();
                btnDelete.style.display = 'none';
                editingId = null;
                modal.style.display = 'flex';
            });

            // Evento para fechar o modal
            document.getElementById('closeModal').addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Fecha o modal ao clicar fora dele
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Evento para editar produto
            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const nome = row.cells[0].innerText;
                    const quantidade = row.cells[1].innerText;
                    const categoria = row.cells[2].innerText;
                    const descricao = row.dataset.descricao;
                    editingId = row.dataset.id;

                    // Preencher o modal com os dados do produto
                    modalTitle.innerText = 'Detalhes';
                    btnSubmit.innerText = 'Editar';
                    document.getElementById('modalNome').value = nome;
                    document.getElementById('modalQuantidade').value = quantidade;
                    document.getElementById('modalCategoria').value = categoria;
                    document.getElementById('modalDescricao').value = descricao;

                    btnDelete.style.display = 'inline-block';
                    modal.style.display = 'flex';
                });
            });

            // Lógica para abrir o popup de confirmação de exclusão
            btnDelete.addEventListener('click', function() {
                confirmDeletePopup.style.display = 'flex';
            });

            // Evento para confirmar a exclusão
            document.getElementById('confirmDeleteButton').addEventListener('click', function() {
                fetch(`backend/excluir_produto.php?id=${editingId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modal.style.display = 'none';
                        confirmDeletePopup.style.display = 'none';
                        showMessage('Produto excluído com sucesso!');
                        location.reload();
                    } else {
                        showMessage('Erro ao excluir o produto.');
                    }
                });
            });

            // Evento para fechar o popup de confirmação
            document.getElementById('cancelDeleteButton').addEventListener('click', function() {
                confirmDeletePopup.style.display = 'none';
            });

            // Fechar o popup de confirmação ao clicar no "X"
            document.getElementById('closeConfirmDeletePopup').addEventListener('click', function() {
                confirmDeletePopup.style.display = 'none';
            });
        </script>
    </div>
</body>
</html>
