<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\MtableCategory */

$this->title = 'Create Ruangan';
$this->params['breadcrumbs'][] = ['label' => 'Ruangan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mtable-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
