<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Socios */
/* @var $pelicuals app\models\Peliculas[] */

$this->title = $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="socios-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Gestionar', ['alquileres/gestionar', 'numero' => $model->numero], ['class' => 'btn btn-primary']) ?>
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
            'numero',
            'nombre',
            'direccion',
            'telefono',
        ],
    ]) ?>

    <h3>Últimas películas alquiladas</h3>

    <table class="table">
        <thead>
            <th>Código</th>
            <th>Título</th>
            <th>Fecha de alquiler</th>
            <th>Devolución</th>
        </thead>
        <tbody>
            <?php foreach ($alquileres as $alquiler): ?>
                <tr>
                    <td><?= Html::a(
                        Html::encode($alquiler->pelicula->codigo), ['peliculas/view', 'id'=>$alquiler->pelicula->id]
                    ) ?></td>
                    <td><?= Html::a(
                        Html::encode($alquiler->pelicula->titulo), ['peliculas/view', 'id'=>$alquiler->pelicula->id]
                    ) ?></td>
                    <td><?= Html::encode(Yii::$app->formatter->asDatetime($alquiler->created_at)) ?></td>
                    <?php $pendientes = $alquiler->socio->getPendientes()->with('pelicula') ?>
                    <?php foreach ($pendientes->each() as $pendiente): ?>
                        <?php if ($pendiente->pelicula->id === $alquiler->pelicula->id): ?>
                            <?= Html::beginForm(['alquileres/devolver', 'numero' => $alquiler->socio->numero], 'post') ?>
                                <?= Html::hiddenInput('id',$pendiente->id) ?>
                                <td><?= Html::submitButton('Devolver', ['class' => 'btn-xs btn-danger']) ?></td>
                            <?= Html::endForm() ?>
                        <?php endif ?>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</div>
