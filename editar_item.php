<?php
// editar_item.php - Formulário de edição pré-preenchido (Update - leitura)

require_once 'auth.php';
require_once 'conexao.php';

// Recebe o ID via URL (?id=X)
$id = $_GET['id'] ?? '';

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: painel.php?erro=id_invalido");
    exit;
}

// Busca o item pelo ID usando Prepared Statement (protege contra SQL Injection)
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
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
            padding: 30px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        header {
            background: #3e2723;
            color: #fff;
            padding: 15px 25px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 20px;
        }

        header a {
            color: #fff;
            text-decoration: none;
            background: #8d6e63;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
        }

        header a:hover {
            background: #6f4e37;
        }

        .card {
            background: #fff;
            border-radius: 0 0 8px 8px;
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

        .botoes {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn-atualizar {
            background: #6f4e37;
            color: #fff;
        }

        .btn-atualizar:hover {
            background: #5d4037;
        }

        .btn-cancelar {
            background: #757575;
            color: #fff;
        }

        .btn-cancelar:hover {
            background: #616161;
        }

        .erro {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Item</h1>
            <a href="painel.php">Voltar ao Painel</a>
        </header>

        <div class="card">
            <h3>Atualizar dados do item #<?php echo htmlspecialchars($item['id']); ?></h3>

            <form method="POST" action="atualizar_item.php">
                <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">

                <div class="campo">
                    <label for="nome_item">Nome do Item *</label>
                    <input type="text" id="nome_item" name="nome_item" required 
                           value="<?php echo htmlspecialchars($item['nome_item']); ?>" maxlength="100">
                </div>

                <div class="campo">
                    <label for="categoria">Categoria *</label>
                    <select id="categoria" name="categoria" required>
                        <option value="Café" <?php echo $item['categoria'] === 'Café' ? 'selected' : ''; ?>>Café</option>
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
                    <label for="preco">Preço (R$) *</label>
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
                    <button type="submit" class="btn btn-atualizar">Atualizar Item</button>
                    <a href="painel.php" class="btn btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>