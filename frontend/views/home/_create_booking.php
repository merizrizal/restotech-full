<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use restotech\standard\backend\models\MtableCategory;

yii\widgets\MaskedInputAsset::register($this);
kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

$this->title = 'Create Booking'; ?>


<div class="col-lg-12">

    <div class="content-panel mt">
        
        <h4><i class="fa fa-angle-right"></i> <?= $this->title ?></h4>
        <br>

        <div class="row"style="margin: 0 15px">
            <div class="col-md-12">
                
                <?= Html::a('<i class="fa fa-undo"></i> Back', [Yii::$app->params['module'] . 'home/booking'], ['id' => 'back', 'class' => 'btn btn-danger']) ?>
                
                <?= Html::a('<i class="fa fa-check"></i> Create', [Yii::$app->params['module'] . 'action/create-booking'], ['id' => 'create-booking', 'class' => 'btn btn-primary']) ?>
                
            </div>
            
            <div class="clearfix mb"></div>
            
            <div class="col-md-6 col-md-offset-3">
                
                 <?php 
                $form = ActiveForm::begin([
                    'id' => 'booking',
                ]) ?>
                
                <?= $form->field($model, 'nama_pelanggan')->textInput(['maxlength' => true]) ?>
                
                <?= $form->field($modelMtable, 'mtable_category_id')->dropDownList(
                        ArrayHelper::map(                                
                            MtableCategory::find()->orderBy('nama_category')->asArray()->all(), 
                            'id', 
                            function($data) { 
                                return $data['nama_category'];                                 
                            }
                        ), 
                        [
                            'prompt' => '',
                            'style' => 'width: 100%'
                        ]) ?>
                
                <?= $form->field($model, 'mtable_id')->textInput(['maxlength' => true]) ?>
                
                <?= $form->field($model, 'date')->widget(DatePicker::className(), [
                    'pluginOptions' => Yii::$app->params['datepickerOptions'],
                ]) ?>
                
                <?= $form->field($model, 'time')->widget(TimePicker::className(), [                    
                    'pluginOptions' => Yii::$app->params['timepickerOptions'], 
                ]); ?>
                
                <?= $form->field($model, 'keterangan')->textarea() ?>
                
                <?php                    
                ActiveForm::end(); ?>
                
            </div>
            
            <div class="clearfix mb"></div>
        </div>
    </div>
</div>

<?php

$jscript = '    
    $("#mtablebooking-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    
    $("#mtablebooking-time").inputmask("hh:mm", {"placeholder": "hh:mm"});
    
    $("#mtable-mtable_category_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    var mtable = function(remoteData) {
        $("#mtablebooking-mtable_id").val(null);
        $("#mtablebooking-mtable_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true,
            data: remoteData,
        });
    };

    mtable([]);

    $("#mtable-mtable_category_id").on("select2:select", function(e) {
        $("input#mtablebooking-mtable_id").val(null).trigger("change");
        
        $.ajax({
            dataType: "json",
            cache: false,
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/get-mtable']) . '?id=" + $("#mtable-mtable_category_id").select2("data")[0].id,
            success: function(response) {
                mtable(response);
            }
        });
    });

    $("#mtable-mtable_category_id").on("select2:unselect", function(e) {
        $("#mtablebooking-mtable_id").val(null).trigger("change");
        mtable([]);
    });
    
    $("#create-booking").on("click", function() {
    
        $("#booking").trigger("submit");                        
        
        return false;
    });
    
    $("#booking").on("beforeSubmit", function() {
    
        if ($(this).find(".has-error").length == 0) {
    
            $.ajax({
                cache: false,
                type: "POST",
                url: $("#create-booking").attr("href"),
                data: $("#booking").serialize(),
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                
                    if (response.success) {
                        $("#back").trigger("click");
                    } else {
                        swal("Error", "Terjadi kesalahan dalam proses input booking.", "error");
                    }

                    $(".overlay").hide();
                    $(".loading-img").hide();                
                },
                error: function (xhr, ajaxOptions, thrownError) {     
                    swal("Error", xhr.responseText, "error");

                    $(".overlay").hide();
                    $(".loading-img").hide();
                }
            });
        }
        
        return false;
    });
    
    $("#back").on("click", function() {
    
        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).attr("href"),
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#home-content").html(response);
                
                $(".overlay").hide();
                $(".loading-img").hide();                
            },
            error: function (xhr, ajaxOptions, thrownError) {     
                swal("Error", xhr.responseText, "error");
                
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });
';

$this->registerJs($jscript); ?>