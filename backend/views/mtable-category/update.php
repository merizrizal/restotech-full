<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\MtableCategory */

$this->title = 'Update Ruangan: ' . ' ' . $model->nama_category;
$this->params['breadcrumbs'][] = ['label' => 'Ruangan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_category, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mtable-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
