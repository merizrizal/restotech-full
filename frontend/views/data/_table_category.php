<?php
use yii\helpers\Html; ?>

<div class="row">

    <?php
    foreach ($modelMtableCategory as $dataMtableCategory): ?>

        <div class="col-sm-4 mb">

            <?= Html::a($dataMtableCategory['nama_category'], Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'data/table', 'id' => $dataMtableCategory['id'], 'isOpened' => $isOpened]), ['id' => 'table', 'class' => 'btn btn-success btn-block']) ?>

        </div>

    <?php
    endforeach; ?>

</div>

<?php
$jscript = '
    $("a#table").on("click", function() {

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
';

$this->registerJs($jscript); ?>