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
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Listagem de Produtos</h1>
        </header>
        <main>
            <section>
                <h2>Filtros:</h2>
                <div class="filters">
                    <form id="filtersForm" method="GET" action="">
                        <input type="text" id="searchInput" name="search" placeholder="Buscar por nome..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <select id="categoryFilter" name="category">
                            <option value="">Todas as Categorias</option>
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
                        <button type="submit">Filtrar</button>
                    </form>
                </div>
                <h2>Produtos Disponíveis:</h2>
                <table class="produtos-tabela">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Nome</th>
                            <th>Quantidade</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
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
                                $descricao = htmlspecialchars($row["descricao"]);
                                echo "<tr>";
                                echo "<td>$id</td>";
                                echo "<td>$nome</td>";
                                echo "<td>$quantidade</td>";
                                echo "<td>$categoria</td>";
                                echo "<td>$descricao</td>";
                                echo "<td>";
                                echo "<a href='backend/editar_produto.php?id=$id' class='btn-editar'>Editar</a> ";
                                echo "<a href='#' class='btn-excluir' data-id='$id' data-nome='$nome'>Excluir</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Nenhum produto encontrado.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <a href="backend/inserir_produto.php" class="btn-adicionar">Adicionar Novo Produto</a>
        </main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Empresa XYZ</p>
            <p><a href="backend/logout.php">Logout</a></p>
        </footer>
    </div>

    <!-- Script para confirmar exclusão com SweetAlert2 -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Função para confirmar exclusão com SweetAlert2
            document.querySelectorAll('.btn-excluir').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const id = this.getAttribute('data-id');
                    const nome = this.getAttribute('data-nome');

                    Swal.fire({
                        title: 'Tem certeza?',
                        text: `Você realmente deseja excluir o produto '${nome}'?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `backend/excluir_produto.php?id=${id}`;
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

<?php
// Fecha a conexão
$conn->close();
?>
