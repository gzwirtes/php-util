<?php

namespace GZInfo\Util;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class S3{
    protected $aws_key;
    protected $aws_secret_key;

    public function __construct($aws_key, $aws_secret_key, $id = NULL, $callObjectLoad = TRUE)
    {
        $this->aws_key        = $aws_key;
        $this->aws_secret_key = $aws_secret_key;

        $this->s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => '2006-03-01',
            'endpoint' => 'http://s3.us-east-1.amazonaws.com/',
            'credentials' => [
                'key'    => $this->aws_key,
                'secret' => $this->aws_secret_key,
            ],
        ]);
    }

    public function enviarArquivo($bucket, $arquivoCompletoS3, $arquivoCompletoLocal, $excluirArquivo = true){
        $this->s3Client->putObject(array(
            'Bucket' => $bucket,
            'Key'    => $arquivoCompletoS3,
            'SourceFile' => $arquivoCompletoLocal,
            'ACL'    => 'public-read'
        ));

        if($excluirArquivo){
            unlink($arquivoCompletoLocal);
        }
    }

    public function baixarArquivo($bucket, $arquivoCompletoS3, $nomeArquivoSalvar ,$diretorioSalvar){
        $object = $this->s3Client->getObject(['Bucket' => $bucket, 'Key' => $arquivoCompletoS3]);
        file_put_contents($diretorioSalvar . $nomeArquivoSalvar, $object['Body']->getContents());
    }

    public function excluirArquivo($bucket,$arquivoCompletoS3){
        $this->s3Client->deleteObject(['Bucket' => $bucket, 'Key' => $arquivoCompletoS3]);
    }

    public function listarDiretorio($bucket,$diretorio){
        $response = $this->s3Client->listObjects(array('Bucket' => $bucket, 'Prefix' => $diretorio));
        $files = $response->getPath('Contents');
        $request_id = array();

        return $files;

        // Exemplo para percorrer os arquivos
        // foreach ($files as $file) {
        //     $filename = $file['Key'];
        //     print "\n\nFilename:". $filename;
        // }
    }

    public function existeArquivo ($bucket, $filename){
        try
        {
            $response = $this->s3Client->getObject(array(
                'Bucket' => $bucket,
                'Key' => $filename
            ));

            if ($response)
            {
                return TRUE;
            }
        }
        catch (S3Exception $e) {
            if ($e->getAwsErrorCode() == 'NoSuchKey') {
                return FALSE;
                // echo "não encontrado";
            }
        }

        // EXEMPLO - https://hotexamples.com/pt/examples/aws.s3/S3Client/getObject/php-s3client-getobject-method-examples.html
        // public function get($path)
        // {
        //     try {
        //         $model = $this->s3->getObject(['Bucket' => $this->bucket, 'Key' => $path]);
        //         return (string) $model->get('Body');
        //     } catch (S3Exception $e) {
        //         if ($e->getAwsErrorCode() == 'NoSuchKey') {
        //             throw Exception\NotFoundException::pathNotFound($path, $e);
        //         }
        //         throw Exception\StorageException::getError($path, $e);
        //     }
        // }
    }
}