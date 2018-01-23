<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Item */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="item-view">
    
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
                                'model-name' => $model->nama_item,
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
                        'parentItemCategory.nama_category',
                        'itemCategory.nama_category',
                        'nama_item',
                        'keterangan:ntext',
                        [
                            'attribute' => 'not_active',
                            'format' => 'raw',
                            'value' => Html::checkbox('not_active[]', $model->not_active, ['value' => $model->id, 'disabled' => 'disabled']),
                        ],
                    ],
                ]) ?>
                                        
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="item-form">
                        <div class="row">      
                            
                            <?php
                            for ($i = 1; $i <= count($modelSkus); $i++): ?>

                                <div class="col-lg-3">       
                                    
                                    <?= DetailView::widget([
                                        'model' => $modelSkus[$i],
                                        'options' => [
                                            'class' => 'table'
                                        ],
                                        'attributes' => [
                                            'id',                                            
                                            'nama_sku',                                            
                                            'stok_minimal',
                                            'per_stok',
                                            'storage.nama_storage',
                                            'storageRack.nama_rak',
                                        ],
                                    ]) ?>

                                </div>

                            <?php
                            endfor; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.row -->

</div>

<?php
    
$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript();

echo $modalDialog->renderDialog(); ?>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = Yii::$app->params['checkbox-radio-script']()
        . '$(".iCheck-helper").parent().removeClass("disabled");';

$this->registerJs($jscript); ?>