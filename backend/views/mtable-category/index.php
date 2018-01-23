<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $searchModel restotech\standard\backend\models\search\MtableCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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

$this->title = 'Ruangan';
$this->params['titleH1'] = '&nbsp;&nbsp;&nbsp;' . Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create New Data', ['create'], ['class' => 'btn btn-success'])
        . '&nbsp;&nbsp;&nbsp;'
        . Html::a('<i class="fa fa-map"></i>&nbsp;&nbsp;&nbsp;' . 'Layout', ['mtable/table-layout'], ['class' => 'btn btn-primary']);;
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="mtable-category-index">

   <?php 
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);

    $jscript = '$(\'[data-toggle="tooltip"]\').tooltip();'
            . Yii::$app->params['checkbox-radio-script']()
            . '$(".iCheck-helper").parent().removeClass("disabled");'
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
            ['content'=>
                Html::a('<i class="fa fa-repeat"></i>', ['index'], [
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
            'nama_category',
            [
                'attribute' => 'color',
                'format' => 'raw',
                'value' => function ($model, $index, $widget) {
                    $value = !empty($model->color) ? '<i style="background-color: ' . $model->color.  ';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>' : '';
                    return $value;
                },
            ],
            'keterangan:ntext',
            [
                'attribute' => 'not_active',
                'format' => 'raw',
                'filter' =>  [1 => 'True', 0 => 'False'],
                'value' => function ($model, $index, $widget) {                    
                    return Html::checkbox('not_active[]', $model->not_active, ['value' => $index, 'disabled' => 'disabled']);
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 100px">'
                                    . '{view}{update}{delete}{mtable}'
                            . '</div>',
                'buttons' => [
                    'view' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-search-plus"></i>', $url, [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                    'update' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-pencil"></i>', $url, [
                            'id' => 'update',
                            'class' => 'btn btn-success',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Edit',
                        ]);
                    },
                    'delete' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',                            
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Delete',
                            'model-id' => $model->id,
                            'model-name' => $model->nama_category,
                        ]);
                    },
                    'mtable' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-coffee"></i>', ['mtable/index', 'cid' => $model->id], [
                            'id' => 'mtable',
                            'class' => 'btn btn-default',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Meja',
                        ]);
                    },
                ]
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-striped table-hover'
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