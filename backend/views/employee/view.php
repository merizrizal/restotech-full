<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Employee */

$this->title = '(' . $model->kd_karyawan . ') ' . $model->nama;
$this->params['breadcrumbs'][] = ['label' => 'Karyawan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="employee-view">
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        <?= Html::a('<i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;' . 'Edit', 
                            ['update', 'id' => $model->kd_karyawan], 
                            [
                                'class' => 'btn btn-success', 
                                'style' => 'color:white'
                            ]) ?>
                        
                        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;' . 'Delete', 
                            ['delete', 'id' => $model->kd_karyawan], 
                            [
                                'id' => 'delete',
                                'class' => 'btn btn-danger',
                                'style' => 'color:white',
                                'model-id' => $model->kd_karyawan,
                                'model-name' => $model->nama,
                            ]) ?>
                        
                        <?= Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;' . 'Cancel', 
                            ['index'], 
                            [
                                'class' => 'btn btn-default', 
                            ]) ?>
                    </h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table'
                        ],
                        'attributes' => [
                            'kd_karyawan',
                            'password_absen',
                            'nama',
                            'alamat:ntext',
                            'jenis_kelamin',
                            'phone1',
                            'phone2',
                            'limit_officer:currency',
                            'sisa:currency',
                            [
                                'attribute' => 'not_active',
                                'format' => 'raw',
                                'value' => Html::checkbox('not_active[]', $model->not_active, ['value' => $model->not_active, 'disabled' => 'disabled']),
                            ],
                            [
                                'attribute' => 'image',
                                'format' => 'raw',
                                'value' => Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/employee/', 'image', 200, 200), ['class'=>'img-thumbnail file-preview-image']),
                            ],
                            
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

$this->registerJs($jscript);
    
 ?>