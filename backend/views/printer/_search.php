<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\search\PrinterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="printer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'printer') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'user_created') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?= $form->field($model, 'user_updated') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
