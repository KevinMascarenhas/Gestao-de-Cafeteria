<?php
// cadastrar_item.php - Lógica de CREATE (alternativa separada)
// Recebe dados via POST e insere no banco, depois redireciona para o painel

require_once 'auth.php';
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: painel.php");
    exit;
}

$nome_item = trim($_POST['nome_item'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$preco = $_POST['preco'] ?? '';

if (empty($nome_item) || empty($categoria) || empty($preco)) {
    header("Location: painel.php?erro=campos_obrigatorios");
    exit;
}

if (!is_numeric($preco) || $preco < 0) {
    header("Location: painel.php?erro=preco_invalido");
    exit;
}

// Prepared Statement para INSERT (protege contra SQL Injection)
$stmt = mysqli_prepare($conn, 
    "INSERT INTO cardapio (nome_item, categoria, descricao, preco, disponivel) VALUES (?, ?, ?, ?, TRUE)");
mysqli_stmt_bind_param($stmt, "ssds", $nome_item, $categoria, $descricao, $preco);

if (mysqli_stmt_execute($stmt)) {
    header("Location: painel.php?sucesso=cadastrado");
} else {
    header("Location: painel.php?erro=bd");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);