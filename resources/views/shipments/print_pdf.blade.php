<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Imprimir Guía</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>

<body>
    {{-- Same-origin: a diferencia de un data: URI, esto permite scriptear el contentWindow
         del visor de PDF para disparar print() automáticamente. --}}
    <iframe id="pdfFrame" src="{{ route('shipments.printFile', $shipment) }}"></iframe>
    <script>
        var frame = document.getElementById('pdfFrame');
        frame.addEventListener('load', function() {
            setTimeout(function() {
                try {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                } catch (e) {
                    window.print();
                }
            }, 300);
        });
    </script>
</body>

</html>
