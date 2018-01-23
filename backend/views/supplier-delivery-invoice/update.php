<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDeliveryInvoice */

$this->title = 'Update Invoice Penerimaan PO: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoice Penerimaan PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="supplier-delivery-invoice-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
