<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\MenuCategory */

$this->title = 'Update Kategori Menu: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kategori Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="menu-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
