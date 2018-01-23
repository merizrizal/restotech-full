<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\DirectPurchase */

$this->title = 'Create Pembelian Langsung';
$this->params['breadcrumbs'][] = ['label' => 'Pembelian Langsung', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="direct-purchase-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelDirectPurchaseTrx' => $modelDirectPurchaseTrx,
    ]) ?>

</div>
