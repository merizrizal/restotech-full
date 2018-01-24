<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use restotech\standard\backend\models\Storage;
use restotech\standard\backend\models\Stock;
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

$this->title = 'Stok Konversi';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['stock-movement/convert']];
$this->params['breadcrumbs'][] = 'Input Stok'; ?>

<?php $form = ActiveForm::begin([
    'id' => 'formStockConvert',
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

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Dari</h3>
                </div>
                <div class="box-body">
                    <div class="stock-form">
                        
                        <?= $form->field($modelStock, 'item_id')->dropDownList(
                                ArrayHelper::map(                                        
                                    Stock::find()->joinWith('item')->where(['item.not_active' => 0])->asArray()->all(), 
                                    function($data) { 
                                        return $data['item']['id'];                                 
                                    }, 
                                    function($data) { 
                                        return $data['item']['nama_item'] . ' (' . $data['item']['id'] . ')';                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 100%'
                                ]
                            ) ?>
                        
                        <?= $form->field($modelStock, 'item_sku_id')->textInput(['maxlength' => 16, 'style' => 'width: 100%']) ?>
                        
                        <?= $form->field($modelStock, 'storage_id')->textInput(['maxlength' => 12, 'style' => 'width: 100%']) ?>
                        
                        <?= $form->field($modelStock, 'storage_rack_id')->textInput(['style' => 'width: 100%']) ?>
                        
                        <?= $form->field($modelStock, 'jumlah_stok', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput() ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Konversi Ke</h3>
                </div>
                <div class="box-body">
                    <div class="stock-movement-form">                        
                        
                        <?= $form->field($model, 'tanggal', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-8'
                                ],
                            ])->widget(DatePicker::className(), [
                                'pluginOptions' => Yii::$app->params['datepickerOptions'],
                            ]) ?>                                                                        

                        <?= $form->field($model, 'item_sku_id')->textInput(['maxlength' => 16, 'style' => 'width: 100%']) ?>
                                                
                        <?= $form->field($model, 'storage_to')->dropDownList(                                
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
                            ) ?>
                        
                        <?= $form->field($model, 'storage_rack_to')->textInput(['maxlength' => 20, 'style' => 'width: 100%']) ?>

                        <?= $form->field($model, 'jumlah', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->textInput(['readonly' => 'readonly']) ?>
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <div class="col-lg-12">
                                        <?= Html::a('<i class="fa fa-exchange"></i> Convert', null, ['class' => 'btn btn-default btn-sm', 'id' => 'btnConvert']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
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
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['stock-movement/convert'], ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
                        </div>                        

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->

<?php ActiveForm::end(); ?>
    
<?php
$notifConvert = new NotificationDialog([
    'status' => 'danger',
    'message1' => 'Data harap diisi',
    'message2' => 'SKU "Dari", SKU "Konversi Ke" atau Jumlah Stok ada yang belum diisi',
    'id' => 'notifConvert',
]);

echo $notifConvert->renderDialog(); ?>
    
<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);    

$jscript = '
    $("#stock-item_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    var itemSkuStock = function(remoteData) {
        $("#stock-item_sku_id").val(null);
        $("#stock-item_sku_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };

    itemSkuStock([]);
    
    $("#stock-item_id").on("select2:select", function(e) {
        $("input#stock-item_sku_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'stock/get-sku-item']) . '?id=" + $("#stock-item_id").select2("data")[0].id,
            success: function(response) {
                itemSkuStock(response);
            }
        });
        
        $("input#stock-storage_id").val(null).trigger("change");
        storageStock([]);
        
        $("#stock-storage_rack_id").val(null).trigger("change");
        storageRackStock([]);
        
         $("#stockmovement-item_sku_id").val(null).trigger("change");
        itemSku([]);
    });

    $("#stock-item_id").on("select2:unselect", function(e) {
        $("#stock-item_sku_id").val(null).trigger("change");
        itemSkuStock([]);
        
        $("input#stock-storage_id").val(null).trigger("change");
        storageStock([]);
        
        $("#stock-storage_rack_id").val(null).trigger("change");
        storageRackStock([]);
        
        $("#stockmovement-item_sku_id").val(null).trigger("change");
        itemSku([]);
    });
    
    var storageStock = function(remoteData) {
        $("#stock-storage_id").val(null);
        $("#stock-storage_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };

    storageStock([]);
    
    $("#stock-item_sku_id").on("select2:select", function(e) {
        $("input#stock-storage_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'stock/get-storage']) . '?id=" + $("#stock-item_sku_id").select2("data")[0].id,
            success: function(response) {
                storageStock(response);
            }
        });
        
        $("#stock-storage_rack_id").val(null).trigger("change");
        storageRackStock([]);
        
        $("input#stockmovement-item_sku_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'stock/get-sku-item-descent']) . '?iid=" + $("#stock-item_id").select2("data")[0].id + "&isid=" + $("#stock-item_sku_id").select2("data")[0].id,
            success: function(response) {
                itemSku(response);
            }
        });
    });

    $("#stock-item_sku_id").on("select2:unselect", function(e) {
        $("#stock-storage_id").val(null).trigger("change");
        storageStock([]);
        
        $("#stock-storage_rack_id").val(null).trigger("change");
        storageRackStock([]);
        
        $("#stockmovement-item_sku_id").val(null).trigger("change");
        itemSku([]);
    });
    
    var storageRackStock = function(remoteData) {
        $("#stock-storage_rack_id").val(null);
        $("#stock-storage_rack_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };

    storageRackStock([]);
    
    $("#stock-storage_id").on("select2:select", function(e) {
        $("input#stock-storage_rack_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'stock/get-storage-rack']) . '?sid=" + $("#stock-storage_id").select2("data")[0].id + "&isid=" + $("#stock-item_sku_id").select2("data")[0].id + "&iid=" + $("#stock-item_id").select2("data")[0].id,
            success: function(response) {
                storageRackStock(response);
            }
        });
    });

    $("#stock-storage_id").on("select2:unselect", function(e) {
        $("#stock-storage_rack_id").val(null).trigger("change");
        storageRackStock([]);
    });
    

    
    $("#stockmovement-tanggal").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});        
    
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
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['standard'] . 'storage-rack/get-storage-rack']) . '?id=" + $("#stockmovement-storage_to").select2("data")[0].id,
            success: function(response) {
                storageRackTo(response);
            }
        });
    });

    $("#stockmovement-storage_to").on("select2:unselect", function(e) {
        $("#stockmovement-storage_rack_to").val(null).trigger("change");
        storageRackTo([]);
    });
    
    $("a#btnConvert").on("click", function(e) {
    
        if ($("#stock-item_sku_id").val() == "" || $("#stockmovement-item_sku_id").val() == "" || $("#stock-jumlah_stok").val() == "" || $(".field-stock-jumlah_stok").hasClass("has-error")) {
            
            $("#notifConvert").modal();
        } else {
        
            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['standard'] . 'item-sku/get-jumlah-convert']) . '?iid=" + $("#stock-item_id").select2("data")[0].id  + "&isidfrom=" + $("#stock-item_sku_id").val() + "&isidto=" + $("#stockmovement-item_sku_id").val(),
                success: function(response) {
                    
                    $("#stockmovement-jumlah").val(parseFloat($("#stock-jumlah_stok").val()) * parseFloat(response.jumlah));
                }
            });
        }
        
        return false;
    });
    
    $("#formStockConvert").yiiActiveForm("add", {
        id: "stockmovement-storage_to",
        name: "StockMovement[storage_to]",
        container: ".field-stockmovement-storage_to",
        input: "#stockmovement-storage_to",
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message":"To Storage tidak boleh kosong."});
        }
    });
    
    $("#formStockConvert").yiiActiveForm("add", {
        id: "stockmovement-jumlah",
        name: "StockMovement[jumlah]",
        container: ".field-stockmovement-jumlah",
        input: "#stockmovement-jumlah",
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message":"Jumlah tidak boleh kosong."});
        }
    });
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>