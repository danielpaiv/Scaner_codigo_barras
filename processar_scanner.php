<?php
require_once "conexao.php";

if (isset($_POST["codigo"])) {
    $codigo = $_POST["codigo"];

    $sql = "SELECT * FROM estoque WHERE descricao = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
        echo json_encode([
            "id" => $produto["id"],
            "produto" => $produto["produto"],
            "quantidade" => $produto["quantidade"],
            "preco" => $produto["preco"],
            "preco_unitario" => $produto["preco_unitario"],
            "preco_total" => $produto["quantidade"] * $produto["preco_unitario"]
        ]);
    } else {
        echo json_encode(["erro" => "Produto não encontrado"]);
    }

    $stmt->close();
    $conexao->close();
}
?>
