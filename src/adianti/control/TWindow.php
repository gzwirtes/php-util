<?php
namespace GZInfo\Adianti\Control;

use Adianti\Registry\TSession;

if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

class TPage extends \Adianti\Control\TWindow
{
    protected static $database = CONEXAO;

    public function render($pattern, $object, $cast = null)
    {
        $content = $pattern;
        if (preg_match_all('/\{(.*?)\}/', $pattern, $matches)) {
            foreach ($matches[0] as $match) {
                $property = substr($match, 1, -1);
                if (substr($property, 0, 1) == '$') {
                    $property = substr($property, 1);
                }
                $value = $object;
                foreach (explode('->',$property) as $property) {
                    $value = $value->$property;
                }
                if ($cast) {
                    settype($value, $cast);
                }
                $content  = str_replace($match, $value, $content);
            }
        }

        return $content;
    }
}
