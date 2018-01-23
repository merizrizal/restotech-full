<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\models\MtableCategory;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Mtable */
/* @var $form yii\widgets\ActiveForm */

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

<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="box box-danger">
            <div class="box-body">
                <div class="mtable-form">

                    <?php $form = ActiveForm::begin([
                            'options' => [
                                'enctype' => 'multipart/form-data'
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
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!$model->isNewRecord)
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create', 'cid' => $model->mtable_category_id], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (!$model->isNewRecord) {
                        echo $form->field($model, 'id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],                      
                        ])->textInput(['maxlength' => 24, $model->isNewRecord ? '' : 'readonly' => $model->isNewRecord ? '' : 'readonly']);
                    } ?>

                    <?= $form->field($model, 'nama_meja')->textInput(['maxlength' => 32]) ?>

                    <?= $form->field($model, 'kapasitas', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-3'
                            ],
                        ])->textInput(['maxlength' => 10]) ?>

                    <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 2]) ?>

                    <?= $form->field($model, 'not_ppn')->checkbox(['value' => true], false) ?>

                    <?= $form->field($model, 'not_service_charge')->checkbox(['value' => true], false) ?>
                    
                    <?= $form->field($model, 'image')->widget(FileInput::classname(), [
                            'options' => [
                                'accept' => 'image/*'
                            ],
                            'pluginOptions' => [
                                'initialPreview' => [
                                    Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/mtable/', 'image', 200, 200), ['class'=>'file-preview-image']),
                                ],
                                'showRemove' => false,
                                'showUpload' => false,
                            ]
                        ]); ?>
                    
                    <?= $form->field($model, 'shape', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->dropDownList(['Circle' => 'Circle', 'Rectangle' => 'Rectangle']) ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index', 'cid' => $model->mtable_category_id], ['class' => 'btn btn-default']); ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2"></div>
</div><!-- /.row -->

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);


$jscript = '

';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>