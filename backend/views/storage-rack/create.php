<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\StorageRack */

$this->title = 'Create Rak';
$this->params['breadcrumbs'][] = ['label' => 'Rak  - ' . $model->storage->nama_storage, 'url' => ['index', 'sid' => $model->storage_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-rack-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
