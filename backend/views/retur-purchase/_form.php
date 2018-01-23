<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\models\Supplier;
use restotech\standard\backend\models\Storage;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\ReturPurchase */
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
    'id' => 'formReturPurchase',
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
                    <div class="retur-purchase-form">

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

                        <?= $form->field($model, 'kd_supplier')->dropDownList(
                                ArrayHelper::map(
                                    Supplier::find()->andWhere(['is_deleted' => 0])->orderBy('nama')->asArray()->all(), 
                                    'kd_supplier', 
                                    function($data) { 
                                        return $data['nama'] . ' (' . $data['kd_supplier'] . ')';                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 100%'
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
        <div class="col-sm-12">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Penerimaan Item PO</h3>
                </div>
                <div class="box-body">
                    
                    <div class="table-responsive">
                        <table id="table-supplier-delivery" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No. Terima</th>
                                    <th>Nama Item</th>
                                    <th>Satuan (SKU)</th>
                                    <th>Jumlah Terima</th>
                                    <th>Harga Satuan</th>
                                    <th><i class="fa fa-plus"></i></th>
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
        <div class="col-sm-12">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Item Yang Diretur</h3>
                </div>
                <div class="box-body">
                    
                    <div class="table-responsive">
                        <table id="table-retur-purchase" class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 13%">No. Terima</th>
                                    <th style="width: 20%">Nama Item</th>
                                    <th style="width: 11%">Satuan (SKU)</th>
                                    <th style="width: 7%">Jumlah</th>
                                    <th style="width: 13%">Harga Satuan</th>
                                    <th style="width: 18%">Storage</th>
                                    <th>Rak</th>
                                    <th></th>
                                </tr>                            
                            </thead>
                            <tbody>
                                
                                <?php
                                if (!$model->isNewRecord):
                                    
                                    if (!empty($model->returPurchaseTrxes)):
                                        
                                        foreach ($model->returPurchaseTrxes as $returPurchaseTrx): ?>
                                
                                            <tr>
                                                <td><?= $returPurchaseTrx->supplier_delivery_id ?></td>
                                                <td><?= $returPurchaseTrx->item->nama_item ?></td>
                                                <td><?= $returPurchaseTrx->itemSku->nama_sku ?></td>
                                                <td><?= $returPurchaseTrx->jumlah_item ?></td>
                                                <td><?= Yii::$app->formatter->asCurrency($returPurchaseTrx->harga_satuan) ?></td>
                                                <td><?= $returPurchaseTrx->storage->nama_storage ?></td>
                                                <td><?= !empty($returPurchaseTrx->storageRack) ? $returPurchaseTrx->storageRack->nama_rak : '' ?></td>
                                                <td></td>
                                            </tr>
                                
                                        <?php
                                        endforeach;
                                    endif;                                
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

<div id="temp-RPTrx" class="hide">
    <table>
        <tbody>
            <tr>
                <td>
                    <?= $form->field($modelReturPurchaseTrx, '[index]supplier_delivery_id', [
                        'template' => '{input}'
                    ])->textInput(['readonly' => 'readonly']) ?>
                </td>
                <td>
                    <?= $form->field($modelItem, '[index]nama_item', [
                        'template' => '{input}'
                    ])->textInput(['readonly' => 'readonly']) ?>
                </td>
                <td>
                    <?= $form->field($modelItemSku, '[index]nama_sku', [
                        'template' => '{input}'
                    ])->textInput(['readonly' => 'readonly']) ?>
                </td>
                <td>
                    <?= $form->field($modelReturPurchaseTrx, '[index]jumlah_item', [
                        'template' => '{input}{error}'
                    ])->textInput() ?>
                </td>
                <td>
                    <?= $form->field($modelReturPurchaseTrx, '[index]harga_satuan', [
                        'template' => '{input}'
                    ])->widget(MaskMoney::className()) ?>
                </td>
                <td>
                    <?= $form->field($modelReturPurchaseTrx, '[index]storage_id', [
                        'template' => '{input}{error}'
                    ])->dropDownList(
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
                </td>
                <td>
                    <?= $form->field($modelReturPurchaseTrx, '[index]storage_rack_id', [
                        'template' => '{input}'
                    ])->textInput(['style' => 'width: 100%']) ?>
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
                    
                    <?= $form->field($modelReturPurchaseTrx, '[index]supplier_delivery_trx_id', [
                        'template' => '{input}'
                    ])->hiddenInput() ?>
                    
                    <?= $form->field($modelReturPurchaseTrx, '[index]item_id', [
                        'template' => '{input}'
                    ])->hiddenInput() ?>
                    
                    <?= $form->field($modelReturPurchaseTrx, '[index]item_sku_id', [
                        'template' => '{input}'
                    ])->hiddenInput() ?>
                    
                </td>
            </tr>
        </tbody>
    </table>
</div>
    
<?php  

$jscript = '
    var disableKdSupplier = function() {
    
        if ($("#table-retur-purchase").children("tbody").find("tr").length > 0) {
        
            $("#returpurchase-kd_supplier").on("select2:opening",function(e) {
                return false;
            });
            
            $("#returpurchase-kd_supplier").on("select2:unselecting",function(e) {
                return false;
            });
        } else {            
            
            $("#returpurchase-kd_supplier").off("select2:opening");
            $("#returpurchase-kd_supplier").off("select2:unselecting");
        }
    };
    
    $("#returpurchase-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    
    $("#returpurchase-kd_supplier").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    $("#returpurchase-kd_supplier").on("select2:select", function(e) {
        
        $.ajax({
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery/get-sd']) . '?id=" + $(this).select2("data")[0].id,
            success: function(response) {
                
                $("table#table-supplier-delivery tbody").html(response);
            }
        });
    });
            
    $("#returpurchase-jumlah_harga-disp").off("keypress");
    $("#returpurchase-jumlah_harga-disp").off("keyup");
';   

if (!$model->isNewRecord) {
    
    $jscript .= '
        
        $.ajax({
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery/get-sd']) . '?id=" + $("#returpurchase-kd_supplier").select2("data")[0].id,
            success: function(response) {
                
                $("table#table-supplier-delivery tbody").html(response);
            }
        });
        
        disableKdSupplier();
    ';
}

$this->registerJs($jscript); ?>