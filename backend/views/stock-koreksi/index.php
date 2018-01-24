<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel restotech\standard\backend\models\StockKoreksiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

yii\widgets\MaskedInputAsset::register($this);

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

$this->title = 'Verifikasi Koreksi Stok';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="stock-koreksi-index">

    <?php 
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);
    
    $jscript = '$(\'[data-toggle="tooltip"]\').tooltip();'
            . Yii::$app->params['checkbox-radio-script']()
            . '
            $("input[name=\"StockKoreksiSearch[date_action]\"").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
            
            $("button#submitSelection").on("click", function(event) {
                $("input#selectedRows").val($("#gridStockKoreksi").yiiGridView("getSelectedRows"));
            });
            '
            . $modalDialog->getScript();
            
    $this->registerJs($jscript);
    
    $jscript = '<script>' . $jscript . '</script>'; ?>
    
    <?= GridView::widget([
        'id' => 'gridStockKoreksi',
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
            'after' => Html::beginForm().           
                            '<div class="form-inline form-group">' .
                                Html::dropDownList('action', null, ['waiting' => 'Waiting', 'approved' => 'Approved', 'rejected' => 'Rejected'], ['class' => 'form-control']) .
                                '&nbsp; &nbsp; &nbsp;' .
                                Html::input('hidden', 'selectedRows', null, ['id' => 'selectedRows']) .
                                Html::submitButton('<i class="fa fa-check"></i>&nbsp; &nbsp;Submit', ['class' => 'btn btn-primary', 'id' => 'submitSelection']) .
                            '</div>' .
                        Html::endForm()
        ],
        'toolbar' => [
            [
                'content' => Html::a('<i class="fa fa-repeat"></i>', ['index'], [
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
            [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple' => false,
                'checkboxOptions' => function($model, $key, $index, $column) {
                    return ['value' => $model->id];
                }
            ],
                    
            ['class' => 'yii\grid\SerialColumn'],

            'item.nama_item',
            'itemSku.nama_sku',
            'storage.nama_storage',
            'storageRack.nama_rak',
            'jumlah',
            'jumlah_awal',
            'date_action:date',
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

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']); 

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']); ?>