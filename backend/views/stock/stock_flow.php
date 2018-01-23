<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use restotech\standard\backend\models\Item;
use restotech\standard\backend\models\Storage;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Stock */

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

endif;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['stock-movement/index', 'type' => $flow]];
$this->params['breadcrumbs'][] = 'Input Stok'; ?>

<div class="stock-create">        

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="stock-movement-form">

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
                        
                        <?= $form->field($model, 'tanggal', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-8'
                                ],
                            ])->widget(DatePicker::className(), [
                                'pluginOptions' => Yii::$app->params['datepickerOptions'],
                            ]) ?>                                                
                        
                        <?= $form->field($model, 'item_id')->dropDownList(
                                ArrayHelper::map(
                                    Item::find()->where(['not_active' => 0])->asArray()->all(), 
                                    'id', 
                                    function($data) { 
                                        return $data['nama_item'] . ' (' . $data['id'] . ')';                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 100%'
                                ]
                            ) ?>

                        <?= $form->field($model, 'item_sku_id')->textInput(['maxlength' => 16, 'style' => 'width: 100%']) ?>
                                                
                        <?php                         
                        
                        if ($flow == 'Outflow' || $flow == 'Transfer') { 
                            echo $form->field($model, 'storage_from')->dropDownList(
                                    ArrayHelper::map(
                                        Storage::find()->orderBy('nama_storage')->asArray()->all(), 
                                        'id', 
                                        function($data) { 
                                            return  $data['nama_storage'] . ' (' . $data['id'] . ')';
                                        }
                                    ), 
                                    [
                                        'prompt' => '',
                                        'style' => 'width: 100%'
                                    ]
                                );
                        }                
                        
                        if ($flow == 'Outflow' || $flow == 'Transfer') { 
                            echo $form->field($model, 'storage_rack_from')->textInput(['maxlength' => 20, 'style' => 'width: 100%']);
                        }
                        
                        if ($flow == 'Inflow' || $flow == 'Transfer') { 
                            echo $form->field($model, 'storage_to')->dropDownList(
                                    ArrayHelper::map(
                                        Storage::find()->orderBy('nama_storage')->asArray()->all(), 
                                        'id', 
                                        function($data) { 
                                            return $data['nama_storage'] . ' (' . $data['id'] . ')';
                                        }
                                    ), 
                                    [
                                        'prompt' => '',
                                        'style' => 'width: 100%'
                                    ]
                                );
                        }
                        
                        if ($flow == 'Inflow' || $flow == 'Transfer') { 
                            echo $form->field($model, 'storage_rack_to')->textInput(['maxlength' => 20, 'style' => 'width: 100%']);
                        } ?>

                        <?= $form->field($model, 'jumlah', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->textInput() ?>
                        
                        <?= $form->field($model, 'reference')->textInput(['maxlength' => 20]) ?>
                        
                        <?= $form->field($model, 'keterangan')->textarea(['rows' => 3]) ?>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['stock-movement/index', 'type' => $flow], ['class' => 'btn btn-default']); ?>
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

</div>

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);   

$jscript = '
    $("#stockmovement-tanggal").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    
    $("#stockmovement-item_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    var itemSku = function(remoteData) {
        $("#stockmovement-item_sku_id").val(null);
        $("#stockmovement-item_sku_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };

    itemSku([]);

    $("#stockmovement-item_id").on("select2:select", function(e) {
        $("input#stockmovement-item_sku_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-sku/get-sku-item']) . '?id=" + $("#stockmovement-item_id").select2("data")[0].id,
            success: function(response) {
                itemSku(response);
            }
        });
    });

    $("#stockmovement-item_id").on("select2:unselect", function(e) {
        $("#stockmovement-item_sku_id").val(null).trigger("change");
        itemSku([]);
    });
';

if ($flow == 'Outflow' || $flow == 'Transfer') { 
    
    $jscript .= '
        $("#stockmovement-storage_from").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true
        });

        var storageRackFrom = function(remoteData) {
            $("#stockmovement-storage_rack_from").val(null);
            $("#stockmovement-storage_rack_from").select2({
                theme: "krajee",
                placeholder: "Pilih",
                allowClear: true,
                data: remoteData,
            });
        };

        storageRackFrom([]);

        $("#stockmovement-storage_from").on("select2:select", function(e) {
            $("input#stockmovement-storage_rack_from").val(null).trigger("change");

            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=" + $("#stockmovement-storage_from").select2("data")[0].id,
                success: function(response) {
                    storageRackFrom(response);
                }
            });
        });

        $("#stockmovement-storage_from").on("select2:unselect", function(e) {
            $("#stockmovement-storage_rack_from").val(null).trigger("change");
            storageRackFrom([]);
        });
    ';
}

if ($flow == 'Inflow' || $flow == 'Transfer') {
    
    $jscript .= '
        $("#stockmovement-storage_to").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true
        });

        var storageRackTo = function(remoteData) {
            $("#stockmovement-storage_rack_to").val(null);
            $("#stockmovement-storage_rack_to").select2({
                theme: "krajee",
                placeholder: "Pilih",
                allowClear: true,
                data: remoteData,
            });
        };

        storageRackTo([]);
        
        $("#stockmovement-storage_to").on("select2:select", function(e) {
            $("input#stockmovement-storage_rack_to").val(null).trigger("change");

            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=" + $("#stockmovement-storage_to").select2("data")[0].id,
                success: function(response) {
                    storageRackTo(response);
                }
            });
        });
        
        $("#stockmovement-storage_to").on("select2:unselect", function(e) {
            $("#stockmovement-storage_rack_to").val(null).trigger("change");
            storageRackTo([]);
        });
    ';
}

if (!$model->isNewRecord || $status == 'danger') {
    
    $jscript .= '
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-sku/get-sku-item']) . '?id=" + $("#stockmovement-item_id").select2("data")[0].id,
            success: function(response) {                
                itemSku(response);
                $("input#stockmovement-item_sku_id").val("' . $model->item_sku_id . '").trigger("change");
            }
        });
    ';
    
    if ($flow == 'Outflow' || $flow == 'Transfer') { 
        
        $jscript .= '
            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=" + $("#stockmovement-storage_from").select2("data")[0].id,
                success: function(response) {                
                    storageRackFrom(response);
                    $("input#stockmovement-storage_rack_from").val("' . $model->storage_rack_from . '").trigger("change");
                }
            });
        ';
    }
    
    if ($flow == 'Inflow' || $flow == 'Transfer') {
        
        $jscript .= '
            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=" + $("#stockmovement-storage_to").select2("data")[0].id,
                success: function(response) {                
                    storageRackTo(response);
                    $("input#stockmovement-storage_rack_to").val("' . $model->storage_rack_to . '").trigger("change");
                }
            });
        ';
    }
}

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>