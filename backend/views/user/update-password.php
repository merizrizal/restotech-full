<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use restotech\standard\backend\models\Employee;
use restotech\standard\backend\models\UserLevel;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\User */
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

endif;

$this->title = 'Update User Password: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update User Password'; ?>

<div class="user-update">

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="user-form">

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

                        <?= $form->field($model, 'id', [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-7'
                                    ],
                                    'enableAjaxValidation' => true
                                ])->textInput(['maxlength' => 32, 'readonly' => 'readonly']) ?>

                        <?= $form->field($model, 'kd_karyawan', [
                            'enableAjaxValidation' => true
                        ])->dropDownList(
                            ArrayHelper::map(
                                Employee::find()->orderBy('nama')->asArray()->andWhere(['not_active' => false])->all(), 
                                'kd_karyawan', 
                                function($data) { 
                                    return $data['nama'] . ' (' . $data['kd_karyawan'] . ')';                                 
                                }
                            ), 
                            [
                                'prompt' => '',
                            ]) ?>

                        <?= $form->field($model, 'user_level_id')->dropDownList(
                                ArrayHelper::map(
                                    UserLevel::find()->orderBy('nama_level')->asArray()->all(), 
                                    'id', 
                                    function($data) { 
                                        return $data['nama_level'];                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 80%'
                                ]) ?>

                        <?= $form->field($model, 'password')->passwordInput(['maxlength' => 64, 'value' => '']) ?>                        

                        <?php
                        if ($model->isNewRecord)
                            echo $form->field($model, 'password')->passwordInput(['maxlength' => 64]); ?>

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

</div>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#user-kd_karyawan").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    $("#user-kd_karyawan").prop("disabled", true);

    $("#user-user_level_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    $("#user-user_level_id").prop("disabled", true);
';

$this->registerJs($jscript); ?>
