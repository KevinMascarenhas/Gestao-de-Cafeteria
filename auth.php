<?php
// auth.php - Protege páginas que exigem login
// Inclui este arquivo no topo de qualquer página restrita

session_start();

// Se o usuário não estiver logado, redireciona para o login
if (!isset($_SESSION['usuario_logado'])) {
    header("Location: login.php?erro=acesso_negado");
    exit;
}