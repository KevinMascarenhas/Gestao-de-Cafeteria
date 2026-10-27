<?php
require_once 'auth.php';
require_once 'conexao.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao_cadastrar'])) {
    $nome_item = trim($_POST['nome_item'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? '';

    if (empty($nome_item) || empty($categoria) || empty($preco)) {
        $mensagem = "Preencha todos os campos obrigatorios (Nome, Categoria e Preco).";
        $tipo_mensagem = 'erro';
    } elseif (!is_numeric($preco) || $preco < 0) {
        $mensagem = "Preco invalido.";
        $tipo_mensagem = 'erro';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO cardapio (nome_item, categoria, descricao, preco, disponivel) VALUES (?, ?, ?, ?, TRUE)");
        mysqli_stmt_bind_param($stmt, "ssds", $nome_item, $categoria, $descricao, $preco);

        if (mysqli_stmt_execute($stmt)) {
            $mensagem = "Item cadastrado com sucesso.";
            $tipo_mensagem = 'sucesso';
        } else {
            $mensagem = "Erro ao cadastrar: " . mysqli_error($conn);
            $tipo_mensagem = 'erro';
        }

        mysqli_stmt_close($stmt);
    }
}

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
    <title>Painel - Cardapio</title>
    <style>
        :root {
            --cafe-escuro: #2d1b10;
            --cafe-medio: #5d4037;
            --cafe-claro: #d7ccc8;
            --creme: #faf6f0;
            --branco: #ffffff;
            --texto: #3e2723;
            --texto-suave: #6f4e37;
            --borda: #e8dfd4;
            --sucesso: #2e7d32;
            --erro: #a33333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--creme);
            color: var(--texto);
            line-height: 1.5;
        }

        header {
            background: var(--cafe-escuro);
            color: #fff;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        header .usuario {
            font-size: 14px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        header a {
            color: #fff;
            text-decoration: none;
            background: var(--cafe-medio);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            transition: background 0.2s;
        }

        header a:hover {
            background: var(--cafe-escuro);
        }

        .container {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px;
        }

        .mensagem {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .mensagem.sucesso {
            background: #f0f7f0;
            color: var(--sucesso);
            border-color: var(--sucesso);
        }

        .mensagem.erro {
            background: #fbf0f0;
            color: var(--erro);
            border-color: var(--erro);
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--branco);
            border-radius: 12px;
            padding: 28px;
            border: 1px solid var(--borda);
        }

        .card h3 {
            margin-bottom: 22px;
            color: var(--cafe-escuro);
            font-size: 18px;
            font-weight: 700;
            border-bottom: 1px solid var(--borda);
            padding-bottom: 12px;
        }

        .campo {
            margin-bottom: 18px;
        }

        .campo label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--texto-suave);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .campo input,
        .campo select,
        .campo textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #d9cec0;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            transition: border-color 0.2s;
        }

        .campo input:focus,
        .campo select:focus,
        .campo textarea:focus {
            outline: none;
            border-color: var(--cafe-medio);
        }

        .campo textarea {
            resize: vertical;
            min-height: 90px;
        }

        .campo small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: var(--texto-suave);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-cadastrar {
            width: 100%;
            background: var(--cafe-medio);
            color: #fff;
        }

        .btn-cadastrar:hover {
            background: var(--cafe-escuro);
        }

        .tabela-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--branco);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--borda);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--borda);
        }

        th {
            background: var(--cafe-escuro);
            color: #fff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        tr:nth-child(even) {
            background: #fcf8f2;
        }

        tr:hover {
            background: #f3e9dd;
        }

        .preco {
            font-weight: 700;
            color: var(--cafe-medio);
        }

        .status {
            font-size: 12px;
            font-weight: 700;
        }

        .disponivel {
            color: var(--sucesso);
        }

        .indisponivel {
            color: var(--erro);
        }

        .acoes {
            display: flex;
            gap: 8px;
        }

        .btn-editar {
            background: var(--cafe-medio);
            color: #fff;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-editar:hover {
            background: var(--cafe-escuro);
        }

        .btn-excluir {
            background: #e8dcd4;
            color: var(--texto);
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-excluir:hover {
            background: var(--erro);
            color: #fff;
        }

        .vazio {
            text-align: center;
            padding: 40px;
            color: var(--texto-suave);
        }

        footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            color: var(--texto-suave);
            font-size: 13px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cardapio</h1>
        <div class="usuario">
            Ola, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Gerente'); ?> |
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
            <div class="card">
                <h3>Cadastrar item</h3>
                <form method="POST" action="painel.php">
                    <input type="hidden" name="acao_cadastrar" value="1">

                    <div class="campo">
                        <label for="nome_item">Nome do item</label>
                        <input type="text" id="nome_item" name="nome_item" required placeholder="Ex: Cappuccino" maxlength="100">
                    </div>

                    <div class="campo">
                        <label for="categoria">Categoria</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="Cafe">Cafe</option>
                            <option value="Doce">Doce</option>
                            <option value="Salgado">Salgado</option>
                            <option value="Bebida Gelada">Bebida Gelada</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="descricao">Descricao</label>
                        <textarea id="descricao" name="descricao" placeholder="Descricao do item..."></textarea>
                    </div>

                    <div class="campo">
                        <label for="preco">Preco (R$)</label>
                        <input type="number" id="preco" name="preco" step="0.01" min="0" required placeholder="12.50">
                        <small>Use ponto para decimais (ex: 12.50)</small>
                    </div>

                    <button type="submit" class="btn btn-cadastrar">Cadastrar</button>
                </form>
            </div>

            <div class="card">
                <h3>Itens cadastrados (<?php echo count($itens); ?>)</h3>

                <?php if (empty($itens)): ?>
                    <div class="vazio">
                        Nenhum item cadastrado ainda. Cadastre o primeiro acima.
                    </div>
                <?php else: ?>
                    <div class="tabela-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Nome</th>
                                    <th style="width: 140px;">Categoria</th>
                                    <th style="width: 220px;">Descricao</th>
                                    <th style="width: 100px;">Preco</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 150px;">Acoes</th>
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
                                            <span class="status <?php echo $item['disponivel'] ? 'disponivel' : 'indisponivel'; ?>">
                                                <?php echo $item['disponivel'] ? 'Disponivel' : 'Indisponivel'; ?>
                                            </span>
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
            Sistema de Gestao do Cardapio - Cafeteria
        </footer>
    </div>
</body>
</html>