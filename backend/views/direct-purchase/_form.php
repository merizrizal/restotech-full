<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\components\DynamicFormField;
use restotech\standard\backend\models\Item;
use restotech\standard\backend\models\Storage;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\DirectPurchase */
/* @var $form yii\widgets\ActiveForm */

yii\widgets\MaskedInputAsset::register($this);

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

<?php 

$form = ActiveForm::begin([
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
]); 

    $dynamicFormDPTrx = new DynamicFormField([
        'dataModel' => $modelDirectPurchaseTrx,
        'form' => $form,
        'formFields' => [
            'item_id' => [
                'type' => 'dropdown',
                'data' => ArrayHelper::map(
                        
                        Item::find()->orderBy('nama_item')->asArray()->all(), 
                        'id', 
                        function($data) { 
                            return $data['nama_item'] . ' (' . $data['id'] . ')';                                 
                        }
                ),
                'affect' => [
                    'field' => 'item_sku_id',
                    'url' => Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-sku/get-sku-item']),
                ],
                'colOption' => 'style="width: 25%"',
                'existIsDisabled' => true,
            ],
            'item_sku_id' => [
                'type' => 'textinput-dropdown',
                'colOption' => 'style="width: 15%"',
                'existIsDisabled' => true,
            ],
            'jumlah_item' => [
                'type' => 'textinput',
                'colOption' => 'style="width: 10%"',
                'existIsDisabled' => true,
            ],
            'harga_satuan' => [
                'type' => 'money',
                'colOption' => 'style="width: 13%"',
                'existIsDisabled' => true,
            ],
            'storage_id' => [
                'type' => 'dropdown',
                'data' => ArrayHelper::map(
                        
                        Storage::find()->orderBy('nama_storage')->asArray()->all(), 
                        'id', 
                        function($data) { 
                            return  $data['nama_storage'] . ' (' . $data['id'] . ')';
                        }
                ),
                'affect' => [
                    'field' => 'storage_rack_id',
                    'url' => Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']),
                ],
                'colOption' => 'style="width: 23%"',
                'existIsDisabled' => true,
            ],
            'storage_rack_id' => [
                'type' => 'textinput-dropdown',
                'colOption' => 'style="width: 13%"',
                'existIsDisabled' => true,
            ],
        ],
        'title' => 'Item',
        'columnClass' => 'col-sm-12'
    ]); ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="direct-purchase-form">                    

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

                        <?= $form->field($model, 'date', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-8'
                                ],
                            ])->widget(DatePicker::className(), [
                                'pluginOptions' => Yii::$app->params['datepickerOptions'],
                            ]) ?>

                        <?php
                        if (!$model->isNewRecord) {
                            echo $form->field($model, 'jumlah_item', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->textInput(['readonly' => 'readonly']); 
                        } ?>

                        <?php
                        if (!$model->isNewRecord) {
                            echo $form->field($model, 'jumlah_harga', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->widget(MaskMoney::className(), ['readonly' => 'readonly']);
                        } ?>

                        <?= $form->field($model, 'reference')->textInput(['maxlength' => true]) ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); 

                                    if (!$model->isNewRecord) {
                                      echo '&nbsp;&nbsp;&nbsp;';
                                      echo Html::a('<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print', ['print', 'id' => $model->id], ['class' => 'btn btn-success']);
                                    } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->
    
    <?= $dynamicFormDPTrx->component(); ?>
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-footer">
                    <?php
                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); 

                    if (!$model->isNewRecord) {
                      echo '&nbsp;&nbsp;&nbsp;';
                      echo Html::a('<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print', ['print', 'id' => $model->id], ['class' => 'btn btn-success']);
                    } ?>
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
$jscript = '    
    $("#directpurchase-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
            
    $("#directpurchase-jumlah_harga-disp").off("keypress");
    $("#directpurchase-jumlah_harga-disp").off("keyup");
';

$this->registerJs($jscript); ?>