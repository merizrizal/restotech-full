<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDelivery */

$this->title = 'Update Penerimaan Item PO: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Item PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="supplier-delivery-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelSupplierDeliveryTrx' => $modelSupplierDeliveryTrx,
        'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
        'modelItem' => $modelItem,
        'modelItemSku' => $modelItemSku,
    ]) ?>

</div>
