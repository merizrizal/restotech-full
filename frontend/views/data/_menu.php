<?php
use yii\helpers\Html;
use restotech\standard\backend\components\Tools;

if (count($modelMenu) > 0):

    foreach ($modelMenu as $menuData):

        $badgeStock = '';

        if (count($menuData['menuRecipes']) > 0) {

            $noStock = '<div class="badge badge-hot"><i class="ion ion-minus" style="font-size: 48px; margin-top: 8px"></i></div>';

            foreach ($menuData['menuRecipes'] as $dataMenuRecipe) {

                if (count($dataMenuRecipe['itemSku']['stocks'] > 0)) {

                    $totalStok = 0;

                    foreach ($dataMenuRecipe['itemSku']['stocks'] as $dataStock) {
                        $totalStok += $dataStock['jumlah_stok'];
                    }

                    if ($totalStok < $dataMenuRecipe['itemSku']['stok_minimal']) {
                        $badgeStock = $noStock;
                    }
                } else {
                    $badgeStock = $noStock;
                }
            }
        } ?>

        <a href="#" id="menu">
            <div class="col-md-3 col-sm-3 mb">
                <div class="product-panel-2 pn" style="padding: 10px 0">

                    <?= $badgeStock ?>

                    <img src="<?= Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/menu/', (!empty($menuData['image']) ? $menuData['image'] : 'noimage.png'), 120, 120) ?>" width="120" class="img-circle" style="margin: 10px 0 10px 0">

                    <h5 class="mt" style="color: #000; font-weight: bold"><?= $menuData['nama_menu'] ?></h5>

                    <h6><?= Yii::$app->formatter->asCurrency($menuData['harga_jual']) ?></h6>

                    <?= Html::hiddenInput('menu_id', $menuData['id'], ['class' => 'menu-id menu']) ?>
                    <?= Html::hiddenInput('nama_menu', $menuData['nama_menu'], ['class' => 'nama-menu menu']) ?>
                    <?= Html::hiddenInput('harga_jual', $menuData['harga_jual'], ['class' => 'harga-jual menu']) ?>

                    <?php
                    if (!empty($menuData['menuCategory']['menuCategoryPrinters'])) {

                        foreach ($menuData['menuCategory']['menuCategoryPrinters'] as $value) {

                            if (!empty($value['printer0']) && !$value['printer0']['not_active']) {

                                    echo Html::hiddenInput('printer', $value['printer0']['printer'], ['class' => 'printer menu']);
                                }
                            }
                    } ?>

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

    ' . Tools::jsHitungServiceChargePajak() . '

    var hitungDiscBill = function() {

        var discountType = $(".discount-type.session").val();

        var discount = parseFloat($(".discount.session").val());

        var harga = parseFloat($(".jumlah-harga.session").val());

        var hargaDisc = 0;

        if (discountType == "Percent") {

            hargaDisc = Math.round(discount * 0.01 * harga);
        } else if (discountType == "Value") {

            hargaDisc = discount;
        }

        return hargaDisc;
    };

    $("a#menu").on("click", function(event) {

        var thisObj = $(this);

        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "warning");
            return false;
        }

        var thisParent = thisObj;
        var menuId = thisParent.find(".menu-id.menu").val();
        var namaMenu = thisParent.find(".nama-menu.menu").val();
        var hargaJual = thisParent.find(".harga-jual.menu").val();

        var harga = parseFloat(hargaJual);

        var totalHarga = harga + parseFloat($(".jumlah-harga.session").val());

        $.ajax({
            cache: false,
            dataType: "json",
            type: "POST",
            data: {
                "sess_id": $(".sess-id.session").val(),
                "jumlah_harga": totalHarga,
                "menu_id": menuId,
                "harga_satuan": harga,
            },
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/save-order']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {

                $(".overlay").hide();
                $(".loading-img").hide();

                if (response.success) {

                    $(".jumlah-harga.session").val(totalHarga);
                    $("#total-harga").html($(".jumlah-harga.session").val());
                    $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
                        
                    var scp = hitungServiceChargePajak(totalHarga, $(".service-charge.session").val(), $(".pajak.session").val());
                    var serviceCharge = scp["serviceCharge"];
                    var pajak = scp["pajak"];
                    var grandTotal = totalHarga + serviceCharge + pajak - hitungDiscBill();

                    $("#service-charge-amount").html(serviceCharge);
                    $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                    $("#tax-amount").html(pajak);
                    $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

                    $("#discbill").html(hitungDiscBill());
                    $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});                                        

                    $("#grand-harga").html(grandTotal);
                    $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

                    var comp = $("#temp-order").children().children().clone();

                    comp.find(".order-id.order").val(response.order_id);
                    comp.find(".parent-id.order").val("");
                    comp.find(".menu-id.order").val(menuId);
                    comp.find(".catatan.order").val("");
                    comp.find(".discount-type.order").val("Percent");
                    comp.find(".discount.order").val(0);
                    comp.find(".harga-satuan.order").val(hargaJual);
                    comp.find(".jumlah.order").val(1);
                    comp.find(".is-free-menu.order").val(0);
                    comp.find(".is-void.order").val(0);

                    var printer = comp.find(".printer.order").clone();
                    comp.find(".printer.order").remove();

                    thisObj.find(".printer.menu").each(function() {
                        printer.val($(this).val());
                        comp.find(".is-void.order").after(printer.clone());
                    });

                    comp.find("#no").html($("#index").val());
                    comp.find("#menu").children("span").html(namaMenu);
                    comp.find("#qty").children("span").first().html(1);
                    comp.find("#subtotal").children("#span-subtotal").html(hargaJual);
                    comp.find("#subtotal").children("#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});

                    $("tbody#order-menu").append(comp);

                    $("#index").val(parseFloat($("#index").val()) + 1);
                } else {

                    swal("Error", "Terjadi kesalahan dalam proses order menu", "warning");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

                $(".overlay").hide();
                $(".loading-img").hide();

                swal("Error", xhr.responseText, "warning");
            }
        });

        return false;
    });
';

if (!$search) {

    $jscript .= '
        $("#load-menu-back").css("display", "block");

        $("#load-menu-back").off("click");

        $("#load-menu-back").on("click", function(event) {

            $.ajax({
                cache: false,
                type: "POST",
                data: {"id": "' . $cid . '"},
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


