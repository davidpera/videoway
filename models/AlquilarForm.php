<?php

namespace app\models;

use yii\base\Model;

class AlquilarForm extends Model
{
    /**
     * El numero del socio.
     * @var string
     */
    public $numero;

    /**
     * El codigo de la pelicula.
     * @var string
     */
    public $codigo;

    public function rules()
    {
        return [
            [['numero', 'codigo'], 'required'],
            [['numero', 'codigo'], 'number'],
            [
                ['numero'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Socios::className(),
                'targetAttribute' => ['numero' => 'numero'],
            ],
            [
                ['codigo'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Peliculas::className(),
                'targetAttribute' => ['codigo' => 'codigo'],
            ],
            [['codigo'], function ($attribute, $params, $validator) {
                if (Peliculas::findOne(['codigo' => $this->codigo])->estaAlquilada) {
                    $this->addError($attribute, 'La película ya está alquilada');
                }
            }],
        ];
    }
}
