<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView; 

$this->title = 'Booking'; ?>


<div class="col-lg-12">

    <div class="content-panel mt">
        
        <h4><i class="fa fa-angle-right"></i> <?= $this->title ?></h4>
        <br>

        <div class="row" style="margin: 0 15px">
            <div class="col-md-12">
                
                <?= Html::a('Create Booking', Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/create-booking']), ['id' => 'create-booking', 'class' => 'btn btn-primary']) ?>
                
            </div>
            
            <div class="clearfix mb"></div>
            
            <div class="col-md-12" >

                <?= GridView::widget([                    
                        'dataProvider' => $dataProvider,
                        'pjax' => false,
                        'panelHeadingTemplate' => '',
                        'panelFooterTemplate' => '',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                            ],

                            'id',
                            'mtable.nama_meja',
                            'nama_pelanggan',
                            'date:date',                               
                            'time',
                            'keterangan',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{check}',
                                'buttons' => [
                                    'check' =>  function($url, $model, $key) {                                                                                
                                        
                                        return '<div class="btn-group btn-group-xs" role="group" style="width: 75px">' .
                                                    Html::a('<i class="fa fa-check"></i>', Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'action/booking-open', 'id' => $model->id, 'tid' => $model->mtable_id]), [
                                                        'id' => 'check',
                                                        'class' => 'btn btn-success',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'right',
                                                        'title' => 'Open Meja',
                                                    ]) . 
                                                '</div>';
                                    },
                                ]
                            ],
                        ]
                    ]); ?>

            </div>
        </div>
    </div>
</div>

<?php

$jscript = '
    $("a#check").tooltip();
    
    $("a#check").on("click", function(event) {
    
        var thisObj = $(this);
        
        $.ajax({
            cache: false,
            dataType: "json",
            type: "POST",
            url: thisObj.attr("href"),
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                
                if (response.success) {
                
                    $.ajax({
                        cache: false,
                        type: "POST",
                        url: response.open_table,
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
                } else {
                    
                    swal("Error", response.message, "error");
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

        return false;
    });
    
    $("#create-booking").on("click", function() {
    
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