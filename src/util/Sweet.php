<?php

namespace GZInfo\Util;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;

class Sweet{
    static function mensagem($title,$text,$icon = 'success',$textButton = 'OK',TAction $action = null,$colorButton = '#236BB0'){
        $callback = 'undefined';

        if ($action)
        {
            $callback = "__adianti_load_page('{$action->serialize()}')";
        }

        TScript::create("Swal.fire({
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                title: '{$title}',
                                html: '{$text}',
                                icon: '{$icon}',
                                confirmButtonText: '{$textButton}',
                                confirmButtonColor: '{$colorButton}'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $callback
                                }
                            })"
                        );
    }

    static function confirmacao($title,$text,TAction $actionConfirm,$textButtonConfirm = 'Confirmar',$textButtonCancel = 'Cancelar',$icon = 'question',$focusConfirm = 'false', TAction $actionCancel = null,$colorButtonConfirm = '#236BB0', $colorButtonCancel = '#d14529'){
        $callbackConfirm = 'undefined';

        if ($actionConfirm)
        {
            $callbackConfirm = "__adianti_load_page('{$actionConfirm->serialize()}')";
        }

        $callbackCancel = 'undefined';

        if ($actionCancel)
        {
            $callbackCancel = "__adianti_load_page('{$actionCancel->serialize()}')";
        }

        TScript::create("Swal.fire({
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                title: '{$title}',
                                html: '{$text}',
                                icon: '{$icon}',
                                showCancelButton: true,
                                confirmButtonText: '{$textButtonConfirm}',
                                confirmButtonColor: '{$colorButtonConfirm}',
                                cancelButtonText: '{$textButtonCancel}',
                                cancelButtonColor: '{$colorButtonCancel}',
                                focusConfirm: {$focusConfirm},
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $callbackConfirm
                                }else if (result.isDenied) {
                                    $callbackCancel
                                }
                            })"
                        );
    }

    static function toast($title,$icon = 'success',$position = 'top-end', $timer = 3000, $actionClose = null){
        $callbackActionClose = 'undefined';

        if ($actionClose)
        {
            $callbackActionClose = "__adianti_load_page('{$actionClose->serialize()}')";
        }

        TScript::create("Toast = Swal.mixin({
                            toast: true,
                            position: '{$position}',
                            showConfirmButton: false,
                            timer: {$timer},
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer),
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            },
                            didClose: (toast) => {
                                $callbackActionClose
                            }
                        });
                        Toast.fire({
                            icon: '{$icon}',
                            title: '{$title}',
                        });
                        ");
    }
}