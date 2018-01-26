<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

$this->title = 'Meja Terisi'; ?>


<div class="col-lg-12">

    <div class="content-panel mt">

        <h4><i class="fa fa-angle-right"></i> <?= $this->title ?></h4>
        <br>

        <div class="row" style="margin: 0 15px">

            <div class="col-md-3" style="margin-bottom: 20px">
                <?= Html::label('Pencarian', null, ['class' => 'control-label']) ?>

                <div class="input-group">
                    <?= Html::textInput('nama_tamu', $namaTamu, ['id' => 'nama-tamu', 'class' => 'form-control', 'placeholder' => 'Nama Tamu']) ?>
                    <span class="input-group-btn">

                        <?php
                        if (!empty($namaTamu)): ?>

                            <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/opened-table']) ?>" class="btn btn-danger search-nama-tamu clear"><i class="fa fa-close"></i></a>

                        <?php
                        endif; ?>

                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/opened-table']) ?>" class="btn btn-success search-nama-tamu"><i class="fa fa-search"></i></a>
                    </span>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-12">

                <?= GridView::widget([
                        'id' => 'opened-table',
                        'dataProvider' => $dataProvider,
                        'pjax' => false,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            
                            [
                                'attribute' => 'nama_meja',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {
                                    $str = null;

                                    if (count($model->mtableSessions) > 0 && $model->mtableSessions[0]->is_join_mtable) {

                                        if ($model->mtableSessions[0]->mtableSessionJoin->mtableJoin->active_mtable_session_id == $model->mtableSessions[0]->id) {

                                            foreach ($model->mtableSessions[0]->mtableSessionJoin->mtableJoin->mtableSessionJoins as $mtableSessionJoin) {

                                                $str .= $mtableSessionJoin->mtableSession->mtable->nama_meja . '<br>';
                                            }
                                        }
                                    }

                                    $str = empty($str) ? $model->nama_meja : $str;

                                    return $str;
                                },
                            ],
                            'mtableCategory.nama_category',
                            [
                                'attribute' => 'opened_at',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {
                                    $str = '';

                                    if (count($model->mtableSessions) > 0) {
                                        $str = Yii::$app->formatter->asDatetime($model->mtableSessions[0]->opened_at);
                                    }

                                    return $str;
                                },
                            ],

                            [
                                'attribute' => 'opened_by',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {
                                    $str = '';

                                    if (count($model->mtableSessions) > 0) {
                                        $str = $model->mtableSessions[0]->userOpened->kdKaryawan->nama;
                                    }

                                    return $str;
                                },
                            ],
                            [
                                'attribute' => 'tamu',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {
                                    $str = '';

                                    if (count($model->mtableSessions) > 0) {

                                        foreach ($model->mtableSessions as $mtableSession) {
                                            $str .= $mtableSession->nama_tamu . '<br>';
                                        }
                                    }

                                    return $str;
                                },
                            ],
                            [
                                'attribute' => 'catatan',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {
                                    $str = '';

                                    if (count($model->mtableSessions) > 0) {
                                        $str = $model->mtableSessions[0]->catatan;
                                    }

                                    return $str;
                                },
                            ],
                            [
                                'attribute' => '',
                                'format' => 'raw',
                                'value' => function ($model, $index, $widget) {
                                    $str = '';

                                    if (count($model->mtableSessions) > 0 && $model->mtableSessions[0]->is_join_mtable) {
                                        $str .= '<span class="badge bg-primary">Gabung</span><br>';                                       
                                    }

                                    if (count($model->mtableSessions) > 1) {
                                        $str .= '<span class="badge bg-error">Split</span><br>';
                                    }

                                    return '<div class="text-center">' . $str . "</div>";
                                },
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' =>  function($url, $model, $key) {

                                        $sessId = null;
                                        $tableId = $model['id'];
                                        $tableCid = $model['mtable_category_id'];

                                        $join = false;

                                        if (count($model['mtableSessions']) > 0) {

                                            $sessId = $model['mtableSessions'][0]['id'];

                                            $badge = 'bg-important';
                                            $tableStatus = 'Not Available';
                                            $join = $model['mtableSessions'][0]['is_join_mtable'] ? true : false;

                                            if ($join != '') {

                                                $sessId = $model['mtableSessions'][0]['mtableSessionJoin']['mtableJoin']['activeMtableSession']['id'];
                                                $tableId = $model['mtableSessions'][0]['mtableSessionJoin']['mtableJoin']['activeMtableSession']['mtable']['id'];
                                                $tableCid = $model['mtableSessions'][0]['mtableSessionJoin']['mtableJoin']['activeMtableSession']['mtable']['mtable_category_id'];
                                            }
                                        }

                                        return '<div class="btn-group btn-group-xs" role="group" style="width: 75px">' .
                                                    Html::a('<i class="fa fa-external-link-square"></i>',
                                                        Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/view-session', 'id' => $tableId, 'cid' => $tableCid, 'sessId' => $sessId]),
                                                        [
                                                            'id' => 'check',
                                                            'class' => 'btn btn-success',
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'right',
                                                            'title' => 'View Table',
                                                        ]
                                                    ) .
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

    $("a#check").on("click", function() {

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

    $(".search-nama-tamu").on("click", function() {

        var namaTamu = $("#nama-tamu").val();

        if ($(this).hasClass("clear")) {
            namaTamu = "";
        }

        $.ajax({
            cache: false,
            type: "POST",
            data: {
                "nama_tamu": namaTamu
            },
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