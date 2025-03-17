<?php
require_once "conexao.php";

if ($conexao->connect_error) {
    die("❌ Erro na conexão: " . $conexao->connect_error);
} else {
    echo "✅ Conexão bem-sucedida com o banco de dados!";
}

$conexao->close();
?>
