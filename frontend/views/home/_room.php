<?php
use restotech\standard\backend\components\Tools;


foreach ($modelMtableCategory as $mtableCategory): ?>            

    <a id="room" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/table', 'id' => $mtableCategory['id']]) ?>">
        <div class="col-lg-3 col-md-3 col-sm-3 mb">
            <div class="content-panel pn">
                <div id="profile-02" style="background: url('<?= Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/mtable-category/', $mtableCategory['image'], 350, 350) ?>') no-repeat center top; background-size: cover;">
                    <div class="user"></div>
                </div>
                <div class="pr2-social centered">
                    <span class="badge" style="margin-top: 10px; font-size: 22px; font-weight: bold; background-color: <?= $mtableCategory['color'] ?>"><?= $mtableCategory['nama_category'] ?></span>
                </div>
            </div>
        </div>
    </a>

<?php
endforeach; ?>

<?php

$jscript = '
    
    $("a#room").on("click", function() {
    
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