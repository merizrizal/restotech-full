<?php
use yii\helpers\Html; 
use restotech\standard\backend\components\VirtualKeyboard; ?>


<div class="col-lg-12">

    <div class="row mt">
        <div class="col-md-12 col-sm-12">
            <div class="weather-2 pn" style="height: auto; padding: 0 0 20px 0">
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">                            
                            <p>
                                &nbsp;
                            </p>
                        </div>
                    </div>                    
                </div>
                
                
                <div class="row data mt">
                    
                    <div class="col-lg-3"></div> 
                    
                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="control-label" for="invoice-id">No. Faktur</label>
                                </div>
                                <div class="col-lg-6">
                                    <div class="col-lg-12">
                                        <?= Html::textInput('invoiceId', null, ['class' => 'form-control', 'id' => 'invoice-id']) ?>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="help-block"></div>                                        
                                </div>                                    
                            </div>
                        </div>   
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    echo Html::a('<i class="fa fa-floppy-o"></i> Submit', [Yii::$app->params['module'] . 'home/' . $type.  '-invoice-submit'], ['id' => 'submit', 'class' => 'btn btn-success']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3"></div>                                        
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerJsFile();

$jscript = '
    $("#submit").on("click", function() {
    
        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).attr("href"),
            data: {
                "id": $("#invoice-id").val()
            },
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
                swal("Error", "Terjadi kesalahan dalam proses submit No. Faktur.", "error");

                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });
    
    $("#cancel").on("click", function() {
    
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

' . $virtualKeyboard->keyboardQwerty('#invoice-id');

$this->registerJs($jscript); ?>