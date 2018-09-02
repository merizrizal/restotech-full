<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel restotech\standard\backend\models\search\SaleInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

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

$this->title = 'Faktur Pembayaran';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="sale-invoice-index">

    <?php 
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);
    
    $jscript = '$(\'[data-toggle="tooltip"]\').tooltip();'
            . $modalDialog->getScript();
            
    $this->registerJs($jscript);
    
    $jscript = '<script>' . $jscript . '</script>'; ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'scriptAfterPjax' => $jscript,
        'bordered' => false,
        'floatHeader' => true,
        'panelHeadingTemplate' => '<div class="kv-panel-pager pull-right" style="text-align:right">'
                                    . '{pager}{summary}'
                                . '</div>'                                
                                . '<div class="clearfix"></div>'
        ,
        'panelFooterTemplate' => '<div class="kv-panel-pager pull-right" style="text-align:right">'
                                    . '{summary}{pager}'
                                . '</div>'
                                . '{footer}'
                                . '<div class="clearfix"></div>'
        ,
        'panel' => [
            'heading' => '',
        ],
        'toolbar' => [
            [
                'content' => Html::a('<i class="fa fa-repeat"></i>', ['refund'], [
                            'data-pjax'=>false, 
                            'class' => 'btn btn-success', 
                            'data-placement' => 'top',
                            'data-toggle' => 'tooltip',
                            'title' => 'Refresh'
                ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date:date',
            'mtableSession.mtable.nama_meja',
            'userOperator.kdKaryawan.nama',
            'jumlah_harga:currency',
            'jumlah_bayar:currency',
            'jumlah_kembali:currency',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 75px">'
                                    . '{view}'
                            . '</div>',
                'buttons' => [
                    'view' => function($url, $model, $key) {
                        return Html::a('<i class="fa fa-search"></i>', $url, [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                ]
            ],
        ],
        'pager' => [
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        ],
    ]); ?>
    

</div>

<?= $modalDialog->renderDialog() ?>