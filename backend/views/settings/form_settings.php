<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Inflector;
use kartik\file\FileInput;
use kartik\time\TimePicker;
use restotech\standard\backend\components\NotificationDialog;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Settings */

$this->title = 'Setting ' . $judul;
$this->params['breadcrumbs'][] = $this->title;

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

<div class="settings-create">

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="settings-form">

                        <?php $form = ActiveForm::begin([
                                'options' => [
                                    'enctype' => 'multipart/form-data'
                                ],
                                'fieldConfig' => [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-12',
                                        '{theLabel}' => '',
                                    ],
                                    'template' => '<div class="row">'
                                                    . '<div class="col-lg-3">'
                                                        . '{theLabel}'
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
                        
                        <?php
                        foreach ($models as $key => $model) :

                            echo $form->field($model, '[' . $key . ']' . 'setting_name',[
                                    'template' => '{input}'
                                ])->input('hidden');

                            if ($model->type == 'file') {
                                
                                echo $form->field($model, '[' . $key . ']' . 'setting_value', [
                                        'parts' => [
                                            '{theLabel}' => Inflector::camel2words($model->setting_name),
                                        ]
                                    ])->widget(FileInput::classname(), [
                                        'options' => [
                                            'accept' => 'image/*'
                                        ],
                                        'pluginOptions' => [
                                            'initialPreview' => [
                                                Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/company/', 'setting_value', 200, 140), ['class'=>'file-preview-image']),
                                            ],
                                            'showRemove' => true,
                                            'showUpload' => false,
                                        ]
                                    ]);                                
                            } else if ($model->type == 'boolean') {
                                
                                echo $form->field($model, '[' . $key . ']' . 'setting_value', [
                                        'parts' => [
                                            '{theLabel}' => Inflector::camel2words($model->setting_name)
                                        ]
                                    ])->checkbox([], false);
                            } else if ($model->type == 'short_text' || $model->type == 'number') {                                       
                                
                                echo $form->field($model, '[' . $key . ']' . 'setting_value', [
                                    'parts' => [
                                        '{theLabel}' => Inflector::camel2words($model->setting_name)
                                    ]
                                ])->textInput();
                            } else if ($model->type == 'time') {                                       
                                
                                echo $form->field($model, '[' . $key . ']' . 'setting_value', [
                                    'parts' => [
                                        '{theLabel}' => Inflector::camel2words($model->setting_name)
                                    ]
                                ])->widget(TimePicker::className(), [
                                    'pluginOptions' => Yii::$app->params['timepickerOptions'], 
                                ]);

                            } else if ($model->type == 'long_text') {                                       
                                
                                echo $form->field($model, '[' . $key . ']' . 'setting_value', [
                                    'parts' => [
                                        '{theLabel}' => Inflector::camel2words($model->setting_name)
                                    ]
                                ])->textarea(['rows' => 4]);
                            }
                        
                        endforeach; ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                    echo Html::submitButton($icon . 'Update', ['class' => 'btn btn-primary']); ?>
                                    <a class="btn btn-default" href="">
                                        <i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel
                                    </a>
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

</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>