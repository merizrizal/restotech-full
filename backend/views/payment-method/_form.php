<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\PaymentMethod */
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
                <div class="payment-method-form">

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
                        echo $form->field($model, 'id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput(['maxlength' => true, 'readonly' => 'readonly']);
                    } ?>

                    <?= $form->field($model, 'nama_payment')->textInput(['maxlength' => 32]) ?>
                    
                    <?= $form->field($model, 'type')->radioList(
                        [
                            'Purchase' => 'Purchase', 
                            'Sale' => 'Sale',                           
                        ], 
                        [
                            'separator' => '<br>'
                        ]) ?>
                    
                    <?= $form->field($model, 'method')->radioList(
                        [
                            'Cash' => 'Cash',
                            'Credit-Card' => 'Credit Card',
                            'Debit-Card' => 'Debit Card',
                            'Account-Receiveable' => 'Account Receiveable',
                            'Account-Payable' => 'Account Payable',                         
                        ], 
                        [
                            'separator' => '<br>'
                        ]) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 6]) ?>
                    
                    <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>

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

<?php 

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
   
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$this->registerJs(Yii::$app->params['checkbox-radio-script']()); ?>
