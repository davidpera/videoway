<?php

namespace app\controllers;

use app\models\Usuarios;
use Yii;

class UsuariosController extends \yii\web\Controller
{
    public function actionCreate()
    {
        $model = new Usuarios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goHome();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
