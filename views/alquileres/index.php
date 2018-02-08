<?php

use kartik\datecontrol\DateControl;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AlquileresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Alquileres';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alquileres-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Alquileres', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'socio.numero',
            'socio.nombre',
            'pelicula.codigo',
            'pelicula.titulo',
            [
                'attribute' => 'created_at',
                'filter' => DateControl::widget([
                    'type' => DateControl::FORMAT_DATE,
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'readonly' => true,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'autoclose' => true,
                            'weekStart' => 1,
                        ]
                    ],
                ]),
                'format' => 'datetime'
            ],
            'devolucion:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
