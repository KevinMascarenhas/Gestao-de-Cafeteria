<?php
$host = 'localhost:3307';
$usuario = 'root';
$senha = '';

$conn = mysqli_connect($host, $usuario, $senha);

if (!$conn) {
    echo "<h3 style='color:red'> ERRO DE CONEXÃO:</h3>";
    echo "<p>" . mysqli_connect_error() . "</p>";
    echo "<p><strong>Possíveis causas:</strong></p>";
    echo "<ul>";
    echo "<li>O MySQL do XAMPP não está rodando na porta 3307</li>";
    echo "<li>O root do MySQL do XAMPP tem uma senha definida</li>";
    echo "<li>Você está tentando conectar na porta 3306 (do Windows) em vez da 3307</li>";
    echo "</ul>";
} else {
    echo "<h3 style='color:green'> CONEXÃO OK! </h3>";
    echo "<p>Conectado com sucesso no MySQL na porta 3307.</p>";
    mysqli_close($conn);
}
?>