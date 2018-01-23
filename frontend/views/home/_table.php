<?php
use yii\helpers\Html;
use restotech\standard\backend\components\Tools; ?>

<div class="col-lg-12 col-md-12 mb">
    <div class="grey-panel pn" style="height: auto">
        <div class="grey-header">
            <div class="row">
                <div class="col-lg-2 col-md-2" style="text-align: left">
                    <h5 style="padding-left: 5px">
                        <?= Html::a('<i class="fa fa-arrow-circle-left"></i> Back', Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/room']), ['id' => 'back']) ?>
                    </h5>
                </div>
                <div class="col-lg-10 col-md-10">
                    <span class="badge" style="margin-top: 3px; font-size: 22px; font-weight: bold; background-color: <?= $modelMtableCategory['color'] ?>"><?= $modelMtableCategory['nama_category'] ?></span>
                </div>
            </div>                                        
        </div>

        <div class="row" style="padding: 0 15px">

            <?php
            if (!empty($modelMtableCategory['mtables'])):
                foreach ($modelMtableCategory['mtables'] as $mtable): 

                    $sessId = null;
                    $tableId = $mtable['id'];
                    $tableCid = $mtable['mtable_category_id'];
            
                    $badge = '';
                    $tableStatus = '';
                    $joinStatus = '';
                    
                    $jmlTamu = '';
                    $namaTamu = '';

                    if (count($mtable['mtableSessions']) > 0) {
                        
                        $sessId = $mtable['mtableSessions'][0]['id'];
                        
                        $badge = 'bg-important';
                        $tableStatus = 'Not Available';
                        $joinStatus = $mtable['mtableSessions'][0]['is_join_mtable'] ? '<br>Gabung' : '';                                                
                        
                        if ($joinStatus != '') {                            
                            
                            $sessId = $mtable['mtableSessions'][0]['mtableSessionJoin']['mtableJoin']['activeMtableSession']['id'];
                            $tableId = $mtable['mtableSessions'][0]['mtableSessionJoin']['mtableJoin']['activeMtableSession']['mtable']['id'];
                            $tableCid = $mtable['mtableSessions'][0]['mtableSessionJoin']['mtableJoin']['activeMtableSession']['mtable']['mtable_category_id'];
                        }
                        
                        if (count($mtable['mtableSessions']) > 1) {
                            
                            $namaTamu = '<span class="badge bg-primary">Split</span>';
                            
                            foreach ($mtable['mtableSessions'] as $mtableSession) {
                                $jmlTamu += $mtableSession['jumlah_tamu'];
                            }
                            
                            $jmlTamu = 'Tamu: ' . $jmlTamu;
                        } else {
                        
                            $jmlTamu = 'Tamu: ' . $mtable['mtableSessions'][0]['jumlah_tamu'];
                            $namaTamu = $mtable['mtableSessions'][0]['nama_tamu'];
                        }
                    } else {
                                                
                        $badge = 'bg-success';
                        $tableStatus = 'Available'; 
                    } ?>

                    <a id="table" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/view-session', 'id' => $tableId, 'cid' => $tableCid, 'sessId' => $sessId]) ?>" data-sess-id="<?= $sessId ?>">
                        <!-- WEATHER-2 PANEL -->
                        <div class="col-lg-3 col-md-3 col-sm-3 mb">
                            <div id="mtable" class="weather-2 pn" style="height: 280px; cursor: pointer">                      
                                <div class="weather-2-header">
                                    <div class="row">
                                        <div class="col-sm-6 col-xs-6 goleft">
                                            <span class="badge bg-primary" style="margin-left: 5px; font-size: 20px"><?= $mtable['nama_meja'] ?></span>                                                                                                   
                                        </div>
                                        <div class="col-sm-6 col-xs-6 goright">
                                            <p class="small"><span class="badge <?= $badge ?>"><?= $tableStatus . $joinStatus ?></span></p>
                                        </div>
                                    </div>
                                </div><!-- /weather-2 header -->
                                <div class="row centered">
                                    <img src="<?= Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/mtable/', $mtable['image'], 150, 150) ?>" class="img-circle">			
                                </div>
                                <div class="row data">
                                    <div class="col-sm-6 col-xs-6 goleft">                                        
                                        <h6>
                                            Kursi: <?= $mtable['kapasitas'] ?>
                                            <br><br>
                                            <?= $jmlTamu ?>
                                        </h6>

                                    </div>

                                    <div class="col-sm-6 col-xs-6 goright">                 
                                        <h6>
                                            Atas Nama:
                                            <br><br>
                                            <?= $namaTamu ?>
                                       </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                <?php
                endforeach;
            endif; ?>

        </div><!-- /row -->
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
    
    $("a#table").on("click", function() {
    
        var thisObj = $(this);
        
        var ajax = function(thisObj) {
            $.ajax({
                cache: false,
                type: "POST",
                url: thisObj.attr("href"),
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
        };
        
        if (thisObj.attr("data-sess-id")) {
        
            ajax(thisObj);
        } else {
        
            swal({
                title: "Open Meja Ini?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(
        
                function () {

                    ajax(thisObj);
                },
                function(dismiss) {

                }
            );
        }
        
        return false;
    });
';

$this->registerJs($jscript); ?>