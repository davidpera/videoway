<?php

namespace app\models;

use yii\base\Model;

class GestionarSocioForm extends Model
{
    /**
     * Numero del socio.
     * @var string
     */
    public $numero;

    public function formName()
    {
        return '';
    }

    public function attributeLabels()
    {
        return [
            'numero' => 'Número de socio',
        ];
    }

    public function rules()
    {
        return [
            [['numero'], 'required'],
            [['numero'], 'default'],
            [['numero'], 'integer'],
            [
                ['numero'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Socios::className(),
                'targetAttribute' => ['numero' => 'numero'],
            ],
        ];
    }
}
