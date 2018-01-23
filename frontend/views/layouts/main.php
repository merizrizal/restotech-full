<?php
use yii\helpers\Html;
use restotech\standard\frontend\assets\AppAsset;
use restotech\standard\frontend\components\AppHeader;
use restotech\standard\frontend\components\NotificationDialog;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$failEndDay = Yii::$app->session->getFlash('fail-end-day');

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null && !$failEndDay) {

    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

} ?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="app" content="<?= Html::encode(Yii::$app->name) ?>">
        <?= Html::csrfMetaTags() ?>

        <!-- Favicon -->
        <link rel="icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>" type="image/x-icon">
        <link rel="apple-touch-icon" href="<?= Yii::$app->request->baseUrl . '/media/favicon.png' ?>">

        <title><?= Html::encode(Yii::$app->name) ?></title>

        <?php
        $this->head();?>
    </head>

    <body>
        <?php $this->beginBody() ?>

        <section id="container" >

            <div class="overlay" style="display: none"></div>
            <div class="loading-img" style="display: none"></div>

            <?php
            $header = new AppHeader();
            echo $header->header(); ?>

            <!-- **********************************************************************************************************************************************************
            MAIN CONTENT
            *********************************************************************************************************************************************************** -->
            <!--main content start-->
            <div class="clearfix hidden-lg hidden-md mb"></div>

            <section>
                <section class="wrapper">
                    <div class="row">
                        <?= $content ?>
                    </div><! --/row -->
                </section>
            </section>
            <!--main content end-->

            <?php
            if (!empty($this->params['statusTransactionDay'])):

                $titleAlertTransactionDay = '';
                $contentAlertTransactionDay = '';
                $btnDayText = '';
                $btnDayHref = '';

                $modalAlwaysOnTop = false;

                if ($this->params['statusTransactionDay'] == 'empty') {
                    $titleAlertTransactionDay = 'Tidak ada Start Day';
                    $contentAlertTransactionDay = 'Transaction day untuk hari ini belum ada. Silakan lakukan start day terlebih dahulu';

                    $btnDayText = 'Start Day';
                    $btnDayHref = ['transaction-day/start-day'];

                    $modalAlwaysOnTop = true;
                } else if ($this->params['statusTransactionDay'] == 'over') {
                    $titleAlertTransactionDay = 'Melebihi End Day';
                    $contentAlertTransactionDay = 'Transaction day yang sedang berlangsung sudah melebihi waktu end day. Silakan lakukan end day terlebih dahulu';

                    $btnDayText = 'End Day';
                    $btnDayHref = ['transaction-day/end-day'];

                    $modalAlwaysOnTop = $this->params['isOverTransactionDay'] ? false : true;
                }

                if ($status !== null) {

                    $titleAlertTransactionDay = $message1;
                    $contentAlertTransactionDay = $message2;
                } ?>

                <div class="modal fade" id="modalAlertTransactionDay" tabindex="-1" role="dialog" <?= $modalAlwaysOnTop ? 'data-backdrop="static" data-keyboard="false"' : '' ?>>
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h4 class="box-title" style="color: white"><?= $titleAlertTransactionDay ?></h4>
                            </div>
                            <div class="modal-body">
                                <?= $contentAlertTransactionDay ?>
                            </div><!-- /.box-body -->
                            <div class="modal-footer">
                                <?= Html::a($btnDayText, $btnDayHref, ['class' => 'btn btn-danger', 'data-method' => 'post']) ?>
                            </div>
                        </div><!-- /.box -->
                    </div>
                </div>

                <?php
                $this->registerJs('
                    $("#modalAlertTransactionDay").modal("show");
                ');

            endif; ?>

        </section>

        <?php
        $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage() ?>
