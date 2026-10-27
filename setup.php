<?php
// setup.php - Cria banco de dados automaticamente
// PORTA 3307 (XAMPP)

$host = 'localhost:3307';
$usuario = 'root';
$senha = '';
$banco = 'gestao_cafeteria';

echo "<h2>Testando conexão...</h2>";
echo "<p>Host: $host</p>";
echo "<p>Usuário: $usuario</p>";

$conn = mysqli_connect($host, $usuario, $senha);

if (!$conn) {
    echo "<h3 style='color:red'>ERRO DE CONEXÃO:</h3>";
    echo "<p>" . mysqli_connect_error() . "</p>";
    echo "<p><strong>Solução:</strong> Verifique se o MySQL do XAMPP está iniciado na porta 3307.</p>";
    exit;
}

echo "<p style='color:green'>Conectado com sucesso!</p>";

// Cria o banco
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $banco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (mysqli_query($conn, $sql_create_db)) {
    echo "<p style='color:green'>Banco '$banco' criado.</p>";
} else {
    echo "<p style='color:red'>Erro ao criar banco: " . mysqli_error($conn) . "</p>";
}

mysqli_select_db($conn, $banco);

// Tabela usuarios
$sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL
    )
";
if (mysqli_query($conn, $sql_usuarios)) {
    echo "<p style='color:green'>Tabela 'usuarios' criada.</p>";
} else {
    echo "<p style='color:red'>Erro ao criar tabela usuarios: " . mysqli_error($conn) . "</p>";
}

// Tabela cardapio
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
    echo "<p style='color:green'>Tabela 'cardapio' criada.</p>";
} else {
    echo "<p style='color:red'>Erro ao criar tabela cardapio: " . mysqli_error($conn) . "</p>";
}

// Inserir admin se não existir
$sql_check = "SELECT id FROM usuarios WHERE usuario = 'admin' LIMIT 1";
$result = mysqli_query($conn, $sql_check);
if ($result && mysqli_num_rows($result) == 0) {
    $sql_insert = "INSERT INTO usuarios (nome, usuario, senha) VALUES ('Gerente', 'admin', 'admin123')";
    if (mysqli_query($conn, $sql_insert)) {
        echo "<p style='color:green'>Usuario admin inserido (admin / admin123).</p>";
    } else {
        echo "<p style='color:red'>Erro ao inserir admin: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:orange'>Usuario admin ja existe.</p>";
}

mysqli_close($conn);

echo '<hr>';
echo '<h3 style="color:#6f4e37;">Configuracao concluida!</h3>';
echo '<p><a href="login.php" style="background:#6f4e37; color:#fff; padding:10px 20px; text-decoration:none; border-radius:6px;">Ir para o Login</a></p>';
echo '<p><small>Após criar o banco, deletar este arquivo setup.php por seguranca.</small></p>';
?>