<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PeliculasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Peliculas';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('http://172.36.0.169/js/jsCliente.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<object id="div" type="text/html" class="objeto"> </object>
