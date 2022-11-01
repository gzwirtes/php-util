<?php

namespace GZInfo\Util;

class JasperServerIntegration {
    public $jasperUrl;
    public $reportPath;
    public $type;
    public $user;
    public $password;
    public $status_code;
    public $parameters = [];

    function __construct($jasperUrl, $reportPath, $type, $user, $password, $parameters) {
        $this->jasperUrl = $jasperUrl;
        $this->reportPath = $reportPath;
        $this->type = $type;
        $this->user = $user;
        $this->password = $password;
        $this->parameters = $parameters;
    }

    private function getQueryString() {
        $queryString = "";
        foreach ($this->parameters as $key => $val) {
            if ($queryString == "") {
                $queryString .= '?';
            } else {
                $queryString .= '&';
            }

            $queryString .= $key . "=" . $val;
        }

        return $queryString;
    }

    public function execute() {
        $url = $this->jasperUrl . '/rest_v2/reports/' . $this->reportPath . '.' . $this->type . $this->getQueryString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90000); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->password");
        $result=curl_exec ($ch);
        $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close ($ch);

        if ($this->status_code != 200) {
            $xml = simplexml_load_string(strval($result));
            $exception = new Exception("Erro $this->status_code. $xml->errorCode: $xml->message.");
            $exception->errorCode = $xml->errorCode;
            $exception->errorMessage = $xml->message;

            throw $exception;
        }

        return $result;
    }
}


?>