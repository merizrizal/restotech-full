<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\ItemCategory */

$this->title = 'Update Kategori Item: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kategori Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="item-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
