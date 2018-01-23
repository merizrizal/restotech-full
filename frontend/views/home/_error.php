<?php
use yii\helpers\Html; ?>

<div class="col-lg-12 col-md-12 mb">
    
    <h3><i class="fa fa-angle-right"></i> <?= $title ?></h3>
    <div class="row mt">
            <div class="col-lg-12">
                <p><?= $message ?></p>
                <p><?= Html::a('Back', [Yii::$app->params['module'] . 'home/table', 'id' => $tableCategoryId], ['id' => 'back']) ?></p>
            </div>
    </div>
</div>

<?php

$jscript = '
    
    $("a#back").on("click", function() {
    
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