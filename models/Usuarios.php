<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['confirmar']);
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
         ];
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