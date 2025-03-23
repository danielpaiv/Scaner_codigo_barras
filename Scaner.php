<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leitor de Código de Barras</title>
    <img src="icones/graficos.ico" width="46" height="46">

    <style>
        body {
            background-color: #f1f1f1;
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
        button {
            background-color: #f1f1f1;
            border: none;
            color: red;
            padding: 0px 0px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<button onclick="window.location.href='teste.php'"><img src="icones/graficos.ico" width="46" height="46" title="relatorio"></button>
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
    <input type="text" id="input-preco-unitario" placeholder="Preço Unitário" readonly />

    <audio id="beep-sound" src="beep.mp3" preload="auto"></audio>

    <script>
        const inputBarcode = document.getElementById("input-barcode");
        const beepSound = document.getElementById("beep-sound");
        const cameraContainer = document.getElementById("camera-container");
        const video = document.getElementById("camera-preview");

        const inputId = document.getElementById("input-id");
        const inputProduto = document.getElementById("input-produto");
        const inputPrecoUnitario = document.getElementById("input-preco-unitario");

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
                        inputPrecoUnitario.value = data.preco_unitario;
                    }
                })
                .catch(error => console.error("Erro na requisição:", error));

                inputPrecoUnitario.focus();

                // Limpa o campo após a leitura
                inputBarcode.value = "";
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
