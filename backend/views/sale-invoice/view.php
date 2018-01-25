<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SaleInvoice */

kartik\money\MaskMoneyAsset::register($this);

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

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Faktur Pembayaran', 'url' => ['refund']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="sale-invoice-view">
    
    <?php $form = ActiveForm::begin([    
        'id' => 'formSaleInvoiceRetur',
        'options' => [

        ],
        'fieldConfig' => [
            'template' => '{input}{error}', 
        ]
    ]); ?>
    
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <div class="box box-danger">

                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table'
                        ],
                        'attributes' => [
                            'id',
                            'date:date',
                            'mtableSession.mtable.nama_meja',
                            'userOperator.kdKaryawan.nama',
                            'jumlah_harga:currency',
                            'jumlah_bayar:currency',
                            'jumlah_kembali:currency',
                        ],
                    ]) ?>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['refund'], ['class' => 'btn btn-default']); ?>
                                
                            </div>
                        </div>
                    </div>
                    
                    <br>
                    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title">Menu Order</h3>
                    </div>
                    <div class="box-body">

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Menu</th>
                                        <th style="width: 15%">Jumlah</th>
                                        <th style="width: 15%">Harga</th>
                                        <th style="width: 15%">Discount</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th></th>
                                    </tr>                            
                                </thead>
                                <tbody>

                                    <?php                                    
                                    if (!empty($model->saleInvoiceTrxes)):

                                        foreach ($model->saleInvoiceTrxes as $saleInvoiceTrx): 

                                            $discount = '';
                                            $hargaDiscount = 0;
                                            $subtotal = $saleInvoiceTrx->jumlah * $saleInvoiceTrx->harga_satuan;

                                            if ($saleInvoiceTrx->discount_type == 'Percent') {

                                                $discount = $saleInvoiceTrx->discount . ' %';

                                                $hargaDiscount = round($saleInvoiceTrx->discount * 0.01 * $subtotal);
                                            } else if ($saleInvoiceTrx->discount_type == 'Value') {
                                                $discount = Yii::$app->formatter->asCurrency($saleInvoiceTrx->discount);

                                                $hargaDiscount = $saleInvoiceTrx->jumlah * $saleInvoiceTrx->discount;
                                            } 

                                            $subtotal =  $subtotal - $hargaDiscount; ?>

                                            <tr>
                                                <td><?= $saleInvoiceTrx->menu->nama_menu ?></td>
                                                <td><?= $saleInvoiceTrx->jumlah ?></td>
                                                <td><?= Yii::$app->formatter->asCurrency($saleInvoiceTrx->harga_satuan) ?></td>
                                                <td><?= $discount ?></td>
                                                <td><?= Yii::$app->formatter->asCurrency($subtotal) ?></td>                                            
                                                <td>
                                                    <?= Html::a('<i class="fa fa-check"></i>', null, [
                                                        'class' => 'btn btn-primary btn-xs',
                                                        'id' => 'check-invoice-trx',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'left',
                                                        'title' => 'Pilih',
                                                    ]) ?>
                                                    
                                                    <?= Html::hiddenInput('sale_invoice_trx_id', $saleInvoiceTrx->id, ['id' => 'sale-invoice-trx-id']) ?>
                                                    <?= Html::hiddenInput('menu_id', $saleInvoiceTrx->menu_id, ['id' => 'menu-id']) ?>
                                                    <?= Html::hiddenInput('harga', $saleInvoiceTrx->harga_satuan, ['id' => 'harga']) ?>
                                                    <?= Html::hiddenInput('discount_type', $saleInvoiceTrx->discount_type, ['id' => 'discount-type']) ?>
                                                    <?= Html::hiddenInput('discount', $saleInvoiceTrx->discount, ['id' => 'discount']) ?>
                                                    
                                                    <?= Html::hiddenInput('nama_menu', $saleInvoiceTrx->menu->nama_menu, ['id' => 'nama-menu-temp']) ?>
                                                    <?= Html::hiddenInput('harga', Yii::$app->formatter->asCurrency($saleInvoiceTrx->harga_satuan), ['id' => 'harga-temp']) ?>
                                                    <?= Html::hiddenInput('discount', $discount, ['id' => 'discount-temp']) ?>
                                                    <?= Html::hiddenInput('subtotal', Yii::$app->formatter->asCurrency($subtotal), ['id' => 'subtotal-temp']) ?>
                                                </td>
                                            </tr>

                                        <?php
                                        endforeach;
                                    endif; ?>

                                </tbody>                            
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title">Menu Refund</h3>
                    </div>
                    <div class="box-body">

                        <div class="table-responsive">
                            <table id="sale-invoice-retur" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Menu</th>
                                        <th style="width: 12%">Jumlah</th>
                                        <th style="width: 12%">Harga</th>
                                        <th style="width: 12%">Discount</th>
                                        <th style="width: 12%">Subtotal</th>
                                        <th style="width: 18%">Keterangan</th>
                                        <th></th>
                                    </tr>                            
                                </thead>
                                <tbody>

                                    <?php                                    
                                    if (!empty($model->saleInvoiceTrxes)):
                                        
                                        foreach ($model->saleInvoiceTrxes as $saleInvoiceTrx):     
                                        
                                            if (!empty($saleInvoiceTrx->saleInvoiceReturs)):

                                                foreach ($saleInvoiceTrx->saleInvoiceReturs as $saleInvoiceRetur): 

                                                    $discount = '';
                                                    $hargaDiscount = 0;
                                                    $subtotal = $saleInvoiceRetur->jumlah * $saleInvoiceRetur->harga;

                                                    if ($saleInvoiceRetur->discount_type == 'Percent') {

                                                        $discount = $saleInvoiceRetur->discount . ' %';

                                                        $hargaDiscount = round($saleInvoiceRetur->discount * 0.01 * $subtotal);
                                                    } else if ($saleInvoiceRetur->discount_type == 'Value') {
                                                        $discount = Yii::$app->formatter->asCurrency($saleInvoiceRetur->discount);

                                                        $hargaDiscount = $saleInvoiceRetur->jumlah * $saleInvoiceRetur->discount;
                                                    } 

                                                    $subtotal =  $subtotal - $hargaDiscount; ?>

                                                    <tr>
                                                        <td><?= $saleInvoiceRetur->menu->nama_menu ?></td>
                                                        <td><?= $saleInvoiceRetur->jumlah ?></td>
                                                        <td><?= Yii::$app->formatter->asCurrency($saleInvoiceRetur->harga) ?></td>
                                                        <td><?= $discount ?></td>
                                                        <td><?= Yii::$app->formatter->asCurrency($subtotal) ?></td>                                            
                                                        <td><?= $saleInvoiceRetur->keterangan ?></td>
                                                        <th></th>
                                                    </tr>

                                                <?php
                                                endforeach;
                                            endif;
                                        endforeach;
                                    endif; ?>

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
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['refund'], ['class' => 'btn btn-default']); ?>
                                    
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    
    <?php
    ActiveForm::end() ?>

