<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SaldoKasir */

$this->title = 'Create Saldo Kasir';
$this->params['breadcrumbs'][] = ['label' => 'Saldo Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="saldo-kasir-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
