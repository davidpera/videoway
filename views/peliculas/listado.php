<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\DataColumn;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/** @var $this \yii\web\Link */
/** @var $dataProvider ActiveDataProvider*/
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => SerialColumn::className(),
            'header' => 'Número'
        ],
        'codigo',
        'titulo',
        [
            // 'class' => DataColumn::className(),
            'label' => 'Código + Título',
            'value' => function ($model, $key, $index, $column) {
                return $model->codigo . " " . $model->titulo;
            },
            'format' => 'text',
        ],
        [
            'class' => ActionColumn::className(),
            'header' => 'Acciones',
            'template' => '{delete} {update}',
            'buttons' =>[
                // 'devolver' => function ($url, $model, $key) {
                //     return Html::
                // },
                'delete' => function ($url, $model, $key) {
                    return Html::a(
                        'Borrar',
                        [
                            'peliculas/delete',
                            'id' => $model->id,
                        ],
                        [
                            'data-confirm' => '¿Seguro?',
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-danger',
                        ]);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a(
                        'Cambiar',
                        [
                            'peliculas/update',
                            'id' => $model->id,
                        ],
                        [
                            'class' => 'btn btn-xs btn-success'
                        ]);
                }
            ]
        ],
    ]
]);?>
