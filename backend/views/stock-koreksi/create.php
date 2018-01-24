<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\StockKoreksi */

$this->title = 'Input Stock Koreksi';
$this->params['breadcrumbs'][] = ['label' => 'Stock', 'url' => ['stock/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-koreksi-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
