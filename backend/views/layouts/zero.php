<?php

use restotech\standard\backend\assets\AppAsset;
use restotech\standard\backend\assets\AdminlteAssets;
use yii\helpers\Html;

$assetCommon = restotech\standard\common\assets\AppAsset::register($this);

AppAsset::register($this);
AdminlteAssets::register($this); ?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <!-- Favicon -->
        <link rel="icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>">
        
        <title><?= Html::encode(Yii::$app->name) . ' - ' . Html::encode($this->title) ?></title>
        <?php 
        $this->head();
        $this->registerCssFile($this->params['assetCommon']->baseUrl . '/css/font-awesome.min.css', ['depends' => 'yii\web\YiiAsset']);
        $this->registerCssFile($this->params['assetCommon']->baseUrl . '/css/ionicons.min.css', ['depends' => 'yii\web\YiiAsset']); ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        
        <div class="wrap">
            <div class="container">
                <?= $content ?>
            </div>
        </div>       
            
        <?php 
        $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage() ?>
