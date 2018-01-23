<?php
use restotech\standard\backend\components\VirtualKeyboard; 

$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerCss();
$virtualKeyboard->registerJsFile(); ?>

<div style="padding: 15px 0">

    <div id="home-content">

        

    </div>   
    
</div>

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/sweetalert/sweetalert2.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/jquery-currency/jquery.currency.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/sweetalert/sweetalert2.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/jquery-validator/jquery-validator.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    
';

$this->registerJs($jscript); ?>