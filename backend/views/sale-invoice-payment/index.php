<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel restotech\standard\backend\models\search\SaleInvoicePaymentSearch */
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

$this->title = 'Piutang';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="sale-invoice-payment-index">

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
                'content' => Html::a('<i class="fa fa-repeat"></i>', ['ar'], [
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

            'sale_invoice_id',
            'saleInvoice.date:date',
            'paymentMethod.nama_payment',
            [
                'attribute' => 'jumlah_bayar',
                'format' => 'currency',
                'label' => 'Jumlah Hutang',
            ],
            [
                'label' => 'Jumlah Bayar',
                'format' => 'currency',
                'value' => function ($model, $index, $widget) {
        
                    $jumlahBayar = 0;
                    
                    foreach ($model->saleInvoiceArPayments as $saleInvoiceArPayment) {
                        $jumlahBayar += $saleInvoiceArPayment->jumlah_bayar;
                    }
                    
                    return $jumlahBayar;
                },
            ],
            'keterangan:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 75px">'
                                    . '{payment}'
                            . '</div>',
                'buttons' => [
                    'payment' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-dollar"></i>', $url, [
                            'id' => 'payment',
                            'class' => 'btn btn-success',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Pembayaran',
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