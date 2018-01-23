<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Storage */

$this->title = 'Update Gudang: ' . ' (' . $model->id . ') ' . $model->nama_storage;
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => ' (' . $model->id . ') ' . $model->nama_storage, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="storage-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
