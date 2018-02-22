<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\imagine\Image;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nombre
 * @property string $password
 * @property string $email
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ESCENARIO_CREATE = 'create';
    const ESCENARIO_UPDATE = 'update';

    public $confirmar;

    /**
     * Contiene la foto subida en el formulario.
     * @var UploadedFile
     */
    public $foto;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'confirmar',
            'foto',
        ]);
    }

    // public function scenarios()
    // {
    //     return [
    //         self::ESCENARIO_CREATE => ['nombre', 'password', 'confirmar', 'email'],
    //         self::ESCENARIO_UPDATE => ['nombre', 'password', 'confirmar', 'email'],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['nombre'], 'required'],
             [['password', 'confirmar'], 'required', 'on' => self::ESCENARIO_CREATE],
             [['nombre', 'password', 'confirmar', 'email'], 'string', 'max' => 255],
             [
                 ['confirmar'],
                 'compare',
                 'compareAttribute' => 'password',
                 'skipOnEmpty' => false,
                 'on' => [self::ESCENARIO_CREATE, self::ESCENARIO_UPDATE],
             ],
             [['nombre'], 'unique'],
             [['email'], 'default'],
             [['email'], 'email'],
             [['foto'], 'file', 'extensions' => 'jpg'],
         ];
    }

    public function getRutaImagen()
    {
        $nombre = Yii::getAlias('@uploads/') . $model->id . '.jpg';
        if (file_exists($nombre)) {
            return Url::to('/uploads/') . $model->id . '.jpg';
        }
        return Url::to('/uploads/') . 'default.jpg';
    }

    public function email()
    {
        $resultado = Yii::$app->mailer->compose('verificacion')
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Validación de tu cuenta de email')
            ->setTextBody('A traves del enlace de este correo verificaras tu cuenta de email')
            ->setHtmlBody(Html::a('verificar', Url::home('http') . 'usuarios/verificar?token_val=' . $this->token_val))
            ->send();
        if (!$resultado) {
            //no se ha enviado correctamente
        }
    }

    public function upload()
    {
        if ($this->foto === null) {
            return true;
        }
        $nombre = Yii::getAlias('@uploads/') . $this->id . '.jpg';
        $res = $this->foto->saveAs($nombre);
        if ($res) {
            Image::thumbnail($nombre, 80, null)->save($nombre);
        }
        return $res;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'password' => 'Contraseña',
            'email' => 'Email',
            'confirmar' => 'Confirmar contraseña',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public static function getPermitido()
    {
        return !Yii::$app->user->isGuest
            && in_array(Yii::$app->user->identity->nombre, ['pepe', 'juan']);
    }

    // public function getExiste()
    // {
    //     return
    // }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->auth_key = Yii::$app->security->generateRandomString();
                $this->token_val = Yii::$app->security->generateRandomString();
                if ($this->scenario === self::ESCENARIO_CREATE) {
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
            } else {
                if ($this->scenario === self::ESCENARIO_UPDATE) {
                    if ($this->password === '') {
                        $this->password = $this->getOldAttribute('password');
                    } else {
                        $this->password = Yii::$app->security->generatePasswordHash($this->password);
                    }
                }
            }
            return true;
        }
        return false;
    }
}
