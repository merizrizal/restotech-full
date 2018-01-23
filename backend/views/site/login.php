<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use restotech\standard\backend\components\VirtualKeyboard;

$assetCommon = restotech\standard\common\assets\AppAsset::register($this);

$this->title = 'Login';

$settings_company_profile = Yii::$app->session->get('company_settings_profile'); ?>

<div class="login-box" style="opacity: 0.8">
    <div class="login-logo">
        <a href=""><?= Html::img(Yii::getAlias('@uploadsUrl') . '/img/company/' . $settings_company_profile['company_image_file'], ['class' => 'img-responsive']) ?></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to <b><?= Html::encode(Yii::$app->name) ?></b></p>
        <?php 
        $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'fieldConfig' => [
                        'parts' => [
                            '{icon}' => ''
                        ],
                        'template' => '<div class="has-feedback">
                                            {input}
                                            <span class="fa {icon} form-control-feedback"></span>
                                            <br>
                                            {error}
                                        </div>', 
                    ]
                ]); ?>
        
            <div class="form-group has-feedback">
                <?= $form->field($model, 'username', [
                            'parts' => [
                                '{icon}' => 'fa-user'
                            ],
                        ])->textInput(['id' => 'username', 'class' => 'form-control', 'placeholder' => 'User ID']) ?>
                <span class="fa fa-user form-control-feedback"></span>
            </div>
        
            <div class="form-group has-feedback">
                <?= $form->field($model, 'password', [
                            'parts' => [
                                '{icon}' => 'fa-lock'
                            ],
                        ])->passwordInput(['id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password']) ?>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
        
            <div class="row">
                <div class="col-xs-8">                    
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <?= Html::submitButton('Login', ['class' => 'btn bg-primary btn-block btn-flat', 'name' => 'loginButton', 'value' => 'loginButton']) ?>                                          
                </div><!-- /.col -->
            </div>
        
        <?php ActiveForm::end(); ?>
        
        <?= Html::beginForm() ?>
        
            <div class="row" style="margin-top: 15px">
                <div class="col-xs-8">   
                    <?= Html::checkbox('showVirtualKeyboard', Yii::$app->session->get('showVirtualKeyboard', false), ['label' => 'Show Virtual Keyboard']) ?>
                </div><!-- /.col -->
                <div class="col-xs-4">                    
                    <?= Html::submitButton('Set', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'setVirtualKeyboard', 'value' => 'setVirtualKeyboard']) ?>
                </div><!-- /.col -->
            </div>
        
         <?= Html::endForm() ?>

        <div style="margin-top: 25px; text-align: center">
            Crafted by <a href="http://www.syncfactory.co.id">Synctech.ID</a> in Bandung.      
        </div>

    </div><!-- /.login-box-body -->
</div>

<?php

$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerJsFile();
$virtualKeyboard->registerCss();

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
   
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$cssscript = '
    .login-bg {
        background: url("' . Yii::getAlias('@uploadsUrl') . '/img/company/' . $settings_company_profile['company_background_login_file'] . '") fixed;
        background-size: cover;
    }
';

$this->registerCss($cssscript);

$jscript = '
    $("body").addClass("login-bg");
';

$jscript .= $virtualKeyboard->keyboardQwerty('#username') . $virtualKeyboard->keyboardQwerty('#password');
   
$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>

