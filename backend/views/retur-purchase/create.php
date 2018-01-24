<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\ReturPurchase */

$this->title = 'Create Retur PO';
$this->params['breadcrumbs'][] = ['label' => 'Retur PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="retur-purchase-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelReturPurchaseTrx' => $modelReturPurchaseTrx,
        'modelItem' => $modelItem,
        'modelItemSku' => $modelItemSku,
    ]) ?>

</div>
