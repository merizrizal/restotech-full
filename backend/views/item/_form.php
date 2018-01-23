<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use restotech\standard\backend\models\ItemCategory;
use restotech\standard\backend\models\Storage;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Item */
/* @var $form yii\widgets\ActiveForm */

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

endif; ?>

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

<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="box box-danger">
            <div class="box-body">
                <div class="item-form">                    
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!$model->isNewRecord)
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create'], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (!$model->isNewRecord) {
                        echo $form->field($model, 'id', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput(['maxlength' => true, 'readonly' => 'readonly']);
                    } ?>
                    
                    <?= $form->field($model, 'parent_item_category_id')->dropDownList(
                            ArrayHelper::map(
                                ItemCategory::find()->where(['IS', 'parent_category_id', NULL])->orderBy('nama_category')->asArray()->all(), 
                                'id', 
                                function($data) { 
                                    return $data['nama_category'] . ' (' . $data['id'] . ')';                                 
                                }
                            ), 
                            [
                                'prompt' => '',
                            ]) ?>

                    <?= $form->field($model, 'item_category_id')->textInput(['maxlength' => 16]) ?>

                    <?= $form->field($model, 'nama_item')->textInput(['maxlength' => 32]) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 2]) ?>

                    <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: center">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                            </div>
                        </div>
                    </div>
                                                                              
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2"></div>
</div><!-- /.row -->

<div class="row">
    <div class="col-sm-12">
        <div class="box box-danger">
            <div class="box-body">
                <div class="item-form">
                    <div class="row">                                            
                        <?php
                        $template =[
                            'template' => '{label}<div style="{width}">{input}</div>{error}'
                        ];

                        $storageRack = [];
                        for ($i = 1; $i <= count($modelSkus); $i++):
                            $opt = ['style' => 'width:35%'];
                            if ($i == 1)
                                $opt = ['style' => 'width:35%', 'value' => 1, 'readonly' => 'readonly'] ;
                            
                            if (!empty($modelSkus[$i]->storage_rack_id)) {
                                $storageRack[$i]['storageId'] = $modelSkus[$i]->storage_id;
                                $storageRack[$i]['id'] = $modelSkus[$i]->storage_rack_id;
                                $storageRack[$i]['nama'] = $modelSkus[$i]->storageRack->nama_rak;
                                $storageRack[$i]['component'] = '$("input#itemsku-' . $i . '-storage_rack_id")';
                            } ?>

                            <div class="col-lg-3">                                    

                                <?= $form->field($modelSkus[$i], '[' . $i . ']id', [
                                        'template' => $template['template'],
                                        'enableAjaxValidation' => true
                                    ])->textInput(['maxlength' => 16, 'style' => 'width:50%']) ?>                                                                   

                                <?= $form->field($modelSkus[$i], '[' . $i . ']nama_sku', $template)->textInput(['maxlength' => 32]) ?>                                                                

                                <?= $form->field($modelSkus[$i], '[' . $i . ']stok_minimal', $template)->textInput(['style' => 'width:35%']) ?>

                                <?= $form->field($modelSkus[$i], '[' . $i . ']per_stok', $template)->textInput($opt) ?>

                                <?= $form->field($modelSkus[$i], '[' . $i . ']storage_id', $template)->dropDownList(
                                        ArrayHelper::map(
                                            Storage::find()->orderBy('nama_storage')->asArray()->all(), 
                                            'id', 
                                            function($data) { 
                                                return $data['nama_storage'] . ' (' . $data['id'] . ')';                                 
                                            }
                                        ), 
                                        [
                                            'prompt' => '',
                                            'class' => 'form-control itemsku-storage_id'
                                        ]
                                    ); ?>

                                <?= $form->field($modelSkus[$i], '[' . $i . ']storage_rack_id', $template)->textInput(['maxlength' => 20, 'class' => 'form-control itemsku-storage_rack_id']); ?>           
                                
                                <?= $form->field($modelSkus[$i], '[' . $i . ']no_urut', $template)->textInput(['maxlength' => 32, 'style' => 'width:50%', 'value' => $i, 'readonly' => 'readonly']) ?>

                            </div>

                        <?php
                        endfor; ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12" style="text-align: center">
                                    <?php
                                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.row -->

<?php                    
ActiveForm::end(); ?>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#item-parent_item_category_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true                
    });    

    var itemCategory = function(remoteData) {
        $("#item-item_category_id").val(null);
        $("#item-item_category_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };

    itemCategory([]);

    $("#item-parent_item_category_id").on("select2:select", function(e) {
        $("input#item-item_category_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-category/sub-item-category']) . '?id=" + $("#item-parent_item_category_id").select2("data")[0].id,
            success: function(response) {
                itemCategory(response);
            }
        });
    });

    $("#item-parent_item_category_id").on("select2:unselect", function(e) {
        $("#item-item_category_id").val(null).trigger("change");
        itemCategory([]);
    });
    
    $("select.itemsku-storage_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    var storageRack = function(remoteData, component) {
        component.select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };
    
    storageRack([], $("input.itemsku-storage_rack_id"));
    
    $("select.itemsku-storage_id").on("select2:select", function(e) {
        var component = $(this).parent().parent().parent().find("input.itemsku-storage_rack_id");
        
        component.val(null).trigger("change");        
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=" + $(this).select2("data")[0].id,
            success: function(response) {
                storageRack(response, component);
            }
        });
    });
    
    $("select.itemsku-storage_id").on("select2:unselect", function(e) {
        var component = $(this).parent().parent().parent().find("input.itemsku-storage_rack_id");
        component.val(null).trigger("change");
        storageRack([], $("input.itemsku-storage_rack_id"));
    });
';

if (!$model->isNewRecord || $status == 'danger') {
    
    $jscript .= '
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-category/sub-item-category']) . '?id=" + $("#item-parent_item_category_id").select2("data")[0].id,
            success: function(response) {                
                itemCategory(response);
                $("input#item-item_category_id").val("' . $model->item_category_id . '").trigger("change");
            }
        });
    ';
    
    foreach ($storageRack as $key => $value) {

        $jscript .= '
            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=' . $value['storageId'] . '",
                success: function(response) {
                    storageRack(response, ' . $value['component'] . ');' .
                    $value['component'] . '.val("' . $value['id'] . '").trigger("change");
                }
            });
        ';
        
    }
}

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>