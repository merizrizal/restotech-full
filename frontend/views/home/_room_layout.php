<?php
use yii\helpers\Html; ?>

<div class="col-lg-12" style="padding-right: 33px">

    <div class="row">

        <div class="col-lg-12">
            Keterangan warna pada kategori meja :
        </div>

    </div>

    <div style="margin: 10px"></div>

    <div class="row">
        <?php
        foreach ($modelMtableCategory as $mtableCategory): 
            $color = !empty($mtableCategory['color']) ? $mtableCategory['color'] : '#000'; ?>

            <div class="col-lg-2">
                <?= Html::a($mtableCategory['nama_category'], Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/table-layout', 'id' => $mtableCategory['id']]), ['class' => 'btn btn-block room', 'style' => 'margin-top: 5px; color: white; background: ' . $color]) ?>
            </div>

        <?php
        endforeach; ?>
        
        

    </div>

    <div style="margin: 10px"></div>

    <div class="row">        
        <div class="col-lg-9">
            <canvas id="table-layout" width="800" height="600" style="background: rgb(200, 200 ,200)">

            </canvas>   
        </div>
        <div class="col-lg-3">
            <div id="info-table" style="width: 100%; height: 600px; background: rgb(200, 200 ,200)">                

            </div>            
        </div>
    </div>
    
</div>
<?php
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/jcanvas/jcanvas.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var tableClick = function(idTable) {
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "id": idTable
            },
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/info-table']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
            
                $("#info-table").html(response);   
                
                $(".overlay").hide();
                $(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
                $(".overlay").hide();
                $(".loading-img").hide();
                
                swal("Error", xhr.responseText, "error");
            }
        });
    };
    
    var drawingArc = function(idTable, posX, posY, color) {
        return {
            layer: true,
            name: idTable,
            fillStyle: color,
            x: posX, 
            y: posY,
            radius: 40,
            click: function(layer) {
                tableClick(idTable);
            }
        };
    };

    var drawingRect = function(idTable, posX, posY, color) {
        return {
            layer: true,
            name: idTable,
            fillStyle: color,
            x: posX, 
            y: posY,
            width: 80,
            height: 80,
            click: function(layer) {
                tableClick(idTable);
            }
        };
    };

    var drawingText = function(idTable, posX, posY, text) {
        return {
            layer: true,
            name: idTable + "text",
            fillStyle: "#fff",
            fontStyle: "bold",
            fontSize: "10pt",
            fontFamily: "Trebuchet MS, sans-serif",
            x: posX, 
            y: posY,
            text: text
        };
    };
    
    $(".room").on("click", function() {
        
        $.ajax({
            cache: false,
            dataType: "json",
            type: "POST",
            url: $(this).attr("href"),
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
            
                $("canvas#table-layout").clearCanvas();
                $("canvas#table-layout").removeLayers();
                
                $.each(response.table, function(i, table) {
                
                    var drawingTemp;
                    
                    if (table.shape == "Circle") {
                        drawingTemp = $("canvas#table-layout").drawArc(drawingArc(table.id, table.layout_x, table.layout_y, table.mtableCategory.color));
                    } else if (table.shape == "Rectangle") {
                        drawingTemp = $("canvas#table-layout").drawRect(drawingRect(table.id, table.layout_x, table.layout_y, table.mtableCategory.color));
                    }
                    
                    drawingTemp.drawText(drawingText(table.id, table.layout_x, table.layout_y, table.nama_meja));
                });
                
                $(".overlay").hide();
                $(".loading-img").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {                     

                $(".overlay").hide();
                $(".loading-img").hide();

                swal("Error", xhr.responseText, "error");
            }
        });
        
        return false;
    });
';

$this->registerJs($jscript); ?>