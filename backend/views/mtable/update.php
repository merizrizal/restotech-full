<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Mtable */

$this->title = 'Update Meja: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Meja Ruangan ' . $model->mtableCategory->nama_category, 'url' => ['index', 'cid' => $model->mtable_category_id]];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mtable-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
