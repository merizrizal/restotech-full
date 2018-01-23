<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\DynamicTable;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\PurchaseOrder */

$dynamicTablePOTrx = new DynamicTable([
    'model' => $modelPOTrx,
    'tableFields' => [
        'item_id',
        'item.nama_item',
        'itemSku.nama_sku',
        'jumlah_order',
        'harga_satuan:currency',
        'jumlah_harga:currency',
    ],
    'dataProvider' => $dataProviderPOTrx,
    'title' => 'Item',
    'columnClass' => 'col-sm-10 col-sm-offset-1'
]);

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="purchase-order-view">
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i>&nbsp;&nbsp;&nbsp;' . 'Edit', 
                            ['update', 'id' => $model->id], 
                            [
                                'class' => 'btn btn-primary',
                                'style' => 'color:white'
                            ]) ?>
                            
                        <?= Html::a('<i class="glyphicon glyphicon-trash"></i>&nbsp;&nbsp;&nbsp;' . 'Delete', 
                            ['delete', 'id' => $model->id], 
                            [
                                'id' => 'delete',
                                'class' => 'btn btn-danger',
                                'style' => 'color:white',
                                'model-id' => $model->id,
                                'model-name' => '',
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
                        'kd_supplier',
                        'kdSupplier.nama',
                        'jumlah_item',
                        'jumlah_harga:currency',
                    ],
                ]) ?>
                        
            </div>
        </div>
    </div>
    
    <?= $dynamicTablePOTrx->tableData() ?>

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