<?php

namespace GZInfo\Exception;
class CustomException extends \PDOException
{
    protected $detailed_message;

    public function __construct(\Exception $exception = null)
    {
        // Extrair a mensagem específica do erro PostgreSQL
        $this->detailed_message = $this->extractDetailedMessage($exception->getMessage());
        parent::__construct($this->detailed_message,(int) $exception->getCode(), $exception);
    }

    private function extractDetailedMessage($message)
    {
        $matches = [];
        if (preg_match('/ERROR:\s*(.*?)(?:\s*CONTEXT:|$)/', $message, $matches)) {
            return $matches[1];
        }
        return $message; // Fallback caso a regex não funcione
    }

    public function getDetailedMessage()
    {
        return $this->detailed_message;
    }
}
