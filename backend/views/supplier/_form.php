<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Supplier */
/* @var $form yii\widgets\ActiveForm */

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) : 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif; ?>

<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="box box-danger">
            <div class="box-body">
                <div class="supplier-form">

                    <?php $form = ActiveForm::begin([
                            'options' => [
                                
                            ],
                            'fieldConfig' => [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-12'
                                ],
                                'template' => '<div class="row">'
                                                . '<div class="col-lg-3">'
                                                    . '{label}'
                                                . '</div>'
                                                . '<div class="col-lg-6">'
                                                    . '<div class="{inputClass}">'
                                                        . '{input}'
                                                    . '</div>'
                                                . '</div>'
                                                . '<div class="col-lg-3">'
                                                    . '{error}'
                                                . '</div>'
                                            . '</div>', 
                            ]
                    ]); ?>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!$model->isNewRecord)
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create'], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (!$model->isNewRecord) {
                        
                        echo $form->field($model, 'kd_supplier', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                            'enableAjaxValidation' => true
                        ])->textInput(['maxlength' => true, $model->isNewRecord ? '' : 'readonly' => $model->isNewRecord ? '' : 'readonly']);
                        
                    } ?>

                    <?= $form->field($model, 'nama')->textInput(['maxlength' => 48]) ?>

                    <?= $form->field($model, 'alamat')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'telp', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <?= $form->field($model, 'fax', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 2]) ?>

                    <?= $form->field($model, 'kontak1')->textInput(['maxlength' => 48]) ?>

                    <?= $form->field($model, 'kontak1_telp', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <?= $form->field($model, 'kontak2')->textInput(['maxlength' => 48]) ?>

                    <?= $form->field($model, 'kontak2_telp', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <?= $form->field($model, 'kontak3')->textInput(['maxlength' => 48]) ?>

                    <?= $form->field($model, 'kontak3_telp', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <?= $form->field($model, 'kontak4')->textInput(['maxlength' => 48]) ?>

                    <?= $form->field($model, 'kontak4_telp', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2"></div>
</div><!-- /.row -->
