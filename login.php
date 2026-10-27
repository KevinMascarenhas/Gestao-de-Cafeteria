<?php
// login.php - Tela de login do gerente

session_start();

// Se já estiver logado, redireciona para o painel
if (isset($_SESSION['usuario_logado'])) {
    header("Location: painel.php");
    exit;
}

require_once 'conexao.php';

$erro = '';

// Recebe o parâmetro de erro da URL (opcional)
if (isset($_GET['erro'])) {
    if ($_GET['erro'] == 'acesso_negado') {
        $erro = "Você precisa fazer login para acessar o painel.";
    } elseif ($_GET['erro'] == 'senha_errada') {
        $erro = "Usuário ou senha incorretos.";
    }
}

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($usuario) || empty($senha)) {
        $erro = "Preencha usuário e senha.";
    } else {
        // Consulta segura com Prepared Statement (protege contra SQL Injection)
        $stmt = mysqli_prepare($conn, "SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $nome, $usuario_db, $senha_hash);
            mysqli_stmt_fetch($stmt);

            // Verifica a senha (neste exemplo, comparação direta)
            // Em produção use password_hash() e password_verify()
            if ($senha === $senha_hash) {
                // Login bem-sucedido: cria a sessão
                $_SESSION['usuario_logado'] = true;
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario'] = $usuario_db;

                header("Location: painel.php");
                exit;
            } else {
                $erro = "Usuário ou senha incorretos.";
            }
        } else {
            $erro = "Usuário ou senha incorretos.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão do Cardápio</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6f4e37, #3e2723);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            color: #3e2723;
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
        }

        .login-card h2 i {
            /* placeholder para ícone, caso queira adicionar */
        }

        .campo {
            margin-bottom: 20px;
        }

        .campo label {
            display: block;
            margin-bottom: 6px;
            color: #5d4037;
            font-weight: 600;
            font-size: 14px;
        }

        .campo input {
            width: 100%;
            padding: 12px;
            border: 2px solid #d7ccc8;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .campo input:focus {
            outline: none;
            border-color: #8d6e63;
        }

        .erro {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #ef9a9a;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #6f4e37;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #5d4037;
        }

        .info {
            margin-top: 25px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 6px;
            color: #2e7d32;
            font-size: 13px;
            text-align: center;
            border: 1px solid #a5d6a7;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Painel da Cafeteria</h2>
        <h2 style="font-size: 16px; font-weight: normal; color: #8d6e63; margin-bottom: 25px;">Acesso do Gerente</h2>

        <?php if ($erro): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="campo">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Ex: admin" autocomplete="username">
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="*******" autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Entrar no Painel</button>
        </form>
    </div>
</body>
</html>