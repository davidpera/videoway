<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Socios */

$this->title = 'Registro de usuario';
$this->params['breadcrumbs'][] = ['label' => 'Socios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
