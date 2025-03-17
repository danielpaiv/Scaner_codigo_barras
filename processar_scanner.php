<?php
require_once "conexao.php";

if (isset($_POST["codigo"])) {
    $codigo = $_POST["codigo"];

    $sql = "SELECT id, produto, preco_unitario FROM estoque WHERE descricao = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $codigo);  // Usando "s" para string, já que o código de barras provavelmente será uma string
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
        echo json_encode([
            "id" => $produto["id"],
            "produto" => $produto["produto"],
            "preco_unitario" => $produto["preco_unitario"]
        ]);
    } else {
        echo json_encode(["erro" => "Produto não encontrado"]);
    }

    $stmt->close();
    $conexao->close();
}
?>
