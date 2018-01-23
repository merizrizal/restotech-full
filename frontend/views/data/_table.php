<?php
use yii\helpers\Html; ?>

<div class="row">
    <div class="col-sm-12 mb">
        <?= Html::a('<i class="fa fa-arrow-circle-left"></i> Back', Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/table-category', 'isOpened' => $isOpened]), ['id' => 'back-table', 'class' => 'mb']) ?>
    </div>
</div>

<div class="row">
    
    <?php                
    $flag = false;
    $tableFlag = false;
    
    foreach ($modelMtable as $dataMtable):
        
        $tableFlag = $isOpened ? (count($dataMtable['mtableSessions']) > 0) : (count($dataMtable['mtableSessions']) == 0);        
        
        if ($tableFlag):
            
            $flag = true; ?>
    
            <div class="col-sm-4 mb">

                <?= Html::radio('table', false, ['id' => $dataMtable['id'], 'class' => 'table', 'value' => $dataMtable['id']]) . ' ' . Html::label($dataMtable['nama_meja'], $dataMtable['id']) ?>
                
                <?= Html::hiddenInput('splitted', (count($dataMtable['mtableSessions']) > 1), ['id' => $dataMtable['id'], 'class' => 'splitted']) ?>

            </div>
    
        <?php
        endif;
    endforeach;
    
    if (!$flag): ?>

        <div class="col-sm-12 mb">
            <br>
            Tidak ada meja yang <?= $isOpened ? 'diisi' : 'kosong' ?>
            <br>
        </div>

    <?php
    endif; ?>
    
</div>

<?php
$jscript = '
    $("#back-table").on("click", function() {
        
        var thisObj = $(this);
    
        $.ajax({
            cache: false,
            type: "POST",
            url: thisObj.attr("href"),
            beforeSend: function(xhr) {
                $("#container-table-list").children(".overlay").show();
                $("#container-table-list").children(".loading-img").show();
            },
            success: function(response) {
                $("#container-table-list").children("#content").html(response);

                $("#container-table-list").children(".overlay").hide();
                $("#container-table-list").children(".loading-img").hide();
            }
        });
        
        return false;
    });
    
    $("input.table").each(function() {
        var self = $(this);
        var label = self.next();
        var label_text = label.text();

        label.remove();
        self.iCheck({
          checkboxClass: "icheckbox_line-blue",
          radioClass: "iradio_line-blue",
          insert: "<div class=\"icheck_line-icon\"></div>" + label_text
        });
    });
';

$this->registerJs($jscript); ?>