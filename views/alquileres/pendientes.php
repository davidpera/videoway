<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;

?>

<?php if (!$pendientes->exists()): ?>
    <h3>No tiene alquileres pendientes</h3>
<?php else: ?>
    <h3>Alquileres pendientes</h3>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $pendientes,
            'pagination' => false,
            'sort' => false,
        ]),
        'columns' => [
            'pelicula.codigo',
            'pelicula.titulo',
            'created_at:datetime',
            [
                'class' => ActionColumn::className(),
                'template' => '{devolver}',
                'header' => 'Devolver',
                'buttons' => [
                    'devolver' => function ($url, $model, $key) {
                        return Html::beginForm(
                            ['alquileres/devolver-ajax'],
                            'post'
                        )
                        .Html::hiddenInput('id', $model->id)
                        .Html::submitButton(
                            'Devolver',
                            ['class' => 'btn-xs btn-danger']
                        )
                        .Html::endForm();
                    },
                ],
            ],
        ],
    ]) ?>
<?php endif ?>
