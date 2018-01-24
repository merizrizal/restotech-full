<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\search\StockMovementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-movement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'item_sku_id') ?>

    <?= $form->field($model, 'storage_from') ?>

    <?php // echo $form->field($model, 'storage_rack_from') ?>

    <?php // echo $form->field($model, 'storage_to') ?>

    <?php // echo $form->field($model, 'storage_rack_to') ?>

    <?php // echo $form->field($model, 'jumlah') ?>

    <?php // echo $form->field($model, 'reference') ?>

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
