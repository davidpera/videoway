<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<h2>A traves de este enlace verificaras la cuenta</h2>
<?php $url = Url::home('http') . 'usuarios/verificar'; ?>
<?= Html::a('Verificación', $url) ?>
