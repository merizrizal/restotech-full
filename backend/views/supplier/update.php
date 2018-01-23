<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Supplier */

$this->title = 'Update Supplier: ' . ' ' . $model->kd_supplier;
$this->params['breadcrumbs'][] = ['label' => 'Supplier', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->kd_supplier, 'url' => ['view', 'id' => $model->kd_supplier]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="supplier-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
