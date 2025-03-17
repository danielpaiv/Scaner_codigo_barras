<?php
$host = "localhost"; // Servidor do banco de dados
$usuario = "root"; // Usuário do banco de dados
$senha = ""; // Senha do banco de dados (vazio se não tiver)
$banco = "hortfruti"; // Nome do banco de dados

$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>
