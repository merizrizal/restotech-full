<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Voucher */

$this->title = 'Update Voucher: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Voucher', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="voucher-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
