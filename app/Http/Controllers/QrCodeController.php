<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public  function generateQrCode(Request $request)
    {
        // Obtener la URL desde la solicitud POST
        $url = $request->input('url');
        // Configurar el renderer para usar SVG
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($url);

        // Convertir SVG a base64
        $base64Svg = base64_encode($qrCodeSvg);
        $dataUri = 'data:image/svg+xml;base64,' . $base64Svg;

        // Generar el contenido HTML para el PDF
        $html = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                }
                .qr-container {
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <h1>Código QR</h1>
            <p>Este es tu código QR generado:</p>
            <div class="qr-container">
                <img src="'.$dataUri.'" alt="Código QR"/>
            </div>
        </body>
        </html>
        ';

        // Generar el PDF
        $pdf = Pdf::loadHTML($html);
        return $pdf->download('qr_code.pdf');
    }

    public static  function generateQrCodeBase64(Request $request)
    {
        // Obtener la URL desde la solicitud POST
        $url = $request->input('url');
        // Configurar el renderer para usar SVG
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($url);

        // Convertir SVG a base64
        $base64Svg = base64_encode($qrCodeSvg);
        $dataUri = 'data:image/svg+xml;base64,' . $base64Svg;

        return $dataUri;

    }
}
