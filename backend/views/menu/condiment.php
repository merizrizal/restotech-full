<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\components\DynamicFormField;
use restotech\standard\backend\models\Menu;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Menu */

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

$this->title = 'Condiment Menu: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add Condiment'; ?>

<div class="menu-update">
    
    <?php 
    $form = ActiveForm::begin(); 
    
    $dynamicFormMenuCondiment = new DynamicFormField([
        'dataModel' => $modelMenuCondiment,
        'form' => $form,
        'formFields' => [
            'menu_id' => [
                'type' => 'dropdown',
                'data' => ArrayHelper::map(
                            Menu::find()->andWhere(['is_deleted' => 0])->orderBy('nama_menu')->asArray()->all(), 
                            'id', 
                            function($data) { 
                                return $data['nama_menu'] . ' (' . $data['id'] . ')';                                 
                            }
                        ),
            ]
        ],
        'title' => 'Condiment Menu',
        'columnClass' => 'col-sm-8 col-sm-offset-2'
    ]); ?>        
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    
                </div>
                
                <div class="box-body">
                
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => [
                            'class' => 'table'
                        ],
                        'attributes' => [
                            'id',
                            'nama_menu',
                            'menuCategory.nama_category',
                            'menuSatuan.nama_satuan',                        
                        ],
                    ]) ?>
                    
                </div>
                
                <div class="box-footer">
                    <?php
                    echo Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;Update', ['class' => 'btn btn-primary']);
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    echo $dynamicFormMenuCondiment->component(); ?>
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-footer">
                    <?php
                    echo Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;Update', ['class' => 'btn btn-primary']);
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                </div>
            </div>
        </div>
    </div>
    
<?php
ActiveForm::end(); ?>
    
</div>