<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\PurchaseOrder */

$this->title = 'Update Purchase Order: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="purchase-order-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
    ]) ?>

</div>
