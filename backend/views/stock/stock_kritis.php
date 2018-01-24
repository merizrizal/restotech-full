<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use restotech\standard\backend\models\Supplier;



$this->title = 'Stock Kritis';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="row">
    <!-- Left col -->
    <section class="col-lg-12">                            
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Stock Barang Yang Kritis</h3>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 20px">#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th style="width: 100px">Satuan Unit</th>
                            <th style="width: 100px">Limit Stok</th>
                            <th style="width: 100px">Stok</th>
                            <th>Gudang</th>
                            <th>Rak</th>
                            <th style="width: 100px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                        $i = 1;
                        foreach ($modelItemSku as $dataItemSku): 
                            if (!empty($dataItemSku['stocks']) && count($dataItemSku['stocks']) > 0):
                                foreach ($dataItemSku['stocks'] as $dataStock): 
                                    $persenLimit = 100;
                            
                                    if ($dataStock['jumlah_stok'] > 0)
                                        $persenLimit = round(($dataItemSku['stok_minimal'] / $dataStock['jumlah_stok']) * 100);
                                    
                                    $persenLimit = ($persenLimit > 100) ? 100 : $persenLimit; ?>

                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $dataItemSku['item']['id'] ?></td>
                                        <td><?= $dataItemSku['item']['nama_item'] ?></td>
                                        <td><?= $dataItemSku['nama_sku'] ?></td>
                                        <td><?= $dataItemSku['stok_minimal'] ?></td>
                                        <td><?= $dataStock['jumlah_stok'] ?></td>
                                        <td><?= $dataStock['storage']['nama_storage'] ?></td>
                                        <td><?= $dataStock['storageRack']['nama_rak'] ?></td>                                        
                                        <td>                        
                                            <?= Html::hiddenInput('itemSku', $dataItemSku['id'], ['id' => 'itemSku']) ?>
                                            <?= Html::hiddenInput('itemSkuHarga', $dataItemSku['harga_beli'], ['id' => 'itemSkuHarga']) ?>
                                            <?= Html::hiddenInput('item', $dataItemSku['item']['id'], ['id' => 'item']) ?>
                                            <button id="btnOrder" class="btn btn-danger btn-sm"><i class="fa fa-truck"></i> Order</button>
                                        </td>
                                    </tr>                 

                                    <?php
                                    $i++;
                                endforeach;
                            else: ?>
                                
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $dataItemSku['item']['id'] ?></td>
                                    <td><?= $dataItemSku['item']['nama_item'] ?></td>
                                    <td><?= $dataItemSku['nama_sku'] ?></td>
                                    <td><?= $dataItemSku['stok_minimal'] ?></td>
                                    <td>0</td>
                                    <td><span class="not-set">not set</span></td>
                                    <td><span class="not-set">not set</span></td>
                                    <td>            
                                        <?= Html::hiddenInput('itemSku', $dataItemSku['id'], ['id' => 'itemSku']) ?>
                                        <?= Html::hiddenInput('itemSkuHarga', $dataItemSku['harga_beli'], ['id' => 'itemSkuHarga']) ?>
                                        <?= Html::hiddenInput('item', $dataItemSku['item']['id'], ['id' => 'item']) ?>
                                        <button id="btnOrder" class="btn btn-danger btn-sm"><i class="fa fa-truck"></i> Order</button>
                                    </td>
                                </tr>  
                                
                                <?php
                                $i++;
                            endif;
                        endforeach; ?>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan Unit</th>
                            <th>Limit Stok</th>
                            <th>Stok</th>
                            <th>Gudang</th>
                            <th>Rak</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </section><!-- /.Left col -->
</div><!-- /.row (main row) -->

<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-maroon">
            <div class="inner">
                <h3>
                    Report
                </h3>
                <p>
                    Stok
                </p>
            </div>
            <div class="icon">
                <i class="ion ion-pricetags"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl('stock/report-stock'); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3>
                    Report
                </h3>
                <p>
                    Stok Masuk
                </p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-undo-outline"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl('stock/report-stock-inflow'); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col --> 
    <div class="col-lg-3 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>
                    Report
                </h3>
                <p>
                    Stok Keluar
                </p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-redo-outline"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl('stock/report-stock-outflow'); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->    
    <div class="col-lg-3 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3>
                    Report
                </h3>
                <p>
                    Stok Transfer
                </p>
            </div>
            <div class="icon">
                <i class="ion ion-shuffle"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl('stock/report-stock-transfer'); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div><!-- ./col -->
