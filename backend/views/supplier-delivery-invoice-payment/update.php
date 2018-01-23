<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDeliveryInvoicePayment */

$this->title = 'Update Supplier Delivery Invoice Payment: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Supplier Delivery Invoice Payment', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="supplier-delivery-invoice-payment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
