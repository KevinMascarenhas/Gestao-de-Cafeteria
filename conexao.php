<?php
// conexao.php - Conexão com o banco de dados usando MySQLi

$host = 'localhost:3307';
$usuario_banco = 'root';
$senha_banco = '';
$banco = 'gestao_cafeteria';

// Cria a conexão
$conn = mysqli_connect($host, $usuario_banco, $senha_banco, $banco);

// Verifica se houve erro na conexão
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define o charset para evitar problemas com acentuação (UTF-8)
mysqli_set_charset($conn, 'utf8mb4');