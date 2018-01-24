<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\StockKoreksiSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-koreksi-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'item_sku_id') ?>

    <?= $form->field($model, 'storage_id') ?>

    <?= $form->field($model, 'storage_rack_id') ?>

    <?php // echo $form->field($model, 'jumlah') ?>

    <?php // echo $form->field($model, 'jumlah_awal') ?>

    <?php // echo $form->field($model, 'jumlah_adjustment') ?>

    <?php // echo $form->field($model, 'action') ?>

    <?php // echo $form->field($model, 'date_action') ?>

    <?php // echo $form->field($model, 'user_action') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'user_created') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'user_updated') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
