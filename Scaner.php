<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leitor de Código de Barras</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        video {
            width: 100%;
            max-width: 400px;
        }
        input {
            display: block;
            margin: 10px auto;
            padding: 10px;
            font-size: 18px;
            width: 300px;
            border: 2px solid #ccc;
        }
        #camera-container {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Leitor de Código de Barras</h1>
    <p>Escaneie um código de barras ou digite manualmente:</p>

    <input type="text" id="input-barcode" placeholder="Digite ou escaneie o código" autofocus />
    <button onclick="startScanner()">📷 Usar Câmera</button>

    <div id="camera-container">
        <video id="camera-preview"></video>
    </div>

    <h2>Detalhes do Produto</h2>
    <input type="text" id="input-id" placeholder="ID" readonly />
    <input type="text" id="input-produto" placeholder="Produto" readonly />
    <input type="text" id="input-quantidade" placeholder="Quantidade" readonly />
    <input type="text" id="input-preco" placeholder="preco" readonly />
    <input type="text" id="input-preco-unitario" placeholder="Preço Unitário" readonly />
    <input type="text" id="input-preco-total" placeholder="Preço Total" readonly />

    <audio id="beep-sound" src="beep.mp3" preload="auto"></audio>

    <script>
        const inputBarcode = document.getElementById("input-barcode");
        const beepSound = document.getElementById("beep-sound");
        const cameraContainer = document.getElementById("camera-container");
        const video = document.getElementById("camera-preview");

        const inputId = document.getElementById("input-id");
        const inputProduto = document.getElementById("input-produto");
        const inputQuantidade = document.getElementById("input-quantidade");
        const inputPreco = document.getElementById("input-preco");
        const inputPrecoUnitario = document.getElementById("input-preco-unitario");
        const inputPrecoTotal = document.getElementById("input-preco-total");

        inputBarcode.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                processBarcode(inputBarcode.value.trim());
            }
        });

        function processBarcode(barcode) {
            if (barcode.length > 0) {
                console.log("Código de barras detectado:", barcode);
                beepSound.play().catch(error => console.log("Erro ao reproduzir som:", error));

                // Faz requisição AJAX para buscar o produto no banco de dados
                fetch("processar_scanner.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "codigo=" + encodeURIComponent(barcode)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert(data.erro);
                    } else {
                        inputId.value = data.id;
                        inputProduto.value = data.produto;
                        inputQuantidade.value = data.quantidade;
                        inputPreco.value = data.preco;
                        inputPrecoUnitario.value = data.preco_unitario;
                        inputPrecoTotal.value = data.preco_total;
                    }
                })
                .catch(error => console.error("Erro na requisição:", error));

                inputQuantidade.focus();
            }
        }

        function startScanner() {
            cameraContainer.style.display = "block";
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: video
                },
                decoder: {
                    readers: ["ean_reader"]
                }
            }, function (err) {
                if (err) {
                    console.error("Erro ao iniciar scanner:", err);
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function (result) {
                const barcode = result.codeResult.code;
                processBarcode(barcode);
                Quagga.stop();
                cameraContainer.style.display = "none";
            });
        }
    </script>

</body>
</html>
