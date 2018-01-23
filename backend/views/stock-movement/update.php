<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\StockMovement */

$title = '';

switch ($model->type) {
    case 'Inflow':
        $title = 'Stok Masuk';
        break;
    
    case 'Outflow':
        $title = 'Stok Keluar';
        break;
    
    case 'Transfer':
        $title = 'Stok Transfer';
        break;
    
    default:
        break;
}

$this->title = 'Update ' . $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index', 'type' => $model->type, 'date' => 'selected', 'StockMovementSearch[tanggal]' => $model->tanggal]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="stock-movement-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
