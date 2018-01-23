<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\DynamicTable;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDeliveryInvoice */

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

$dynamicTableSDTrx = new DynamicTable([
    'model' => $modelSDTrx,
    'tableFields' => [
        'item.nama_item',
        'itemSku.nama_sku',
        'jumlah_terima',
        'harga_satuan:currency',
    ],
    'dataProvider' => $dataProviderSDTrx,
    'title' => 'Penerimaan Item PO',
    'columnClass' => 'col-sm-8 col-sm-offset-2'
]);

$dynamicTableRPTrx = new DynamicTable([
    'model' => $modelRPTrx,
    'tableFields' => [
        'item.nama_item',
        'itemSku.nama_sku',
        'jumlah_item',
        'harga_satuan:currency',
    ],
    'dataProvider' => $dataProviderRPTrx,
    'title' => 'Item Yang Diretur',
    'columnClass' => 'col-sm-8 col-sm-offset-2'
]);

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invoice Penerimaan PO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="supplier-delivery-invoice-view">
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        <?= Html::a('<i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;' . 'Edit', 
                            ['update', 'id' => $model->id], 
                            [
                                'class' => 'btn btn-primary',
                                'style' => 'color:white'
                            ]) ?>
                            
                        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;' . 'Delete', 
                            ['delete', 'id' => $model->id], 
                            [
                                'id' => 'delete',
                                'class' => 'btn btn-danger',
                                'style' => 'color:white',
                                'model-id' => $model->id,
                                'model-name' => '',
                            ]) ?>                            
                        
                        <?= Html::a('<i class="fa fa-dollar"></i>&nbsp;&nbsp;&nbsp;' . 'Pembayaran', 
                            ['supplier-delivery-invoice-payment/create', 'id' => $model->id], 
                            [
                                'class' => 'btn btn-default',
                            ]) ?>
                        
                        <?= Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;' . 'Cancel', 
                            ['index'], 
                            [
                                'class' => 'btn btn-default',
                            ]) ?>
                                                
                    </h3>
                </div>
                
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => [
                        'class' => 'table'
                    ],
                    'attributes' => [
                        'id',
                        'date:date',
                        'supplier_delivery_id',
                        'supplierDelivery.kdSupplier.nama',
                        'paymentMethod.nama_payment',
                        'jumlah_harga:currency',
                        'jumlah_bayar:currency',
                        [
                            'label' => 'Jumlah Sisa',
                            'format' => 'raw',
                            'value' => Yii::$app->formatter->asCurrency(-1 * ($model->jumlah_bayar - $model->jumlah_harga)),
                        ],
                    ],
                ]) ?>
                        
            </div>
        </div>
    </div>
    
    <?= $dynamicTableSDTrx->tableData() ?>
    
    <?= $dynamicTableRPTrx->tableData() ?>

</div>

<?php
    
$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript();

echo $modalDialog->renderDialog();
    
?>