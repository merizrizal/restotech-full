<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\ItemCategory */

$this->title = 'Create Kategori Item';
$this->params['breadcrumbs'][] = ['label' => 'Kategori Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
