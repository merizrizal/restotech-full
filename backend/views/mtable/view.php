<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Mtable */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Meja Ruangan ' . $model->mtableCategory->nama_category, 'url' => ['index', 'cid' => $model->mtable_category_id]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="mtable-view">
    
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
                                'model-name' => $model->nama_meja,
                            ]) ?>                            
                        
                        <?= Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;' . 'Cancel', 
                            ['index', 'cid' => $model->mtable_category_id], 
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
                        'mtableCategory.nama_category',
                        'nama_meja',
                        'kapasitas',
                        [
                            'attribute' => 'not_active',
                            'format' => 'raw',
                            'value' => Html::checkbox('not_active[]', $model->not_active, ['value' => $model->id, 'disabled' => 'disabled']),
                        ],
                        'keterangan:ntext',
                        [
                            'attribute' => 'not_ppn',
                            'format' => 'raw',
                            'value' => Html::checkbox('not_ppn[]', $model->not_ppn, ['value' => $model->id, 'disabled' => 'disabled']),
                        ],
                        [
                            'attribute' => 'not_service_charge',
                            'format' => 'raw',
                            'value' => Html::checkbox('not_service_charge[]', $model->not_service_charge, ['value' => $model->id, 'disabled' => 'disabled']),
                        ],
                        [
                            'attribute' => 'image',
                            'format' => 'raw',
                            'value' => Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/mtable/', 'image', 200, 200), ['class'=>'img-thumbnail file-preview-image']),
                        ],
                        'shape',
                    ],
                ]) ?>
                        
                </div>
            </div>
        </div>
    </div>

</div>

<?php
    
$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript();

echo $modalDialog->renderDialog();

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
    
$jscript = Yii::$app->params['checkbox-radio-script']()
        . '$(".iCheck-helper").parent().removeClass("disabled");';

$this->registerJs($jscript); ?>