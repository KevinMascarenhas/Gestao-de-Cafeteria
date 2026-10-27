<?php
// setup.php - Script para criar automaticamente o banco de dados e tabelas
// Acesse este arquivo uma vez no navegador: http://localhost/atividade%20extra/setup.php

$host = 'localhost:3307';
$usuario = 'root';
$senha = '';
$banco = 'gestao_cafeteria';

// Conecta no MySQL (sem selecionar banco ainda)
$conn = mysqli_connect($host, $usuario, $senha);

if (!$conn) {
    die('<h3 style="color:red">Erro ao conectar no MySQL:</h3><p>' . mysqli_connect_error() . '</p>');
}

// Define UTF-8
mysqli_set_charset($conn, 'utf8mb4');

echo '<h2 style="color:#3e2723; font-family:sans-serif;">🔧 Configurando Banco de Dados...</h2>';

// 1. Cria o banco de dados
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (mysqli_query($conn, $sql_create_db)) {
    echo "<p style='color:green'> Banco '$banco' criado (ou já existente).</p>";
} else {
    echo "<p style='color:red'> Erro ao criar banco: " . mysqli_error($conn) . "</p>";
}

// 2. Seleciona o banco
mysqli_select_db($conn, $banco);

// 3. Cria tabela usuarios
$sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL
    )
";
if (mysqli_query($conn, $sql_usuarios)) {
    echo "<p style='color:green'> Tabela 'usuarios' criada (ou já existente).</p>";
} else {
    echo "<p style='color:red'> Erro ao criar tabela usuarios: " . mysqli_error($conn) . "</p>";
}

// 4. Cria tabela cardapio
$sql_cardapio = "
    CREATE TABLE IF NOT EXISTS cardapio (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_item VARCHAR(100) NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10, 2) NOT NULL,
        disponivel BOOLEAN DEFAULT TRUE
    )
";
if (mysqli_query($conn, $sql_cardapio)) {
    echo "<p style='color:green'>✅ Tabela 'cardapio' criada (ou já existente).</p>";
} else {
    echo "<p style='color:red'>❌ Erro ao criar tabela cardapio: " . mysqli_error($conn) . "</p>";
}

// 5. Insere usuário admin padrão (só se não existir)
$sql_check_admin = "SELECT id FROM usuarios WHERE usuario = 'admin' LIMIT 1";
$result = mysqli_query($conn, $sql_check_admin);

if ($result && mysqli_num_rows($result) == 0) {
    $sql_insert_admin = "INSERT INTO usuarios (nome, usuario, senha) VALUES ('Gerente', 'admin', 'admin123')";
    if (mysqli_query($conn, $sql_insert_admin)) {
        echo "<p style='color:green'> Usuário admin inserido (login: admin / senha: admin123).</p>";
    } else {
        echo "<p style='color:red'> Erro ao inserir admin: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:orange'> Usuário admin já existe, pulando inserção.</p>";
}

mysqli_close($conn);

echo '<hr>';
echo '<h3 style="color:#6f4e37;"> Configuração concluída!</h3>';
echo '<p><a href="login.php" style="background:#6f4e37; color:#fff; padding:10px 20px; text-decoration:none; border-radius:6px;">▶ Ir para o Login</a></p>';
echo '<p><small>Após criar o banco, você pode deletar este arquivo setup.php por segurança.</small></p>';

?>
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; color: #333; line-height: 1.6; }
    h2 { border-bottom: 2px solid #d7ccc8; padding-bottom: 10px; }
    p { margin: 10px 0; }
    a { display: inline-block; margin-top: 20px; }
</style>