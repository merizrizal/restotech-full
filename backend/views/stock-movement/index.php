<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel restotech\standard\backend\models\search\StockMovementSearch */
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

$column = [];

array_push($column, ['class' => 'yii\grid\SerialColumn']);

array_push($column,        
        'tanggal',            
        'item.nama_item',
        'jumlah',
        'itemSku.nama_sku'
);           

switch ($type) {
    case 'Inflow':
        $this->title = 'Stok Masuk';        
        
        array_push($column,
                'storageTo.nama_storage',
                'storageRackTo.nama_rak'
        );
        
        break;
    
    case 'Outflow':
        $this->title = 'Stok Keluar';
        
        array_push($column,
                'storageFrom.nama_storage',
                'storageRackFrom.nama_rak'
        );
        
        break;
    
    case 'Transfer':
        $this->title = 'Stok Transfer';
        
        array_push($column,
                'storageFrom.nama_storage',
                'storageRackFrom.nama_rak',
                'storageTo.nama_storage',
                'storageRackTo.nama_rak'
        );
        
        break;
    
    default:
        break;
}

array_push($column, [
    'class' => 'yii\grid\ActionColumn',
    'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 50px">'
                        . '{update}'
                . '</div>',
    'buttons' => [
        'update' => function($url, $model, $key) {
            return Html::a('<i class="fa fa-pencil"></i>', $url, [
                'id' => 'update',
                'class' => 'btn btn-success',
                'data-pjax' => '0',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => 'Edit',
            ]);
        },
    ]
]);
        
Yii::$app->formatter->timeZone = 'Asia/Jakarta';

$this->params['breadcrumbs'][] = $this->title;
$this->params['titleH1'] = '&nbsp;&nbsp;&nbsp;' . Html::a('<i class="fa fa-tag"></i>&nbsp;&nbsp;&nbsp;' . 'Input ' . $this->title, ['stock/input-stock', 'type' => $type], ['class' => 'btn btn-success']);?>

<div class="stock-movement-index">

    <?php 
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);
    
    $jscript = '$(\'[data-toggle="tooltip"]\').tooltip();'
            . '$("input[name=\"StockMovementSearch[tanggal]\"").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});'
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
                'content' => 
                    Html::a('All', ['index', 'type' => $type, 'date' => 'all'], [                        
                        'data-pjax'=>false, 
                        'class' => 'btn btn-primary', 
                    ]) . 
                    Html::a('Today', ['index', 'type' => $type, 'date' => 'today', 'StockMovementSearch[tanggal]' => Yii::$app->formatter->asDate(time())], [                        
                        'data-pjax'=>false, 
                        'class' => 'btn btn-primary', 
                    ])
            ],
        ],
        'filterModel' => $searchModel,
        'columns' => $column,
        'pager' => [
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        ],
    ]); ?>
    

</div>

<?= $modalDialog->renderDialog() ?>