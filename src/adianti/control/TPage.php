<?php
namespace Mycomponent\Adianti\Control;

use Adianti\Registry\TSession;

if (!defined('CONEXAO')) {
    define('CONEXAO', TSession::getValue('conexao'));
}

class TPage extends \Adianti\Control\TPage
{
    protected static $database = CONEXAO;
}
