<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\PurchaseOrder */

$this->title = 'Create Purchase Order';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-order-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
    ]) ?>

</div>
