<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\StorageRack */

$this->title = 'Update Rak';
$this->params['breadcrumbs'][] = ['label' => 'Rak  - ' . $model->storage->nama_storage, 'url' => ['index', 'sid' => $model->storage_id]];
$this->params['breadcrumbs'][] = 'Update'; ?>

<div class="storage-rack-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
