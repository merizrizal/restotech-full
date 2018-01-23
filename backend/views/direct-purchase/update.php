<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\DirectPurchase */

$this->title = 'Update Pembelian Langsung: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pembelian Langsung', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="direct-purchase-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelDirectPurchaseTrx' => $modelDirectPurchaseTrx,
    ]) ?>

</div>
