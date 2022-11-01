<?php

namespace GZInfo\Util;

use PHPJasper\PHPJasper;
use Adianti\Database\TConnection;

class Jasper {
    public static function report ($jrxml, $name, $connection, $params=[]) {
        try {
            $input = str_replace('\\', '/', getcwd()) .'/app/reports/' . $jrxml . '.jrxml';

            $output =  str_replace('\\', '/', getcwd()) . '/tmp/'. $name;

            $output_url = 'tmp/'. $name;

            $conexao = TConnection::getDatabaseInfo($connection);

            if ( !isset($conexao['port']) || $conexao['port'] == '' )
            {
                if ( $conexao['type'] == 'mysql' )
                {
                    $conexao['port'] = '3306';
                }
                else if ( $conexao['type'] == 'pgsql' )
                {
                    $conexao['port'] == '5432';
                }
                else
                {
                    echo 'Porta para conexão não declarada nas configurações';
                    die;
                }
            }
            $options = [
                'format' => ['pdf'],
                'params' => $params,
                'db_connection' => [
                    'driver'   => $conexao['type'],
                    'username' => $conexao['user'],
                    'password' => $conexao['pass'],
                    'host'     => $conexao['host'],
                    'database' => $conexao['name'],
                    'port'     => $conexao['port'],
                ]
            ];

            $jasper = new PHPJasper;

            $jasper->process(
                $input,
                $output,
                $options
            )->execute();

            return $output_url . '.pdf';
        } catch (Exception $e) {
            // Sweet::mensagem('Erro',$e->getMessage(),'error');

            // new TMessage('error', $e->getMessage());
            echo $e->getMessage();
        }

    }
}