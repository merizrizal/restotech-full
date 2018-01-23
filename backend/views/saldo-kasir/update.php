<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SaldoKasir */

$this->title = 'Update Saldo Kasir: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Saldo Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="saldo-kasir-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
