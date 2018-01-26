<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView; 

$this->title = 'Antrian Menu'; ?>


<div class="col-lg-12">

    <div class="content-panel mt">
        
        <h4><i class="fa fa-angle-right"></i> <?= $this->title ?></h4>
        <br>

        <div class="row" style="margin: 0 15px">
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

                            'menu_id',
                            'menu.nama_menu',
                            'jumlah',
                            'keterangan',                               
                            'mtableOrder.mtableSession.mtable.nama_meja',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{check}',
                                'buttons' => [
                                    'check' =>  function($url, $model, $key) {                                                                                
                                        
                                        return '<div class="btn-group btn-group-xs" role="group" style="width: 75px">' .
                                                    Html::a('<i class="fa fa-check"></i>', Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'action/queue-finish', 'id' => $model->id]), [
                                                        'id' => 'check',
                                                        'class' => 'btn btn-success',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'right',
                                                        'title' => 'Selesai',
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
                
                    thisObj.parent().parent().parent().fadeOut(100, function() {
                        $(this).remove();
                    });
                } else {
                    swal("Error", "Terjadi kesalahan dalam proses antrian menu.", "error");
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
';

$this->registerJs($jscript); ?>