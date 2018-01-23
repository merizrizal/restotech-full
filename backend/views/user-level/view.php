<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\UserLevel */

$this->title = $model->nama_level;
$this->params['breadcrumbs'][] = ['label' => 'User Level', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="user-level-view">
    
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
                                'model-name' => $model->nama_level,
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
                        'nama_level',
                        [
                            'attribute' => 'is_super_admin',
                            'format' => 'raw',
                            'value' => Html::checkbox('is_super_admin[]', $model->is_super_admin, ['value' => $model->is_super_admin, 'disabled' => 'disabled']),
                        ],
                        [
                            'attribute' => 'default_action',
                            'format' => 'raw',
                            'value' => !empty($model->defaultAction) ? ($model->defaultAction->sub_program . '/' . $model->defaultAction->nama_module . '/' . $model->defaultAction->module_action) : null,
                        ],
                        'keterangan:ntext',
                    ],
                ]) ?>
                        
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        Roles
                    </h3>
                    <div class="box-tools">

                    </div>
                </div>
                <div class="box-body">
                    <div class="row" id="roles">
                        <?php                    
                        foreach ($modelUserAppModule as $keySubprogram => $subprogram):    
                            foreach ($subprogram as $key => $value): ?>

                            <div class="col-lg-4" id="roles-item">
                                <div class="box box-solid bg-light-blue">
                                    <div class="box-header">
                                        <h3 class="box-title"><?= $value[0]['sub_program'] . '/' . $key ?></h3>
                                    </div>
                                    <div class="box-body">
                                        <?php
                                        foreach ($value as $moduleAction) { 
                                            $checkBoxId = $moduleAction['nama_module'] . '-' . $moduleAction['module_action'];
                                            $checkBoxName = 'roles[' . $moduleAction['nama_module'] . $moduleAction['module_action'] . '][action]';
                                            $isActive = false;
                                            $userAksesId = 0;

                                            if (count($moduleAction['userAkses']) > 0) {
                                                $userAksesId = $moduleAction['userAkses'][0]['id'];
                                                $isActive = $moduleAction['userAkses'][0]['is_active'];
                                            }

                                            echo Html::checkbox($checkBoxName, $isActive, ['id' => $checkBoxId, 'value' => $moduleAction['id'], 'disabled' => 'disabled']) . '&nbsp; &nbsp; ';
                                            echo Html::label($moduleAction['module_action'], $checkBoxId);
                                            echo '<br>';
                                        } ?>
                                    </div><!-- /.box-body -->
                                </div>
                            </div>                                        

                            <?php
                            endforeach;
                        endforeach; ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-sm-1"></div>
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
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/masonry/masonry.pkgd.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = Yii::$app->params['checkbox-radio-script']()
. '
    $(".iCheck-helper").parent().removeClass("disabled");
    
    $("#roles").masonry({
        itemSelector: "#roles-item",
    });
';

$this->registerJs($jscript); ?>