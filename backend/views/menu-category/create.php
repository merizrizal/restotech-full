<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\MenuCategory */

$this->title = 'Create Kategori Menu';
$this->params['breadcrumbs'][] = ['label' => 'Kategori Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
