<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use restotech\standard\backend\models\Printer;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\components\ModalDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Customer */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

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

$this->title = 'Printer Kategori Menu';
$this->params['breadcrumbs'][] = ['label' => 'Kategori Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="customer-view">
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    
                </div>
                
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => [
                        'class' => 'table'
                    ],
                    'attributes' => [
                        'id',
                        'nama_category',
                    ],
                ]) ?>
                        
                <br>
                
                <div style="padding: 10px">
                    <?php $form = ActiveForm::begin([
                                'options' => [
                                    
                                ],
                                'fieldConfig' => [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-12'
                                    ],
                                    'template' => '<div class="row">'
                                                    . '<div class="col-lg-3">'
                                                        . '{label}'
                                                    . '</div>'
                                                    . '<div class="col-lg-6">'
                                                        . '<div class="{inputClass}">'
                                                            . '{input}'
                                                        . '</div>'
                                                    . '</div>'
                                                    . '<div class="col-lg-3">'
                                                        . '{error}'
                                                    . '</div>'
                                                . '</div>', 
                                ]
                        ]); ?>                 

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?= Html::a('<i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;' . 'Printer', ['#collapsePrinter'], [
                                            'id' => 'btnCollapsePrinter',
                                            'class' => 'btn btn-success',
                                        ]); ?>
                                </div>
                            </div>
                        </div>

                        <?php
                        $collapse = '';
                        $collapsePrinter = '';
                        if (empty($pid)) {
                            $collapse = 'collapse';
                            $collapsePrinter = 'collapsePrinter'; 
                        } ?>

                        <div class="<?= $collapse ?>" id="<?= $collapsePrinter ?>">

                            <?= $form->field($modelMenuCategoryPrinter, 'menu_category_id', [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-7'
                                    ],
                                ])->textInput(['maxlength' => true, 'value' => $model->id, 'readonly' => 'readonly']) ?>

                            <?= $form->field($modelMenuCategoryPrinter, 'printer')->dropDownList(
                                ArrayHelper::map(
                                    Printer::find()->andWhere(['type' => 'kitchen'])->all(), 
                                    'printer', 
                                    function($data) { 
                                        return $data->printer;                                 
                                    }
                                ), 
                                [
                                    'prompt' => '',
                                    'style' => 'width: 70%',
                                ]) ?>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-6">
                                        <?php
                                        $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                        echo Html::submitButton($modelMenuCategoryPrinter->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                        echo '&nbsp;&nbsp;&nbsp;';
                                        echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['printer', 'id' => $model->id], ['class' => 'btn btn-default']); ?>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        List Printer
                    </h3>                    
                </div>
                <div class="box-body table-responsive no-padding">
                    
                    <?php 
                    $modalDialog = new ModalDialog([
                        'clickedComponent' => 'a#delete',
                        'modelAttributeId' => 'model-id',
                        'modelAttributeName' => 'model-name',
                    ]);

                    $jscript = '
                            $(\'[data-toggle="tooltip"]\').tooltip();
                            $("a#update").on("click", function(event) {
                                $(location).attr("href", $(this).attr("href"));
                            });
                            
                            $("button#btnPrinter").on("click", function(event) {
                                $(location).attr("href", $(this).attr("data-href"));
                            });
                            '                            
                            . $modalDialog->getScript();

                    $this->registerJs($jscript);

                    $jscript = '<script>' . $jscript . '</script>'; ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProviderMenuCategoryPrinter,
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
                                'content' => Html::a('<i class="fa fa-repeat"></i>', ['printer', 'id' => $model->id, 'pid' => $pid], [
                                            'data-pjax'=>false, 
                                            'class' => 'btn btn-success', 
                                            'data-placement' => 'top',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Refresh'
                                ])
                            ],
                        ],
                        'filterModel' => $searchModelMenuCategoryPrinter,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            'id',
                            'printer',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 50px">'
                                                    . '{update}{delete}'
                                            . '</div>',
                                'buttons' => [
                                    'update' =>  function($url, $model, $key) {
                                        return Html::a('<i class="fa fa-pencil"></i>', 
                                            Yii::$app->urlManager->createUrl(['menu-category/printer', 'id' => $model->menuCategory->id, 'pid' => $model->id]), 
                                            [
                                                'id' => 'update',
                                                'class' => 'btn btn-success',
                                                'data-pjax' => '0',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => 'Edit',
                                            ]);
                                    },
                                    'delete' =>  function($url, $model, $key) {
                                        return Html::a('<i class="fa fa-trash"></i>', 
                                            Yii::$app->urlManager->createUrl(['menu-category/printer-delete', 'id' => $model->menuCategory->id, 'pid' => $model->id]), 
                                            [
                                                'id' => 'delete',
                                                'class' => 'btn btn-danger',                            
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => 'Delete',
                                                'model-id' => $model->printer,
                                                'model-name' => '',
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
            </div>
        </div>
    </div>

</div>

<?php   

echo $modalDialog->renderDialog();
    
$jscript = '
    
    $("#btnCollapsePrinter").on("click", function(event) {
        event.preventDefault();
        
        $("#collapsePrinter").collapse("toggle");        
        
        $("#collapsePrinter #menucategoryprinter-printer").val("");
    });       
    
    $("#menucategoryprinter-printer").select2({
        theme: "krajee",
        placeholder: "Select Printer",
        allowClear: true,
    });
';

$this->registerJs($jscript); ?>