<?php

use restotech\standard\backend\assets\AppAsset;
use restotech\standard\backend\assets\AdminlteAssets;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use restotech\standard\backend\components\AppMenu;

AdminlteAssets::register($this);
AppAsset::register($this); ?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?= Html::csrfMetaTags() ?>
        
        <!-- Favicon -->
        <link rel="icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>">

        <title><?= Html::encode(Yii::$app->name) . ' - ' . Html::encode($this->title) ?></title>
        <?php 
        $this->head(); ?>
    </head>
    <body class="hold-transition skin-red sidebar-mini fixed">
        <div class="wrapper">
            
            <?php $this->beginBody() ?>

            <?php 
            $menu = new AppMenu(); 
            echo $menu->header() ?>                
            
            <?= $menu->sideMenu() ?>
            
            <div class="content-wrapper">
                
                <section class="content-header">
                    <h1><?= Html::encode($this->title ) . (isset($this->params['titleH1']) ? $this->params['titleH1'] : '') ?></h1>
                    
                    <?= 
                    Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                    
                </section>
                
                <section class="content">
                    
                    <?= $content ?>
                    
                </section>            
                
            </div>

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 2.0.0
                </div>
                <strong>Copyright &copy; 2017 <a href="http://synctech.co.id">Synctech.ID</a></strong>
            </footer>

        <?php 
        $this->endBody(); ?>       
        </div>
    </body>
</html>
<?php $this->endPage() ?>
