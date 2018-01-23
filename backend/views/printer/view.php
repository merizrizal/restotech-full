<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Printer */

$this->title = 'Printer: ' . $model->printer;
$this->params['breadcrumbs'][] = ['label' => 'Printer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="printer-view">
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        <?= Html::a('<i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;' . 'Edit', 
                            ['update', 'id' => $model->printer], 
                            [
                                'class' => 'btn btn-primary',
                                'style' => 'color:white'
                            ]) ?>
                            
                        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;' . 'Delete', 
                            ['delete', 'id' => $model->printer], 
                            [
                                'id' => 'delete',
                                'class' => 'btn btn-danger',
                                'style' => 'color:white',
                                'model-id' => $model->printer,
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
                        'printer',
                        'type',
                        [
                            'attribute' => 'is_autocut',
                            'format' => 'raw',
                            'value' => Html::checkbox('is_autocut[]', $model->is_autocut, ['value' => $model->is_autocut, 'disabled' => 'disabled']),
                        ],
                        [
                            'attribute' => 'not_active',
                            'format' => 'raw',
                            'value' => Html::checkbox('not_active[]', $model->not_active, ['value' => $model->not_active, 'disabled' => 'disabled']),
                        ],
                    ],
                ]) ?>
                        
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