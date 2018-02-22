<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use Yii;
use Spatie\Dropbox\Exceptions\BadRequest;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /*$nombre = Yii::$app->db->createCommand(
            'SELECT titulo
                FROM alquileres a
                JOIN peliculas p ON a.pelicula_id = p.id
                WHERE socio_id = :socio_id',
            ['socio_id' => $socio_id]
        )
                              ->queryAll();

        $nombre = (new \yii\db\Query())
            ->select('titulo')
            ->from('alquileres a')
            ->join('LEFT JOIN', 'peliculas p', 'p.id = a.pelicula_id')
            ->where(['socio_id' => $socio_id])
            ->all();
        echo '<pre>';
        var_dump($nombre);*/
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionDropbox()
    {
        $client = new \Spatie\Dropbox\Client(getenv('DROPBOX_TOKEN'));
        try {
            $client->delete('3.jpg');
        } catch (BadRequest $e) {
        }
        $client->upload(
            '3.jpg',
            file_get_contents(Yii::getAlias('@uploads/3.jpg')),
            'overwrite'
        );
        $res = $client->createSharedLinkWithSettings('3.jpg', [
            'requested_visibility' => 'public'
        ]);
        return $res['url'];
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionEmail()
    {
        $resultado = Yii::$app->mailer->compose('prueba')
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo('domingo.castaneda@iesdonana.org')
            ->setSubject('Ven a mi pagina o te parto las piernas')
            ->setTextBody('Este es un texto que no va a existir en el correo')
            // ->setHtmlBody('<b>HTML content</b>')
            ->send();
        if (!$resultado) {
            //no se ha enviado correctamente
        }
        return 'Hecho';
    }
}
