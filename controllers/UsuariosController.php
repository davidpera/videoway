<?php

namespace app\controllers;

use app\models\Usuarios;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class UsuariosController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Usuarios::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }



    public function actionVerificar()
    {
        $token = $_GET['token_val'];
        $model = Usuarios::findOne(['token_val' => $token]);
        $model->token_val = null;
        $model->save();
        return $this->goHome();
    }

    public function actionCreate()
    {
        $model = new Usuarios(['scenario' => Usuarios::ESCENARIO_CREATE]);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->foto = UploadedFile::getInstance($model, 'foto');
            if ($model->save() && $model->upload()) {
                $model->email();
                return $this->goHome();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate()
    {
        $model = Yii::$app->user->identity;
        $model->scenario = Usuarios::ESCENARIO_UPDATE;
        $model->password = '';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goHome();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
