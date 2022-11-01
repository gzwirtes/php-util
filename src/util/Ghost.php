<?php

namespace GZInfo\Util;

class Ghost {
    public static function convertPDFtoImage ($pdf, $image, $folder_pdf, $folder_image) {
        try {
            // exec("gswin64 -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile=C:/Apache24/htdocs/golfranportalfornecedorphp/tmp/{$imagem[0]}.jpg C:/Apache24/htdocs/golfranportalfornecedorphp/tmp/{$imagem[0]}.pdf", $output, $response);
            // exec("gswin64 -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile={$data['edicao_fornecedor_atividade_id']}.jpg {$imagem[0]}.pdf", $output, $response);
            // exec("gswin64 -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile=C:/Apache24/htdocs/golfranportalfornecedorphp/tmp/{$imagem[0]}.jpg C:/Apache24/htdocs/golfranportalfornecedorphp/tmp/{$imagem[0]}.pdf", $output, $response);
            // echo "{$pdf} | {$image} | {$folder_pdf} | {$folder_image}";die;
            // echo "gswin64 -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile={$folder_image}{$image} {$folder_pdf}{$pdf}";
            // exec("gswin64 -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile={$folder_image}{$image} {$folder_pdf}{$pdf}", $output, $response);
            // exec("gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile={$folder_image}{$image} {$folder_pdf}{$pdf}", $output, $response);
            exec("gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile={$folder_image}{$image} {$folder_pdf}{$pdf}", $output, $response);
        } catch (Exception $e) {
            throw new Exception('error', $e->getMessage());
        }
    }

    public static function convertPDFCatalogo ($imagem, $pdf) {
        try {
            exec("gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=80 -r300x300 -dFIXEDMIDEA -dUseCropBox -dDownScaleFactor=1 -sOutputFile='{$imagem}' {$pdf}", $output, $response);
            // exec("gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dJPEGQ=10 -r250x250 -sOutputFile={$folder_image}{$image} {$folder_pdf}{$pdf}", $output, $response);
        } catch (Exception $e) {
            throw new Exception('error', $e->getMessage());
        }
    }
}