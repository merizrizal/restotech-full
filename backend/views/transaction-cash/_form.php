<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\models\TransactionAccount;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\TransactionCash */
/* @var $form yii\widgets\ActiveForm */

yii\widgets\MaskedInputAsset::register($this);
kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

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
                <div class="transaction-cash-form">

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
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create', 'type' => $type], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>

                    <?= $form->field($model, 'account_id')->dropDownList(
                            ArrayHelper::map(
                                TransactionAccount::find()->orderBy('nama_account')->andWhere(['account_type' => $type])->asArray()->all(), 
                                'id', 
                                function($data) { 
                                    return $data['nama_account'] . ' (' . $data['id'] . ')';                                 
                                }
                            ), 
                            [
                                'prompt' => '',
                                'style' => 'width: 100%'
                            ]) ?>

                    <?= $form->field($model, 'date', [
                        'parts' => [
                            '{inputClass}' => 'col-lg-8'
                        ],
                    ])->widget(DatePicker::className(), [
                        'pluginOptions' => Yii::$app->params['datepickerOptions'],
                    ]) ?>

                    <?= $form->field($model, 'jumlah', [
                        'parts' => [
                            '{inputClass}' => 'col-lg-7'
                        ],
                    ])->widget(MaskMoney::className()) ?>

                    <?= $form->field($model, 'reference_id')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 6]) ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index', 'type' => $type], ['class' => 'btn btn-default']); ?>
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
$jscript = '
    $("#transactioncash-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    
    $("#transactioncash-account_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
';

$this->registerJs($jscript); ?>