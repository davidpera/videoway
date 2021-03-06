<?php

namespace app\controllers;

use app\models\Alquileres;
use app\models\AlquileresSearch;
use app\models\GestionarPeliculaForm;
use app\models\GestionarSocioForm;
use app\models\Peliculas;
use app\models\Socios;
use app\models\Usuarios;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * AlquileresController implements the CRUD actions for Alquileres model.
 */
class AlquileresController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['gestionar', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['gestionar'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                        //que se llame pepe
                        'matchCallback' => function ($rule, $action) {
                            return Usuarios::getPermitido();
                        },
                    ],
                ],
            ],
        ];
    }

    /*public function beforeAction($action)
    {

        if ($action->id != 'devolver'){
            Yii::$app->session->set('rutaVuelta', Url::to());
            return parent::beforeAction($action);
        }
    }*/

    public function actionListado()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Alquileres::find()
            ->joinWith(['socio', 'pelicula']),
        ]);

        $dataProvider->sort->attributes['socio.numero'] = [
            'asc' => ['socios.numero' => SORT_ASC],
            'desc' => ['socios.numero' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pelicula.codigo'] = [
            'asc' => ['peliculas.codigo' => SORT_ASC],
            'desc' => ['peliculas.codigo' => SORT_DESC],
        ];

        return $this->render('listado', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Gestionara el alquiler y devolucion de una pelicula enuna sola accion.
     * @return mixed
     * @param null|mixed $numero
     * @param null|mixed $codigo
     */
    public function actionGestionar($numero = null, $codigo = null)
    {
        $gestionarSocioForm = new GestionarSocioForm([
            'numero' => $numero,
        ]);

        if (Yii::$app->request->isAjax && $gestionarSocioForm->load(Yii::$app->request->queryParams)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($gestionarSocioForm);
        }

        $data = [];

        if ($numero != null && $gestionarSocioForm->validate()) {
            $data['socio'] = Socios::findOne(['numero' => $gestionarSocioForm->numero]);
            $gestionarPeliculaForm = new GestionarPeliculaForm([
                'numero' => $numero,
                'codigo' => $codigo,
            ]);
            $data['gestionarPeliculaForm'] = $gestionarPeliculaForm;
            if ($codigo !== null && $gestionarPeliculaForm->validate()) {
                $data['pelicula'] = Peliculas::findOne([
                    'codigo' => $gestionarPeliculaForm->codigo,
                ]);
            }
        }

        Yii::$app->session->set('rutaVuelta', Url::to()); //Url::to() te devuelve la ruta actual

        $data['gestionarSocioForm'] = $gestionarSocioForm;

        return $this->render('gestionar', $data);
    }

    public function actionGestionarAjax($numero = null, $codigo = null)
    {
        $gestionarPeliculaForm = new GestionarPeliculaForm([
            'numero' => $numero,
            'codigo' => $codigo,
        ]);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($gestionarPeliculaForm);
        }

        return $this->render('gestionar-ajax', [
            'gestionarPeliculaForm' => $gestionarPeliculaForm,
        ]);
    }

    public function actionPendientes($numero)
    {
        $socio = Socios::findOne(['numero' => $numero]);

        if ($socio === null) {
            return '';
        }

        $pendientes = $socio->getPendientes()->with('pelicula');

        return $this->renderAjax('pendientes', [
            'pendientes' => $pendientes,
        ]);
    }

    /**
     * devuelve un alquiler indicado por el id pasado por post.
     * @param  string   $numero         numero del socio para volver a el
     * @return Response                 La redirección
     * @throws NotFoundHttpException    si el id falta o no es correcto
     */
    public function actionDevolver($numero)
    {
        if (($id = Yii::$app->request->post('id')) === null) {
            throw new NotFoundHttpException('Falta el alquiler.');
        }

        if (($alquiler = Alquileres::findOne($id)) === null) {
            throw new NotFoundHttpException('El alquiler no existe.');
        }

        $alquiler->devolucion = date('Y-m-d H:i:s');
        $alquiler->save();

        $url = Yii::$app->session->get(
            'rutaVuelta',
            ['alquileres/gestionar', 'numero' => $numero]
        );
        Yii::$app->session->remove('rutaVuelta');

        return $this->redirect($url);
    }

    public function actionDevolverAjax()
    {
        if (($id = Yii::$app->request->post('id')) === null) {
            throw new NotFoundHttpException('Falta el alquiler.');
        }

        if (($alquiler = Alquileres::findOne($id)) === null) {
            throw new NotFoundHttpException('El alquiler no existe.');
        }

        $alquiler->devolucion = date('Y-m-d H:i:s');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $alquiler->save();
    }

    public function actionAlquilarAjax()
    {
        $numero = Yii::$app->request->post('numero');
        $codigo = Yii::$app->request->post('codigo');
        $socio = Socios::findOne(['numero' => $numero]);
        $pelicula = Peliculas::findOne(['codigo' => $codigo]);
        $alquiler = new Alquileres([
            'socio_id' => $socio->id,
            'pelicula_id' => $pelicula->id,
        ]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $alquiler->save();
    }

    /**
     * Alquila una pelicula dados socio_id y pelicula_id
     * pasamos por post.
     * @param   string        $numero    El numero del socio para volver a él
     * @return  Response                 la redireccion
     * @throws  BadRequestHttpException  Si algun id es incorrecto
     */
    public function actionAlquilar($numero)
    {
        $alquiler = new Alquileres();

        if ($alquiler->load(Yii::$app->request->post(), '') && $alquiler->save()) {
            return $this->redirect([
                'alquileres/gestionar',
                'numero' => $numero,
            ]);
        }

        throw new BadRequestHttpException('No se ha creado el alquiler.');
    }

    /**
     * Lists all Alquileres models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlquileresSearch([
            // 'desdeAlquilado' => date('Y-m-d'),
            // 'hastaAlquilado' => date('Y-m-d'),
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Alquileres model.
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

    /**
     * Creates a new Alquileres model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Alquileres();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Alquileres model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Alquileres model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Alquileres model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Alquileres the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alquileres::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
