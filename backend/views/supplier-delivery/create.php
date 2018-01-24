<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDelivery */

$this->title = 'Create Penerimaan Item PO';
$this->params['breadcrumbs'][] = ['label' => 'Penerimaan Item PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-delivery-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelSupplierDeliveryTrx' => $modelSupplierDeliveryTrx,
        'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
        'modelItem' => $modelItem,
        'modelItemSku' => $modelItemSku,
    ]) ?>

</div>
