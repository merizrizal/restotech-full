<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\PaymentMethod */

$this->title = 'Update Metode Pembayaran: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Metode Pembayaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payment-method-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
