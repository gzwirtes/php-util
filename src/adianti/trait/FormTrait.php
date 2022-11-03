<?php

namespace GZInfo\Adianti\Trait;

use Exception;
use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;

trait FormTrait
{
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public function onShow($param = null)
    {

    }

    public function onDelete($param = null)
    {
        try
        {
            if (isset($param['delete']) && $param['delete'] == 1)
            {
                TTransaction::open(self::$database);
                $object = new self::$activeRecord($param['key']);

                $mensagem_success = 'Registro excluido com sucesso!';
                if ($this->successDelete)
                {
                    $mensagem_success = $this->render($this->successDelete, $object);
                }

                $mensagem_error = 'Impossível excluir o registro!';
                if ($this->errorDelete)
                {
                    $mensagem_error = $this->render($this->errorDelete, $object);
                }
                $object->delete();
                TTransaction::close();

                if(self::$list_class){
                    new TMessage('info', $mensagem_success, new TAction([self::$list_class, 'onShow']), 'Sucesso');
                }
                else
                {
                    new TMessage('info', $mensagem_success, new TAction([$this, 'onShow']), 'Sucesso');
                }
            }
            else
            {
                $action = new TAction([$this, 'onDelete']);
                $param['delete'] = 1;
                $action->setParameters($param);

                $mesage = 'Deseja excluir o registro?';

                if($this->questionDelete)
                {
                    TTransaction::open(self::$database);
                    $object = new self::$activeRecord($param['key'], FALSE);
                    TTransaction::close();

                    $mesage = $this->render($this->questionDelete, $object);
                }

                new TQuestion($mesage, $action);
            }
        }
        catch (Exception $e)
        {
            $action = new TAction([$this, 'onEdit'], ['key'=>$param['key']]);
            new TMessage('error', '<b style="font-size: 15px;">'.$mensagem_error.'</b><br><br><p style="font-size: 12px;"><b>Erro Banco de Dados </b>='.$e->getMessage().'</p>', $action, 'Impossível excluir!' );
            TTransaction::rollback();
        }
    }

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