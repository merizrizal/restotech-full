<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\models\SupplierDelivery;
use restotech\standard\backend\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDeliveryInvoice */
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

<?php $form = ActiveForm::begin([
    'id' => 'formSupplierDeliveryInvoice',
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
                <div class="box-body">
                    <div class="supplier-delivery-invoice-form">                   

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

                        <?= $form->field($model, 'supplier_delivery_id')->dropDownList(
                                ArrayHelper::map(
                                    SupplierDelivery::find()->joinWith(['kdSupplier', 'supplierDeliveryInvoices'])
                                        ->andWhere([$model->isNewRecord ? 'IS' : 'IS NOT', 'supplier_delivery_invoice.supplier_delivery_id', null])
                                        ->orderBy('supplier.nama, supplier_delivery.id')->asArray()->all(), 
                                    'id', 
                                    function($data) { 
                                        return $data['kdSupplier']['nama'] . ' (' . $data['id'] . ')';                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 100%'
                                ]) ?>

                        <?= $form->field($model, 'payment_method')->dropDownList(
                                ArrayHelper::map(
                                    PaymentMethod::find()->andWhere(['type' => 'Purchase'])->orderBy('nama_payment')->asArray()->all(), 
                                    'id', 
                                    function($data) { 
                                        return $data['nama_payment'];                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 100%'
                                ]) ?>

                        <?= $form->field($model, 'jumlah_harga', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->widget(MaskMoney::className()) ?>

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

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->
    
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Penerimaan Item PO</h3>
                </div>
                <div class="box-body">
                    
                    <div class="table-responsive">
                        <table id="table-supplier-delivery" class="table table-striped">
                            <thead>
                                <tr>

                                    <th>Nama Item</th>
                                    <th>Satuan (SKU)</th>
                                    <th>Jumlah Terima</th>
                                    <th>Harga Satuan</th>
                                </tr>                            
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Item Yang Diretur</h3>
                </div>
                <div class="box-body">
                    
                    <div class="table-responsive">
                        <table id="table-retur-purchase" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Satuan (SKU)</th>
                                    <th>Jumlah Terima</th>
                                    <th>Harga Satuan</th>
                                </tr>                            
                            </thead>
                            <tbody>                                                                
                                
                            </tbody>                            
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box">
                <div class="box-body">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->
    
    <?= Html::hiddenInput('index', 0, ['id' => 'index']) ?>
    
<?php ActiveForm::end(); ?>

<?php
$jscript = '
    $("#supplierdeliveryinvoice-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    
    $("#supplierdeliveryinvoice-supplier_delivery_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    $("#supplierdeliveryinvoice-payment_method").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    $("#supplierdeliveryinvoice-supplier_delivery_id").on("select2:select", function(e) {
    
        var thisObj = $(this);
        var index = 0;
        var jumlahHarga = 0;
        
        var changeIndex = function(tbody) {
            
            $(tbody).find("tr").each(function() {
                
                $(this).find("input").each(function() {

                    $(this).attr("name", $(this).attr("name").replace("index", index));                                                
                });                                        

                index++;
                
                jumlahHarga += parseFloat($(this).find("#jumlah-item").val()) * parseFloat($(this).find("#harga-satuan").val());
            });
        };
        
        $.ajax({
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'supplier-delivery/get-sd-by-id']) . '?id=" + thisObj.select2("data")[0].id,
            success: function(response) {
                
                $("table#table-supplier-delivery tbody").html(response);
                
                changeIndex("table#table-supplier-delivery tbody");
                
                $.ajax({
                    cache: false,
                    url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'retur-purchase/get-rp-by-id']) . '?id=" + thisObj.select2("data")[0].id,
                    success: function(response) {

                        $("table#table-retur-purchase tbody").html(response);
                        
                        changeIndex("table#table-retur-purchase tbody");
                        
                        $("#supplierdeliveryinvoice-jumlah_harga-disp").maskMoney("mask", jumlahHarga);
                        $("#supplierdeliveryinvoice-jumlah_harga-disp").trigger("change");
                    }
                });
            }
        });
    });
';

if (!$model->isNewRecord) {
    
    $jscript .= '
        $("#supplierdeliveryinvoice-supplier_delivery_id").prop("disabled", true);
        
        $.ajax({
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'supplier-delivery/get-sd-by-id']) . '?id=" + $("#supplierdeliveryinvoice-supplier_delivery_id").select2("data")[0].id,
            success: function(response) {
                
                $("table#table-supplier-delivery tbody").html(response);
                
                $.ajax({
                    cache: false,
                    url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'retur-purchase/get-rp-by-id']) . '?id=" + $("#supplierdeliveryinvoice-supplier_delivery_id").select2("data")[0].id,
                    success: function(response) {

                        $("table#table-retur-purchase tbody").html(response);
                    }
                });
            }
        });
    ';       
}

$this->registerJs($jscript); ?>