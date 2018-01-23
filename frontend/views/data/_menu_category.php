<?php
use yii\helpers\Html;

if (count($modelMenuCategory) > 0):
    
    foreach ($modelMenuCategory as $menuCategoryData): 
        
        $color = '';
    
        if (!(empty($menuCategoryData['color']))) {
            $color = ';background-color:' . $menuCategoryData['color'];
        } ?>        

        <a href="#" id="menu-category-id">
            <?= Html::hiddenInput('parent_category_id', $menuCategoryData['parent_category_id'], ['id' => 'parent-category-id']) ?>
            <?= Html::hiddenInput('category_id', $menuCategoryData['id'], ['id' => 'category-id']) ?>
            
            <div class="col-md-3 col-sm-3 mb">
                <div class="product-panel-2" style="padding: 10px 0 5px 0<?= $color ?>">   
                    <br>
                    <h5 style="color: #000; font-weight: bold"><?= $menuCategoryData['nama_category'] ?></h5>
                    <br>
                </div>
            </div>
        </a>

    <?php
    endforeach; 
else: ?>

    <br><br><br><br>
    No Data Found
    <br><br><br><br><br>

<?php    
endif; ?>

<?php

$jscript = '
    $("a#menu-category-id").on("click", function(event) {        
        
        var thisObj = $(this);                                   
        
        if ($(this).find("input#parent-category-id").val() == "") {
            
            $.ajax({
                cache: false,
                type: "POST",
                data: {"id": thisObj.find("input#category-id").val()},
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/menu-category']) . '",
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    thisObj.off("click");
                    
                    $("#menu-container").html(response);
                    
                    $(".overlay").hide();
                    $(".loading-img").hide();                    
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    $(".overlay").hide();
                    $(".loading-img").hide();
                    
                    swal("Error", "Terjadi kesalahan dalam data menu", "warning");
                }
            });
        } else {
            $.ajax({
                cache: false,
                type: "POST",
                data: {"id": thisObj.find("input#category-id").val()},
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/menu']) . '",
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    thisObj.off("click");
                    
                    $("#load-menu-back").css("display", "none");
                    
                    $("#menu-container").html(response);
                    
                    $(".overlay").hide();
                    $(".loading-img").hide();                                        
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    $(".overlay").hide();
                    $(".loading-img").hide();
                    
                    swal("Error", "Terjadi kesalahan dalam data menu", "warning")
                }
            });            
        }
        
        return false;
    });
';

if (!empty($pid)) {
    
    $jscript .= '
        $("#load-menu-back").css("display", "block");
        
        $("#load-menu-back").off("click");
        
        $("#load-menu-back").on("click", function(event) {
            
            $.ajax({
                cache: false,
                type: "POST",
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/menu-category']) . '",
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    $("#load-menu-back").css("display", "none");
                    
                    $("#load-menu-back").off("click");
                    
                    $("#menu-container").html(response);
                    
                    $(".overlay").hide();
                    $(".loading-img").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    $(".overlay").hide();
                    $(".loading-img").hide();
                    
                    swal("Error", "Terjadi kesalahan dalam data menu", "warning");
                }
            });
            
            return false;
        });
    ';
}

$this->registerJs($jscript); ?>

