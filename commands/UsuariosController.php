<?php

namespace app\commands;

use app\models\Usuarios;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Comandos de mantenimiento relacionados con los usuarios.
 */
class UsuariosController extends Controller
{
    /**
     * Elimina los usuarios que llevan mas de X horas sin validarse.
     * @param int $dias
     */
    public function actionLimpiar($dias = 2)
    {
        $ahora = date('Y-m-d H:i:s');
        $res = Usuarios::deleteAll(
            "token_val IS NOT NULL
            AND '$ahora' - created_at >= :dias::interval",
            ['dias' => "P{$dias}D"]
        );
        echo "Se han eliminado $res usuarios.\n";
        return ExitCode::OK;
    }
}
