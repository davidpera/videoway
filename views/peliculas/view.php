<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Peliculas */

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = ['label' => 'Peliculas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="peliculas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codigo',
            'titulo',
            'precio_alq',
        ],
    ]) ?>

    <h3>Últimos socios que alquilaron esta película</h3>

     <!--GridView::widget([
        'dataProvider'=> new ActiveDataProvider([
            'query'=> $alquileres,
            'pagination'=>false,
            'sort'=>false,
        ]),
        'columns' => [
            'socio.numero',
            'socio.nombre',
            'created_at'
        ]
    ]) -->

    <table class="table">
        <thead>
            <th>Número</th>
            <th>Nombre</th>
            <th>Fecha de alquiler</th>
        </thead>
        <tbody>
            <?php $cont = 0 ?>
            <?php foreach ($alquileres as $alquiler): ?>
                <tr>
                    <td><?= Html::a(
                        Html::encode($alquiler->socio->numero), ['socios/view', 'id'=>$alquiler->socio->id]
                    ) ?></td>
                    <td><?= Html::a(
                        Html::encode($alquiler->socio->nombre), ['socios/view', 'id'=>$alquiler->socio->id]
                    ) ?></td>
                    <td><?= Html::encode(Yii::$app->formatter->asDatetime($alquiler->created_at)) ?></td>
                    <?php $pendiente = $model->getPendiente()->one() ?>
                    <?php if(isset($pendiente)): ?>
                        <?php if ($pendiente->socio->id === $alquiler->socio->id && $cont === 0): ?>
                            <?= Html::beginForm(['alquileres/devolver', 'numero' => $pendiente->socio->numero], 'post') ?>
                                <?= Html::hiddenInput('id',$pendiente->id) ?>
                                <td><?= Html::submitButton('Devolver', ['class' => 'btn-xs btn-danger']) ?></td>
                            <?= Html::endForm() ?>
                            <?php $cont += 1 ?>
                        <?php endif ?>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</div>
