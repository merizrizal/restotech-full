<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\MenuSatuan */

$this->title = 'Update Satuan Menu: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Satuan Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="menu-satuan-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
