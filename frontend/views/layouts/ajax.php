<?php

unset($this->assetBundles['restotech\standard\common\assets\AppAsset']);
unset($this->assetBundles['yii\web\YiiAsset']);
unset($this->assetBundles['yii\web\JqueryAsset']);
unset($this->assetBundles['yii\bootstrap\BootstrapAsset']);
unset($this->assetBundles['yii\bootstrap\BootstrapPluginAsset']);

$this->beginPage();
$this->head();
$this->beginBody();
echo $content;
$this->endBody();
$this->endPage(true); ?>