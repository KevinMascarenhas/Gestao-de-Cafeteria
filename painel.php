<?php
// painel.php - Página principal do painel administrativo (protegida)

require_once 'auth.php'; // Bloqueia acesso se não estiver logado
require_once 'conexao.php';

$mensagem = '';
$tipo_mensagem = ''; // 'sucesso' ou 'erro'

// =====================
// CREATE - Cadastrar item
// =====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao_cadastrar'])) {
    $nome_item = trim($_POST['nome_item'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';

    // Validação básica
    if (empty($nome_item) || empty($categoria) || empty($preco)) {
        $mensagem = "Preencha todos os campos obrigatórios (Nome, Categoria e Preço).";
        $tipo_mensagem = 'erro';
    } elseif (!is_numeric($preco) || $preco < 0) {
        $mensagem = "Preço inválido.";
        $tipo_mensagem = 'erro';
    } else {
        // Prepared Statement para INSERT (protege contra SQL Injection)
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO cardapio (nome_item, categoria, descricao, preco, disponivel) VALUES (?, ?, ?, ?, TRUE)");
        mysqli_stmt_bind_param($stmt, "ssds", $nome_item, $categoria, $descricao, $preco);

        if (mysqli_stmt_execute($stmt)) {
            $mensagem = "Item cadastrado com sucesso!";
            $tipo_mensagem = 'sucesso';
        } else {
            $mensagem = "Erro ao cadastrar: " . mysqli_error($conn);
            $tipo_mensagem = 'erro';
        }

        mysqli_stmt_close($stmt);
    }
}

// =====================
// READ - Listar itens
// =====================
$itens = [];
$result = mysqli_query($conn, "SELECT id, nome_item, categoria, descricao, preco, disponivel FROM cardapio ORDER BY id DESC");

if ($result) {
    while ($linha = mysqli_fetch_assoc($result)) {
        $itens[] = $linha;
    }
    mysqli_free_result($result);
} else {
    $mensagem = "Erro ao listar itens: " . mysqli_error($conn);
    $tipo_mensagem = 'erro';
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Gestão do Cardápio</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        /* Cabeçalho */
        header {
            background: #3e2723;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        header h1 {
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        header .usuario {
            font-size: 14px;
            opacity: 0.9;
        }

        header a {
            color: #fff;
            text-decoration: none;
            background: #8d6e63;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            transition: background 0.3s;
        }

        header a:hover {
            background: #6f4e37;
        }

        /* Conteúdo */
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Mensagens */
        .mensagem {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .mensagem.sucesso {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .mensagem.erro {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        /* Grid: formulário à esquerda, tabela à direita (ou um abaixo do outro em mobile) */
        .layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        /* Card do formulário */
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 25px;
        }

        .card h3 {
            margin-bottom: 20px;
            color: #3e2723;
            font-size: 18px;
            border-bottom: 2px solid #d7ccc8;
            padding-bottom: 10px;
        }

        .campo {
            margin-bottom: 18px;
        }

        .campo label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 14px;
            color: #5d4037;
        }

        .campo input,
        .campo select,
        .campo textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #d7ccc8;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .campo input:focus,
        .campo select:focus,
        .campo textarea:focus {
            outline: none;
            border-color: #8d6e63;
        }

        .campo textarea {
            resize: vertical;
            min-height: 80px;
        }

        .campo small {
            font-size: 12px;
            color: #8d6e63;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-cadastrar {
            width: 100%;
            background: #6f4e37;
            color: #fff;
        }

        .btn-cadastrar:hover {
            background: #5d4037;
        }

        /* Tabela */
        .tabela-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background: #3e2723;
            color: #fff;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        tr:hover {
            background: #f1e8e2;
        }

        .preco {
            font-weight: 600;
            color: #6f4e37;
        }

        .disponivel {
            color: #2e7d32;
            font-weight: 600;
            font-size: 13px;
        }

        .indisponivel {
            color: #c62828;
            font-weight: 600;
            font-size: 13px;
        }

        .acoes {
            display: flex;
            gap: 8px;
        }

        .btn-editar {
            background: #1565c0;
            color: #fff;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-editar:hover {
            background: #0d47a1;
        }

        .btn-excluir {
            background: #c62828;
            color: #fff;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-excluir:hover {
            background: #b71c1c;
        }

        .btn-excluir:not(:disabled):hover {
            opacity: 0.9;
        }

        .btn-excluir:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .vazio {
            text-align: center;
            padding: 40px;
            color: #8d6e63;
        }

        footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            color: #8d6e63;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cardápio da Cafeteria</h1>
        <div class="usuario">
            Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Gerente'); ?> | 
            <a href="logout.php">Sair</a>
        </div>
    </header>

    <div class="container">
        <?php if ($mensagem): ?>
            <div class="mensagem <?php echo $tipo_mensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="layout">
            <!-- Formulário de Cadastro (Create) -->
            <div class="card">
                <h3>Cadastrar Novo Item</h3>
                <form method="POST" action="painel.php">
                    <input type="hidden" name="acao_cadastrar" value="1">

                    <div class="campo">
                        <label for="nome_item">Nome do Item *</label>
                        <input type="text" id="nome_item" name="nome_item" required 
                               placeholder="Ex: Cappuccino" maxlength="100">
                    </div>

                    <div class="campo">
                        <label for="categoria">Categoria *</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Café">Café</option>
                            <option value="Doce">Doce</option>
                            <option value="Salgado">Salgado</option>
                            <option value="Bebida Gelada">Bebida Gelada</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" placeholder="Descreva o item..."></textarea>
                    </div>

                    <div class="campo">
                        <label for="preco">Preço (R$) *</label>
                        <input type="number" id="preco" name="preco" step="0.01" min="0" required 
                               placeholder="Ex: 12,50">
                        <small>Use ponto para decimais (ex: 12.50)</small>
                    </div>

                    <button type="submit" class="btn btn-cadastrar">Cadastrar Item</button>
                </form>
            </div>

            <!-- Lista de Itens (Read) -->
            <div class="card">
                <h3>Itens Cadastrados (<?php echo count($itens); ?>)</h3>

                <?php if (empty($itens)): ?>
                    <div class="vazio">
                        Nenhum item cadastrado ainda. Cadastre o primeiro item acima!
                    </div>
                <?php else: ?>
                    <div class="tabela-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Nome do Item</th>
                                    <th style="width: 140px;">Categoria</th>
                                    <th style="width: 220px;">Descrição</th>
                                    <th style="width: 100px;">Preço</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 160px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itens as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                                        <td><?php echo htmlspecialchars($item['nome_item']); ?></td>
                                        <td><?php echo htmlspecialchars($item['categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($item['descricao'] ?? '-'); ?></td>
                                        <td class="preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($item['disponivel']): ?>
                                                <span class="disponivel">Disponível</span>
                                            <?php else: ?>
                                                <span class="indisponivel">Indisponível</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="acoes">
                                            <a href="editar_item.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-editar">Editar</a>
                                            <a href="excluir_item.php?id=<?php echo (int)$item['id']; ?>" 
                                               class="btn btn-excluir" 
                                               onclick="return confirm('Tem certeza que deseja excluir este item?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            Sistema de Gestão do Cardápio - Cafeteria Local &copy; 2026
        </footer>
    </div>
</body>
</html>