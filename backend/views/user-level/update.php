<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\UserLevel */

$this->title = 'Update User Level: ' . ' ' . $model->nama_level;
$this->params['breadcrumbs'][] = ['label' => 'User Level', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_level, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-level-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelUserAppModule' => $modelUserAppModule,
    ]) ?>

</div>
