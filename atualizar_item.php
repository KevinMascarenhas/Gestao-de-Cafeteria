<?php
// atualizar_item.php - Lógica de UPDATE (atualização)

require_once 'auth.php';
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: painel.php");
    exit;
}

$id = $_POST['id'] ?? '';
$nome_item = trim($_POST['nome_item'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$preco = $_POST['preco'] ?? '';
$disponivel = isset($_POST['disponivel']) ? $_POST['disponivel'] : 'TRUE';

// Validações
if (!filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: painel.php?erro=id_invalido");
    exit;
}

if (empty($nome_item) || empty($categoria) || empty($preco)) {
    header("Location: editar_item.php?id=" . (int)$id . "&erro=campos_obrigatorios");
    exit;
}

if (!is_numeric($preco) || $preco < 0) {
    header("Location: editar_item.php?id=" . (int)$id . "&erro=preco_invalido");
    exit;
}

// Prepared Statement para UPDATE (protege contra SQL Injection)
// Atualiza pelo ID, então não cria item duplicado
$stmt = mysqli_prepare($conn, 
    "UPDATE cardapio SET nome_item = ?, categoria = ?, descricao = ?, preco = ?, disponivel = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssdsii", $nome_item, $categoria, $descricao, $preco, $disponivel, $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: painel.php?sucesso=atualizado");
} else {
    header("Location: editar_item.php?id=" . (int)$id . "&erro=bd");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);