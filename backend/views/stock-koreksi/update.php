<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\StockKoreksi */

$this->title = 'Update Stock Koreksi: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Stock Koreksi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="stock-koreksi-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
