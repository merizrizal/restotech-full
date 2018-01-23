<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDeliveryInvoice */

$this->title = 'Create Invoice Penerimaan PO';
$this->params['breadcrumbs'][] = ['label' => 'Invoice Penerimaan PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-delivery-invoice-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
