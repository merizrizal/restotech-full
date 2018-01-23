<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Settings */

$this->title = 'Update Settings: ' . ' ' . $model->setting_id;
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->setting_id, 'url' => ['view', 'id' => $model->setting_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="settings-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