</div>

<?= Html::hiddenInput('index', 0, ['id' => 'index']) ?>

<div id="temp-sale-invoice-retur" class="hide">
    <table>
        <tbody>
            <tr>
                <td>
                    <?= $form->field($modelMenu, '[index]nama_menu')->textInput(['readonly' => 'readonly']) ?>
                </td>
                <td>
                    <?= $form->field($modelSaleInvoiceRetur, '[index]jumlah')->textInput(['class' => 'form-control jumlah']) ?>
                </td>
                <td>
                    <div class="form-group">
                        <?= Html::textInput('harga', null, ['class' => 'harga-temp form-control', 'readonly' => 'readonly']) ?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?= Html::textInput('discount', null, ['class' => 'discount-temp form-control', 'readonly' => 'readonly']) ?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?= Html::textInput('subtotal', null, ['class' => 'subtotal-temp form-control', 'readonly' => 'readonly']) ?>
                    </div>                    
                </td>
                <td>
                    <?= $form->field($modelSaleInvoiceRetur, '[index]keterangan')->textInput() ?>
                </td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <?= Html::a('<i class="fa fa-trash"></i>', null, [                    
                            'id' => 'aDelete',
                            'class' => 'btn btn-danger',                            
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'left',
                            'title' => 'Delete',
                        ]) ?>
                    </div>
                    
                    <?= $form->field($modelSaleInvoiceRetur, '[index]sale_invoice_trx_id')->hiddenInput() ?>
                    
                    <?= $form->field($modelSaleInvoiceRetur, '[index]menu_id')->hiddenInput() ?>
                    
                    <?= $form->field($modelSaleInvoiceRetur, '[index]harga')->hiddenInput(['class' => 'form-control harga']) ?>
                    
                    <?= $form->field($modelSaleInvoiceRetur, '[index]discount_type')->hiddenInput(['class' => 'form-control discount-type']) ?>
                    
                    <?= $form->field($modelSaleInvoiceRetur, '[index]discount')->hiddenInput(['class' => 'form-control discount']) ?>
                    
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php
$jscript = '
    var changeIndex = function(content, field, index, validation) {
        
        var inputClass = "";
        var inputName = "";
        var inputId = "";

        inputClass = content.find("#" + field).parent().attr("class");
        inputClass = inputClass.replace("index", index);
        
        content.find("#" + field).parent().attr("class", inputClass);
            
        inputName = content.find("#" + field).attr("name");
        inputName = inputName.replace("index", index);
        
        content.find("#" + field).attr("name", inputName);
            
        inputId = content.find("#" + field).attr("id");
        inputId = inputId.replace("index", index);
        
        content.find("#" + field).attr("id", inputId);
        
        $("#formSaleInvoiceRetur").yiiActiveForm("add", {
            id: inputId,
            name: inputName,
            container: ".field-" + inputId,
            input: "#" + inputId,
            validate: function(attribute, value, messages, deferred, $form) {            
            
                $.each(validation, function(index, val) {                
                
                    if (val == "required") {

                        yii.validation.required(value, messages, {"message": "Tidak boleh kosong"});        
                    }

                    if (val == "number") {

                        yii.validation.number(value, messages, {"pattern":/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/, "message": "Harus berupa angka","skipOnEmpty":1});                                             
                    }
                });
            },
        });
        
        return content;
    };
    
    $("a#check-invoice-trx").on("click", function() {
    
        var thisObj = $(this).parent();
        
        var content = $("#temp-sale-invoice-retur").children().children().children().clone();
        
        var index = parseFloat($("#index").val());
        
        content.find("#menu-index-nama_menu").val(thisObj.find("#nama-menu-temp").val());
        content.find(".harga-temp").val(thisObj.find("#harga-temp").val());
        content.find(".discount-temp").val(thisObj.find("#discount-temp").val());
        content.find(".subtotal-temp").val(0);
        
        content.find("#saleinvoiceretur-index-sale_invoice_trx_id").val(thisObj.find("#sale-invoice-trx-id").val());
        content.find("#saleinvoiceretur-index-menu_id").val(thisObj.find("#menu-id").val());
        content.find("#saleinvoiceretur-index-harga").val(thisObj.find("#harga").val());
        content.find("#saleinvoiceretur-index-discount_type").val(thisObj.find("#discount-type").val());
        content.find("#saleinvoiceretur-index-discount").val(thisObj.find("#discount").val());
        
        content = changeIndex(content, "saleinvoiceretur-index-jumlah", index, ["number", "required"]);
        
        content = changeIndex(content, "saleinvoiceretur-index-keterangan", index);
        
        content = changeIndex(content, "saleinvoiceretur-index-sale_invoice_trx_id", index);
        
        content = changeIndex(content, "saleinvoiceretur-index-menu_id", index);
        
        content = changeIndex(content, "saleinvoiceretur-index-harga", index);
        
        content = changeIndex(content, "saleinvoiceretur-index-discount_type", index);
        
        content = changeIndex(content, "saleinvoiceretur-index-discount", index);
        
        content.find(".jumlah").on("change", function() {
            
            var subtotal = parseFloat($(this).val()) * parseFloat(content.find(".harga").val());
            var hargaDiscount = 0;
            
            if (content.find(".discount-type").val() == "Percent") {
                hargaDiscount = Math.round(parseFloat(content.find(".discount").val()) * 0.01 * subtotal);
            } else if (content.find(".discount-type").val() == "Value") {
                hargaDiscount = parseFloat($(this).val()) * parseFloat(content.find(".discount").val());
            }
            
            subtotal = subtotal - hargaDiscount;

            content.find(".subtotal-temp").maskMoney({"prefix":"Rp ","suffix":"","affixesStay":true,"thousands":".","decimal":",","precision":0,"allowZero":false,"allowNegative":false})
            content.find(".subtotal-temp").maskMoney("mask", subtotal);
        });
        
        content.find("a#aDelete").on("click", function() {
        
            $(this).parent().parent().parent().fadeOut(180, function() {                                
                
                $(this).remove();                
            });    
            
            return false;
        });
        
        $("#sale-invoice-retur").children("tbody").append(content);        
        
        $("#index").val(index + 1);        
        
        return false;
    });
';

$this->registerJs($jscript); ?>