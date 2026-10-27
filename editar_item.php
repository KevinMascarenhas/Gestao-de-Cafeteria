<?php
require_once 'auth.php';
require_once 'conexao.php';

$id = $_GET['id'] ?? '';

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: painel.php?erro=id_invalido");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, nome_item, categoria, descricao, preco, disponivel FROM cardapio WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    header("Location: painel.php?erro=item_nao_encontrado");
    exit;
}

$item = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Item - Cardápio</title>
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
            padding: 32px 20px;
            line-height: 1.5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        header {
            background: var(--cafe-escuro);
            color: #fff;
            padding: 18px 24px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 20px;
            font-weight: 700;
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

        .card {
            background: var(--branco);
            border-radius: 0 0 12px 12px;
            padding: 28px;
            border: 1px solid var(--borda);
            border-top: none;
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

        .botoes {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
        }

        .btn-atualizar {
            background: var(--cafe-medio);
            color: #fff;
        }

        .btn-atualizar:hover {
            background: var(--cafe-escuro);
        }

        .btn-cancelar {
            background: #e8dcd4;
            color: var(--texto);
        }

        .btn-cancelar:hover {
            background: #d9cec0;
        }

        .erro {
            background: #fbf0f0;
            color: #a33333;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar item</h1>
            <a href="painel.php">Voltar ao painel</a>
        </header>

        <div class="card">
            <h3>Atualizar dados do item #<?php echo htmlspecialchars($item['id']); ?></h3>

            <form method="POST" action="atualizar_item.php">
                <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">

                <div class="campo">
                    <label for="nome_item">Nome do item</label>
                    <input type="text" id="nome_item" name="nome_item" required
                           value="<?php echo htmlspecialchars($item['nome_item']); ?>" maxlength="100">
                </div>

                <div class="campo">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria" required>
                        <option value="Cafe" <?php echo $item['categoria'] === 'Cafe' ? 'selected' : ''; ?>>Café</option>
                        <option value="Doce" <?php echo $item['categoria'] === 'Doce' ? 'selected' : ''; ?>>Doce</option>
                        <option value="Salgado" <?php echo $item['categoria'] === 'Salgado' ? 'selected' : ''; ?>>Salgado</option>
                        <option value="Bebida Gelada" <?php echo $item['categoria'] === 'Bebida Gelada' ? 'selected' : ''; ?>>Bebida Gelada</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($item['descricao'] ?? ''); ?></textarea>
                </div>

                <div class="campo">
                    <label for="preco">Preço (R$)</label>
                    <input type="number" id="preco" name="preco" step="0.01" min="0" required
                           value="<?php echo htmlspecialchars($item['preco']); ?>">
                    <small>Use ponto para decimais (ex: 12.50)</small>
                </div>

                <div class="campo">
                    <label for="disponivel">Status</label>
                    <select id="disponivel" name="disponivel">
                        <option value="TRUE" <?php echo $item['disponivel'] == 1 || $item['disponivel'] === true ? 'selected' : ''; ?>>Disponível</option>
                        <option value="FALSE" <?php echo $item['disponivel'] == 0 || $item['disponivel'] === false ? 'selected' : ''; ?>>Indisponível</option>
                    </select>
                </div>

                <div class="botoes">
                    <button type="submit" class="btn btn-atualizar">Atualizar item</button>
                    <a href="painel.php" class="btn btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>