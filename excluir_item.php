<?php
// excluir_item.php - Lógica de DELETE (exclusão)
// Recebe o ID via URL (?id=X)

require_once 'auth.php';
require_once 'conexao.php';

$id = $_GET['id'] ?? '';

// Valida se o ID é um número inteiro
if (!filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: painel.php?erro=id_invalido");
    exit;
}

// Prepared Statement para DELETE (protege contra SQL Injection)
$stmt = mysqli_prepare($conn, "DELETE FROM cardapio WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: painel.php?sucesso=excluido");
    } else {
        header("Location: painel.php?erro=item_nao_encontrado");
    }
} else {
    header("Location: painel.php?erro=bd");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);