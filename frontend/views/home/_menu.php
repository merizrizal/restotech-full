<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/room']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-coffee fa-5x"></i>
            <h1>Meja (Basic)</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/room-layout']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-coffee fa-5x"></i>
            <h1>Meja (Layout)</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/opened-table']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-cutlery fa-5x"></i>
            <h1>Meja Terisi</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/menu-queue']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-tasks fa-5x"></i>
            <h1>Antrian Menu</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/menu-queue-finished']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-thumbs-up fa-5x"></i>
            <h1>Menu Siap</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/reprint-invoice']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-print fa-5x"></i>
            <h1>Reprint Faktur</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/correction-invoice']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-edit fa-5x"></i>
            <h1>Koreksi Faktur</h1>            
        </div>
    </a>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 mb">
    <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/booking']) ?>" id="menu">
        <div class="home-menu pn centered">
            <br><br>
            <i class="fa fa-address-book fa-5x"></i>
            <h1>Booking</h1>            
        </div>
    </a>
</div>

<?php

$jscript = '
    
    $("a#menu").on("click", function() {
    
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
                console.log(xhr);
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });
';

$this->registerJs($jscript); ?>