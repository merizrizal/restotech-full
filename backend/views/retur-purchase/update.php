<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\ReturPurchase */

$this->title = 'Update Retur PO: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Retur PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="retur-purchase-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelReturPurchaseTrx' => $modelReturPurchaseTrx,
        'modelItem' => $modelItem,
        'modelItemSku' => $modelItemSku,
    ]) ?>

</div>
