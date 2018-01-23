<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Shift */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shift', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="shift-view">
    
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
                                'model-name' => $model->start_time . ' - ' . $model->end_time,
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
                        'start_time:time',
                        'end_time:time',
                        'keterangan:ntext',
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
    
?>