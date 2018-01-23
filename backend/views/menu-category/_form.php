<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use restotech\standard\backend\models\MenuCategory;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\MenuCategory */
/* @var $form yii\widgets\ActiveForm */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

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
                <div class="menu-category-form">

                    <?php $form = ActiveForm::begin([
                            'options' => [
                                
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
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create'], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (!$model->isNewRecord) {
                        
                        echo $form->field($model, 'id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput(['maxlength' => true, 'readonly' => 'readonly']);
                    } ?>

                    <?= $form->field($model, 'nama_category')->textInput(['maxlength' => 128]) ?>
                    
                    <?= $form->field($model, 'parent_category_id')->dropDownList(
                            ArrayHelper::map(
                                MenuCategory::find()->andWhere(['IS', 'parent_category_id', NULL])->andWhere(['not_active' => 0])->orderBy('nama_category')->asArray()->all(), 
                                'id', 
                                function($data) { 
                                    return $data['nama_category'] . ' (' . $data['id'] . ')';                                 
                                }
                            ), 
                            [
                                'prompt' => '',
                            ]) ?>

                    <?= $form->field($model, 'color', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                            'template' => '<div class="row">'
                                            . '<div class="col-lg-3">'
                                                . '{label}'
                                            . '</div>'
                                            . '<div class="col-lg-6">'
                                                . '<div class="{inputClass}">'
                                                    . '<div class="input-group my-colorpicker">'
                                                        . '{input}'
                                                        . '<div class="input-group-addon">'
                                                            . '<i></i>'
                                                        . '</div>'
                                                    . '</div>'
                                                . '</div>'
                                            . '</div>'
                                            . '<div class="col-lg-3">'
                                                . '{error}'
                                            . '</div>'
                                        . '</div>',
                        ])->textInput(['maxlength' => 7]) ?>
                    
                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 2]) ?>

                    <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>      
                    
                    <?php
                    if (!$model->isNewRecord): ?>
                    
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"><label class="control-label">Printer</label></div>
                                <div class="col-lg-6"><?= Html::a('<i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Printer', ['printer', 'id' => $model->id], ['class' => 'btn btn-success',]) ?></div>
                            </div>
                        </div>
                    
                    <?php
                    endif; ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
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
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/colorpicker/bootstrap-colorpicker.min.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/colorpicker/bootstrap-colorpicker.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $(".my-colorpicker").colorpicker();' . Yii::$app->params['checkbox-radio-script']() . '
        
    $("#menucategory-parent_category_id").select2({
        theme: "krajee",
        placeholder: "Select Parent Category",
        allowClear: true
    });
    
';

$this->registerJs($jscript); ?>