</div><!-- /.row -->


<div class="modal fade" id="modalOrder" tabindex="-1" role="dialog">
    <div class="modal-dialog">    
        <?php $form = ActiveForm::begin([
                    'id' => 'form-item',
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
        
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        Purchase Order
                    </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-primary btn-sm" data-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>

                    </div>
                </div>
                <div class="box-body">                

                    <?= $form->field($modelPurchaseOrder, 'kd_supplier')->dropDownList(
                            ArrayHelper::map(
                                Supplier::find()->all(), 
                                'kd_supplier', 
                                function($data) { 
                                    return '(' . $data->kd_supplier . ') ' . $data->nama;                                 
                                }
                            ), 
                            [
                                'prompt' => '',
                                'style' => 'width: 90%'
                            ]) ?>    
                    
                    <?= $form->field($modelPurchaseOrderTrx, 'item_id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput(['maxlength' => 16, 'readonly' => 'readonly']) ?>

                    <?= $form->field($modelPurchaseOrderTrx, 'item_sku_id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput(['maxlength' => 16, 'readonly' => 'readonly']) ?>
                    
                    <?= $form->field($modelPurchaseOrderTrx, 'harga_satuan', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->widget(MaskMoney::className()) ?>                   

                    <?= $form->field($modelPurchaseOrderTrx, 'jumlah_order', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput() ?> 

                </div>
                <div class="box-footer" style="text-align: right">
                    <?= Html::submitButton('<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;Submit', ['id' => 'aYes', 'class' => 'btn btn-primary']); ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;
                        Cancel
                    </button>
                </div> 
            </div>
        <?php ActiveForm::end(); ?>
    </div>    
</div>

<?php

$this->params['regCssFile'][] = function() {
    $this->registerCssFile(Yii::getAlias('@common-web') . '/css/select2/select2.css');
    $this->registerCssFile(Yii::getAlias('@common-web') . '/css/select2/select2-bootstrap.css');
}; 

$this->params['regJsFile'][] = function() {
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/select2/select2.min.js');        
};

$jscript = '
    $("button#btnOrder").on("click", function(event) {
        $("#modalOrder #purchaseorder-kd_supplier").select2("val", "");
        $("#modalOrder #purchaseorder-jumlah_item").val("");
        $("#modalOrder #purchaseordertrx-item_id").val("");
        $("#modalOrder #purchaseordertrx-item_sku_id").val("");
        $("#modalOrder #purchaseordertrx-harga_satuan").val("");
        $("#modalOrder #purchaseordertrx-harga_satuan-disp").maskMoney("mask", 0);
        $("#modalOrder #purchaseordertrx-jumlah_order").val("");
        
        if ($("#modalOrder .form-group").hasClass("has-error")) {
            $("#modalOrder .form-group").removeClass("has-error");
            $("#modalOrder .help-block").empty();
        }
        if ($("#modalOrder .form-group").hasClass("has-success")) $("#modalOrder .form-group").removeClass("has-success");
        
        $("#modalOrder #purchaseordertrx-item_id").val($(this).parent().find("input#item").val());
        $("#modalOrder #purchaseordertrx-item_sku_id").val($(this).parent().find("input#itemSku").val());
        $("#modalOrder #purchaseordertrx-harga_satuan").val(parseFloat($(this).parent().find("input#itemSkuHarga").val()));
        $("#modalOrder #purchaseordertrx-harga_satuan-disp").maskMoney("mask", parseFloat($(this).parent().find("input#itemSkuHarga").val()));

        $("#modalOrder").modal();
    });

    $("#purchaseorder-kd_supplier").select2({
        placeholder: "Select Supplier",
        allowClear: true
    });
';

$this->registerJs($jscript); ?>
