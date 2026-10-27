<?php
session_start();

if (isset($_SESSION['usuario_logado'])) {
    header("Location: painel.php");
    exit;
}

require_once 'conexao.php';

$erro = '';

if (isset($_GET['erro'])) {
    if ($_GET['erro'] == 'acesso_negado') {
        $erro = "Você precisa fazer login para acessar o painel.";
    } elseif ($_GET['erro'] == 'senha_errada') {
        $erro = "Usuário ou senha incorretos.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($usuario) || empty($senha)) {
        $erro = "Preencha usuário e senha.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $nome, $usuario_db, $senha_hash);
            mysqli_stmt_fetch($stmt);

            if ($senha === $senha_hash) {
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
        :root {
            --cafe-escuro: #2d1b10;
            --cafe-medio: #5d4037;
            --cafe-claro: #d7ccc8;
            --creme: #faf6f0;
            --texto: #3e2723;
            --texto-suave: #6f4e37;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.5;
        }

        .login-card {
            background: #ffffff;
            padding: 48px 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(45, 27, 16, 0.08);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(93, 64, 55, 0.08);
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-header h2 {
            font-size: 28px;
            color: var(--cafe-escuro);
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--texto-suave);
            font-size: 15px;
        }

        .campo {
            margin-bottom: 20px;
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

        .campo input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e2d8cf;
            border-radius: 8px;
            font-size: 15px;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .campo input:focus {
            outline: none;
            border-color: var(--cafe-medio);
            box-shadow: 0 0 0 3px rgba(93, 64, 55, 0.1);
        }

        .erro {
            background: #fdecec;
            color: #a33333;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #f5c6c6;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--cafe-medio);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: var(--cafe-escuro);
        }

        .info {
            margin-top: 30px;
            padding: 18px;
            background: #f4ede4;
            border-radius: 8px;
            color: var(--texto-suave);
            font-size: 13px;
            text-align: center;
            border-top: 3px solid var(--cafe-claro);
        }

        .info strong {
            color: var(--cafe-escuro);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2>Cafeteria</h2>
            <p>Painel de gestão do cardápio</p>
        </div>

        <?php if ($erro): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="campo">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Digite seu usuário" autocomplete="username">
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="Digite sua senha" autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>

    </div>
</body>
</html>