<?php
use yii\helpers\Html;
use restotech\standard\backend\components\Tools; 

$jumlahTamu = 0;

foreach ($modelMtableSession as $mtableSession) {
    
    $jumlahTamu += $mtableSession['jumlah_tamu'];
}?>

<div class="col-md-4 col-sm-4">
    <div class="weather-2 pn" style="height: auto">

        <div class="weather-2-header">
            <div class="row">
                <div class="col-sm-6 col-xs-6 goleft">
                    <span class="badge" style="margin: 0 0 10px 5px; font-size: 20px"><?= $modelMtable['nama_meja'] ?></span>                                               
                </div>
            </div>
        </div><!-- /weather-2 header -->
        <div class="row centered">
            <img src="<?= Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/mtable/', $modelMtable['image'], 150, 150) ?>" class="img-circle" width="120">			
        </div>

        <div class="row data">
            <div class="col-sm-6 col-xs-6 goleft">
                <h4><b>Kursi: <?= $modelMtable['kapasitas'] ?></b></h4>
            </div>
            <div class="col-sm-6 col-xs-6 goright">
                <h4><b>Tamu: <?= $jumlahTamu ?></b></h4>
            </div>
        </div>

        <div class="row data">
            <div class="col-sm-12 col-xs-12 mt mb">
                <?= Html::a('<i class="fa fa-undo"></i> Back', Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/table', 'id' => $modelMtable['mtable_category_id']]), ['id' => 'back', 'class' => 'btn btn-danger']) ?>
            </div>                    
        </div>
    </div> 

</div>

<div class="col-md-8 col-sm-8 mb">
    <div class="white-panel pn" style="height: auto">
        <div class="white-header"></div>
        <div style="padding: 0 10px 15px 10px">
            <div class="row goleft">
                <div class="col-md-12 col-sm-12">
                    <?php
                    foreach ($modelMtableSession as $mtableSession): ?>

                    <div class="alert alert-success" style="margin-bottom: 10px; padding: 10px 15px">
                        <div class="row">
                            <div class="col-md-4 col-sm-4">
                                <b>Total Tagihan: <?= Yii::$app->formatter->asCurrency($mtableSession['jumlah_harga']) ?></b>
                            </div>
                            <div class="col-md-3 col-sm-3">
                                <b>Atas Nama: <?= $mtableSession['nama_tamu'] ?></b>
                            </div>                            
                            <div class="col-md-3 col-sm-3">
                                <b>Jumlah Tamu: <?= $mtableSession['jumlah_tamu'] ?></b>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <?= Html::a('<i class="fa fa-sign-in" style="color: #FFF"></i>', Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/open-table', 'id' => $modelMtable['id'], 'cid' => $modelMtable['mtable_category_id'], 'sessId' => $mtableSession['id']]), ['id' => 'open', 'class' => 'btn btn-primary btn-sm pull-right']) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    endforeach; ?>
                </div>                        
            </div>
        </div>
    </div>
</div>

<?php
$jscript = '
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
                $("#home-content").html(xhr.responseText);
                
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });
    
    $("a#open").on("click", function() {
    
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
                $("#home-content").html(xhr.responseText);
                
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });
';

$this->registerJs($jscript); ?>