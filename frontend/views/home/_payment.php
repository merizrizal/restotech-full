<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\Tools;
use restotech\standard\backend\components\PrinterDialog;
use restotech\standard\backend\components\VirtualKeyboard;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

$form = new ActiveForm();

$virtualKeyboard = new VirtualKeyboard();

Tools::loadIsIncludeScp();

$discBillValue = 0;

if ($modelMtableSession->discount_type == 'Percent') {

    $discBillValue = round($modelMtableSession->discount * 0.01 * $modelMtableSession->jumlah_harga);
} else if ($modelMtableSession->discount_type == 'Value') {

    $discBillValue = $modelMtableSession->discount;
}

if (!empty($settingsArray)) {
    echo Html::textarea('struk_invoice_header', $settingsArray['struk_invoice_header'], ['id' => 'struk-invoice-header', 'style' => 'display:none']);
    echo Html::textarea('struk_invoice_footer', $settingsArray['struk_invoice_footer'], ['id' => 'struk-invoice-footer', 'style' => 'display:none']);
}

echo Html::hiddenInput('sess_id', $modelMtableSession->id, ['class' => 'sess-id session']);
echo Html::hiddenInput('mtable_id', $modelMtableSession->mtable->id, ['class' => 'mtable-id session']);
echo Html::hiddenInput('nama_tamu', $modelMtableSession->nama_tamu, ['class' => 'nama-tamu session']);
echo Html::hiddenInput('jumlah_tamu', $modelMtableSession->jumlah_tamu, ['class' => 'jumlah-tamu session']);
echo Html::hiddenInput('catatan', $modelMtableSession->catatan, ['class' => 'catatan session']);
echo Html::hiddenInput('jumlah_harga', $modelMtableSession->jumlah_harga, ['class' => 'jumlah-harga session']);
echo Html::hiddenInput('discount_type', $modelMtableSession->discount_type, ['class' => 'discount-type session']);
echo Html::hiddenInput('discount', $modelMtableSession->discount, ['class' => 'discount session']);
echo Html::hiddenInput('pajak', $modelMtableSession->pajak, ['class' => 'pajak session']);
echo Html::hiddenInput('service_charge', $modelMtableSession->service_charge, ['class' => 'service-charge session']);
echo Html::hiddenInput('bill_printed', $modelMtableSession->bill_printed, ['class' => 'bill-printed session']);
echo Html::hiddenInput('opened_table_at', Yii::$app->formatter->asDatetime($modelMtableSession->opened_at, 'dd-MM-yyyy HH:mm'), ['class' => 'open-table-at session']);

echo Html::hiddenInput('user_active', Yii::$app->session->get('user_data')['employee']['nama'], ['id' => 'user-active']);

echo Html::hiddenInput('mtable_nama', $modelMtableSession->mtable->nama_meja, ['class' => 'mtable-nama session']); ?>

<table id="temp-payment" style="display: none">
    <tbody>
        <tr class="payment-row">

            <?= Html::hiddenInput('payment_method_id', null, ['class' => 'payment-method-id payment']) ?>
            <?= Html::hiddenInput('jumlah_bayar', null, ['class' => 'jumlah-bayar payment']) ?>
            <?= Html::hiddenInput('keterangan', null, ['class' => 'keterangan payment']) ?>
            <?= Html::hiddenInput('kode', null, ['class' => 'kode payment']) ?>

            <td id="payment-method-id" class="goleft">
                <?= Html::a('<i class="fa fa-minus-circle" style="color:white"></i>', null, ['id' => 'delete', 'class' => 'btn btn-danger btn-xs'])?> <span></span>
            </td>
            <td id="payment-value" class="goright">
                <span></span> <?= Html::a('<i class="fa fa-pencil-square-o" style="color:white"></i>', null, ['id' => 'keterangan', 'class' => 'btn btn-primary btn-xs'])?>
            </td>
        </tr>
    </tbody>
</table>

<div id="container" class="hidden">
    <div id="temp-keterangan">
        <form>

            <?= $form->field(new \restotech\standard\backend\models\SaleInvoicePayment(), 'keterangan')->textInput(['class' => 'form-control keterangan temp']) ?>

        </form>
    </div>

    <div id="temp-limit-karyawan">
        <div class="form-group ">

            <?= Html::label('Kode Karyawan', 'kode-karyawan', ['class' => 'control-label']) ?>

            <?= Html::textInput('kode_karyawan', null, ['id' => 'kode-karyawan', 'class' => 'form-control kode-karyawan temp']) ?>

        </div>
    </div>

    <div id="temp-voucher">
        <div class="form-group ">

            <?= Html::label('Kode Voucher', 'kode-voucher', ['class' => 'control-label']) ?>

            <?= Html::textInput('kode_voucher', null, ['id' => 'kode-voucher', 'class' => 'form-control kode-voucher temp']) ?>

        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="weather-2 pn" style="height: auto; padding-bottom: 20px">
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <p>
                                Meja: <?= $modelMtableSession->mtable->nama_meja . ' (' . $modelMtableSession->mtable->id . ')' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row data mt">

                    <div class="col-lg-4">
                        <div class="white-panel pn" style="height: auto; color: #000">
                            <div class="white-header" style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-6 goleft">
                                        <a id="bayar" class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(!$isCorrection ? [Yii::$app->params['module'] . 'action/payment'] : [Yii::$app->params['module'] . 'action/payment-correction']) ?>"><i class="fa fa-check" style="font-size: 12px; color: white"></i> Bayar</a>
                                    </div>
                                    <div class="col-md-6 goright">
                                        <a id="back" class="btn btn-danger" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/open-table', 'id' => $modelMtableSession->mtable->id, 'cid' => $modelMtableSession->mtable->mtable_category_id, 'sessId' => $modelMtableSession->id, 'isCorrection' => $isCorrection]) ?>"><i class="fa fa-undo" style="font-size: 12px; color: white"></i> Back</a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">

                                <table class="table table-advance table-hover">

                                    <thead>
                                        <tr>
                                            <th class="goleft">#</th>
                                            <th class="goleft">Menu</th>
                                            <th class="centered" style="width: 60px">Qty</th>
                                            <th class="goright" style="width: 35%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-menu">

                                        <?php
                                        $i = 0;

                                        $jumlah_harga = 0;
                                        $serviceCharge = 0;
                                        $pajak = 0;
                                        $grandTotal = 0;

                                        $totalFreeMenu = 0;
                                        $totalVoid = 0;

                                        if (count($modelMtableSession->mtableOrders) > 0):

                                            foreach ($modelMtableSession->mtableOrders as $i => $mtableOrderData):

                                                $data = [];

                                                if (!empty($mtableOrderData->mtableOrders)) {
                                                    $data[] = $mtableOrderData;
                                                    $data = array_merge($data, $mtableOrderData->mtableOrders);
                                                } else {
                                                    $data[] = $mtableOrderData;
                                                }

                                                foreach ($data as $mtableOrderData):

                                                    $subtotal = $mtableOrderData->jumlah * $mtableOrderData->harga_satuan;

                                                    if ($mtableOrderData->is_free_menu) {

                                                        $totalFreeMenu += $subtotal;
                                                    }

                                                    if ($mtableOrderData->is_void) {

                                                        $totalVoid += $subtotal;
                                                    }

                                                    $disc = 0;

                                                    if ($mtableOrderData->discount_type == 'Percent') {

                                                        $disc = round($mtableOrderData->discount * 0.01 * $subtotal);
                                                    } else if ($mtableOrderData->discount_type == 'Value') {

                                                        $disc = $mtableOrderData->jumlah * $mtableOrderData->discount;
                                                    }

                                                    $subtotal = $subtotal - $disc;

                                                    if (!$mtableOrderData->is_free_menu && !$mtableOrderData->is_void) {

                                                        $jumlah_harga += $subtotal;
                                                    } ?>

                                                    <tr id="menu-row" class="<?= ($mtableOrderData->is_void ? 'voided ' : ($mtableOrderData->is_free_menu ? 'free-menu' : '')) ?>" style="cursor: pointer">

                                                        <?= Html::hiddenInput('order_id', $mtableOrderData->id, ['class' => 'order-id order']) ?>
                                                        <?= Html::hiddenInput('parent_id', $mtableOrderData->parent_id, ['class' => 'parent-id order']) ?>
                                                        <?= Html::hiddenInput('menu_id', $mtableOrderData->menu_id, ['class' => 'menu-id order']) ?>
                                                        <?= Html::hiddenInput('catatan', $mtableOrderData->catatan, ['class' => 'catatan order']) ?>
                                                        <?= Html::hiddenInput('discount_type', $mtableOrderData->discount_type, ['class' => 'discount-type order']) ?>
                                                        <?= Html::hiddenInput('discount', $mtableOrderData->discount, ['class' => 'discount order']) ?>
                                                        <?= Html::hiddenInput('harga_satuan', $mtableOrderData->harga_satuan, ['class' => 'harga-satuan order']) ?>
                                                        <?= Html::hiddenInput('jumlah', $mtableOrderData->jumlah, ['class' => 'jumlah order']) ?>
                                                        <?= Html::hiddenInput('is_free_menu', $mtableOrderData->is_free_menu, ['class' => 'is-free-menu order']) ?>
                                                        <?= Html::hiddenInput('is_void', $mtableOrderData->is_void, ['class' => 'is-void order']) ?>

                                                        <td id="no"><?= empty($mtableOrderData->parent_id) ? $i + 1 : '<i class="fa fa-plus" style="color:green"></i>' ?></td>
                                                        <td id="menu" class="goleft">
                                                            <span><?= $mtableOrderData->menu->nama_menu ?></span>
                                                        </td>
                                                        <td id="qty" class="centered">
                                                            <span><?= $mtableOrderData->jumlah ?></span>
                                                        </td>
                                                        <td id="subtotal" class="goright">
                                                            <span id="span-discount">Disc: <span id="val-discount"><?= $mtableOrderData->discount ?></span></span>
                                                            <br>
                                                            <span id="span-subtotal"><?= $subtotal ?></span>
                                                        </td>
                                                    </tr>

                                                    <?php

                                                endforeach;

                                            endforeach;

                                            $scp = Tools::hitungServiceChargePajak($jumlah_harga, $modelMtableSession->service_charge, $modelMtableSession->pajak);
                                            $serviceCharge = $scp['serviceCharge'];
                                            $pajak = $scp['pajak'];
                                            $grandTotal = $jumlah_harga + $serviceCharge + $pajak - $discBillValue;

                                        endif; ?>

                                    </tbody>
                                    <tfoot>
                                        <tr id="free-menu-row">
                                            <td colspan="2" class="goleft">Total Free Menu</td>
                                            <td colspan="2" class="goright">
                                                <span>(</span><span id="total-free-menu"><?= $totalFreeMenu ?></span><span>)</span>
                                            </td>
                                            <?= Html::hiddenInput('total_free_menu', $totalFreeMenu, ['id' => 'total-free-menu']) ?>
                                        </tr>
                                        <tr id="void-row">
                                            <td colspan="2" class="goleft">Total Void</td>
                                            <td colspan="2" class="goright">
                                                <span>(</span><span id="total-void"><?= $totalVoid ?></span><span>)</span>
                                            </td>
                                            <?= Html::hiddenInput('total_void', $totalVoid, ['id' => 'total-void']) ?>
                                        </tr>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td colspan="2" class="goleft">Total</td>
                                            <td colspan="2" id="total-harga" class="goright"><?= $jumlah_harga ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="goleft">Service (<?= $modelMtableSession->service_charge ?> %)</td>
                                            <td colspan="2" id="service-charge-amount" class="goright"><?= $serviceCharge ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="goleft">Ppn (<?= $modelMtableSession->pajak ?> %)</td>
                                            <td colspan="2" id="tax-amount" class="goright"><?= $pajak ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="goleft">Discount Bill <span id="discbill-text"><?= $modelMtableSession->discount_type === 'Percent' ? '(' . $modelMtableSession->discount . '%)' : '' ?></span></td>
                                            <td colspan="2" class="goright">
                                                (<span id="discbill"><?= $discBillValue ?></span>)
                                            </td>

                                        </tr>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td colspan="2" class="goleft">Grand Total</td>
                                            <td colspan="2" id="grand-harga" class="goright"><?= $grandTotal ?></td>
                                            <?= Html::hiddenInput('grand_harga', $grandTotal, ['class' => 'grand-harga session']) ?>
                                        </tr>
                                    </tfoot>

                                </table>

                                <table class="table table-advance">

                                    <?= Html::hiddenInput('jumlah_bayar', 0, ['id' => 'jumlah-bayar']) ?>
                                    <?= Html::hiddenInput('jumlah_kembali', 0, ['id' => 'jumlah-kembali']) ?>

                                    <thead>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <th class="goleft">
                                                Total Bayar
                                            </th>
                                            <th id="total-bayar" class="goright" style="width: 45%">0</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payment">

                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td class="goleft">
                                                Kembali
                                            </td>
                                            <td id="total-kembali" class="goright">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="white-header" style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-6 goleft">
                                        <a id="bayar" class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(!$isCorrection ? [Yii::$app->params['module'] . 'action/payment'] : [Yii::$app->params['module'] . 'action/payment-correction']) ?>"><i class="fa fa-check" style="font-size: 12px; color: white"></i> Bayar</a>
                                    </div>
                                    <div class="col-md-6 goright">
                                        <a id="back" class="btn btn-danger" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/open-table', 'id' => $modelMtableSession->mtable->id, 'cid' => $modelMtableSession->mtable->mtable_category_id, 'sessId' => $modelMtableSession->id, 'isCorrection' => $isCorrection]) ?>"><i class="fa fa-undo" style="font-size: 12px; color: white"></i> Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="btn-group btn-block">
                                    <button class="btn btn-danger btn-block btn-lg dropdown-toggle" data-toggle="dropdown" type="button" style="height: 83px">Payment<br>Method <span class="caret"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php
                                         foreach ($modelPaymentMethod as $paymentMethodData): ?>

                                        <li><a id="payment-method" data-id="<?= $paymentMethodData['id'] ?>" class="btn-block btn-lg" style="font-size: 18px" href=""><?= $paymentMethodData['nama_payment'] ?></a></li>

                                        <?php
                                         endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span style="font-size: 30px">Total</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <?= MaskMoney::widget(['name' => 'total', 'value' => $grandTotal, 'options' => [
                                            'id' => 'input-jumlah-tagihan',
                                            'class' => 'input-payment',
                                            'style' => 'text-align: right; font-size: 24px; width:100%',
                                            'readonly' => 'readonly'
                                        ]]) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <span style="font-size: 30px">Bayar</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <?= MaskMoney::widget(['name' => 'bayar', 'value' => 0, 'options' => [
                                            'id' => 'input-bayar',
                                            'class' => 'input-payment',
                                            'style' => 'text-align: right; font-size: 24px; width:100%'
                                        ]]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt">
                            <div class="col-lg-3" style="margin-top: 5px">
                                <button id="rp" data-rp="1000" class="btn btn-success btn-block btn-lg" type="button">1.000</button>
                                <button id="rp" data-rp="5000" class="btn btn-success btn-block btn-lg" type="button">5.000</button>
                                <button id="rp" data-rp="10000" class="btn btn-success btn-block btn-lg" type="button">10.000</button>
                                <button id="rp" data-rp="20000" class="btn btn-success btn-block btn-lg" type="button">20.000</button>
                                <button id="rp" data-rp="50000" class="btn btn-success btn-block btn-lg" type="button">50.000</button>
                                <button id="rp" data-rp="100000" class="btn btn-success btn-block btn-lg" type="button">100.000</button>
                                <button id="all" class="btn btn-danger btn-block btn-lg" type="button" style="height: 85px">Pay All</button>
                            </div>
                            <div class="col-lg-9">
                                <button id="number" data-number="7" class="btn btn-primary btn-lg" type="button" style="height: 90px">7</button>
                                <button id="number" data-number="8" class="btn btn-primary btn-lg" type="button" style="height: 90px">8</button>
                                <button id="number" data-number="9" class="btn btn-primary btn-lg" type="button" style="height: 90px">9</button>
                                <button id="number" data-number="4" class="btn btn-primary btn-lg" type="button" style="height: 90px">4</button>
                                <button id="number" data-number="5" class="btn btn-primary btn-lg" type="button" style="height: 90px">5</button>
                                <button id="number" data-number="6" class="btn btn-primary btn-lg" type="button" style="height: 90px">6</button>
                                <button id="number" data-number="1" class="btn btn-primary btn-lg" type="button" style="height: 90px">1</button>
                                <button id="number" data-number="2" class="btn btn-primary btn-lg" type="button" style="height: 90px">2</button>
                                <button id="number" data-number="3" class="btn btn-primary btn-lg" type="button" style="height: 90px">3</button>
                                <button id="clear-all" class="btn btn-theme02 btn-lg" type="button">Clear All</button>
                                <button id="number" data-number="0" class="btn btn-primary btn-lg" type="button" style="height: 90px">0</button>
                                <button id="clear" class="btn btn-theme02 btn-lg" type="button">Clear</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php

$printerDialog = new PrinterDialog();
$printerDialog->theScript();
echo $printerDialog->renderDialog('pos');

$jscript = '
    var datetime;
    var getDateTime = function() {

        datetime = 0;

        $.when(
            $.ajax({
                async: false,
                type: "GET",
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/datetime']) . '",
                success: function(data) {
                    datetime = data.datetime;
                }
            })
        ).done(function() {
            return datetime;
        });
    };

    var orderId = [];
    var trObj = [];
    var i = 0;
    var setOrderId = function(id, obj) {

        orderId[i] = id;
        trObj[i] = obj;
        i++;
    };

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

';

$jscriptInit = '

    $("#order-menu").find(".discount-type.order").each(function() {

        if ($(this).val() == "Percent") {

        } else if ($(this).val() == "Value") {
            $(this).parent().find("td#subtotal #span-discount #val-discount").currency({' . Yii::$app->params['currencyOptions'] . '});
        }
    });

    $("span#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-void").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});

    $(".dropdown-toggle").dropdown();

    $(".input-payment").removeClass("form-control");
    $("#input-jumlah-tagihan-disp").off("keypress");
    $("#input-jumlah-tagihan-disp").off("keydown");

    var clearInputBayar = function() {

        var value = parseFloat(0);

        $("#input-bayar").val(value);
        $("#input-bayar-disp").val(value);
        $("#input-bayar-disp").maskMoney("mask", value);
    }

    var paymentMethod = function(thisObj, other, kd) {

        var valueBayar = parseFloat($("#input-bayar").val());

        if (valueBayar > 0) {

            var element = $("#temp-payment").children().children().clone();

            var paymentMethodId = element.find(".payment-method-id.payment");
            var jumlahBayar = element.find(".jumlah-bayar.payment");
            var keterangan = element.find(".keterangan.payment");
            var kode = element.find(".kode.payment");

            paymentMethodId.val(thisObj.attr("data-id"));
            jumlahBayar.val(valueBayar);
            kode.val(kd);

            element.find("#payment-method-id").children("span").html(thisObj.html() + other);

            element.find("#payment-value").children("span").html(valueBayar);
            element.find("#payment-value").children("span").currency({' . Yii::$app->params['currencyOptions'] . '});

            if (other != "") {
                if (paymentMethodId.val() == "XLIMIT") {
                    keterangan.val("Kode karyawan = " + kode.val());
                } else if (paymentMethodId.val() == "XVCHR") {
                    keterangan.val("Kode voucher = " + kode.val());
                }
            }

            element.find("#delete").on("click", function() {

                var valueBayar = parseFloat($(this).parent().parent().find(".jumlah-bayar.payment").val());
                var tagihan = parseFloat($("#input-jumlah-tagihan").val()) + valueBayar;

                tagihan = (tagihan >= parseFloat($(".grand-harga.session").val())) ? parseFloat($(".grand-harga.session").val()) : tagihan;

                var kembali = parseFloat($("#jumlah-kembali").val()) - valueBayar;
                kembali = (kembali >= 0) ? kembali : 0;

                $("#jumlah-kembali").val(kembali);
                $("#total-kembali").html(kembali);
                $("#total-kembali").currency({' . Yii::$app->params['currencyOptions'] . '});

                if (kembali <= 0) {

                    $("#input-jumlah-tagihan").val(tagihan);
                    $("#input-jumlah-tagihan-disp").val(tagihan);
                    $("#input-jumlah-tagihan-disp").maskMoney("mask", tagihan);
                }

                $("#jumlah-bayar").val(parseFloat($("#jumlah-bayar").val()) - valueBayar);
                $("#total-bayar").html(parseFloat($("#jumlah-bayar").val()));
                $("#total-bayar").currency({' . Yii::$app->params['currencyOptions'] . '});

                $(this).parent().parent().fadeOut(100, function() {
                    $(this).remove();
                });

                return false;
            });

            element.find("#keterangan").on("click", function() {

                var thisObj = $(this).parent().parent();

                var form = $("#temp-keterangan").clone();

                swal({
                    title: "Keterangan Pembayaran",
                    html:
                        "<div id=\"keterangan-container\">" +
                            form.html() +
                        "</div>",
                    showCancelButton: true,
                    onOpen: function () {

                        $("#keterangan-container").find("form").on("submit", function() {

                            return false;
                        });

                        $("#keterangan-container").find(".keterangan.temp").val(thisObj.find(".keterangan.payment").val());

                        $("#keterangan-container").find(".keterangan.temp").focus();

                        ' . $virtualKeyboard->keyboardQwerty('$("#keterangan-container").find(".keterangan.temp")', true) . '
                    }
                }).then(
                    function(result) {

                        thisObj.find(".keterangan.payment").val($("#keterangan-container").find(".keterangan.temp").val());
                    },
                    function(dismiss) {

                    }
                );

                return false;
            });

            $("#payment").append(element);

            var tagihan = parseFloat($("#input-jumlah-tagihan").val()) - valueBayar;
            tagihan = (tagihan < 0) ? 0 : tagihan;

            $("#input-jumlah-tagihan").val(tagihan);
            $("#input-jumlah-tagihan-disp").val(tagihan);
            $("#input-jumlah-tagihan-disp").maskMoney("mask", tagihan);

            $("#jumlah-bayar").val(parseFloat($("#jumlah-bayar").val()) + valueBayar);
            $("#total-bayar").html(parseFloat($("#jumlah-bayar").val()));
            $("#total-bayar").currency({' . Yii::$app->params['currencyOptions'] . '});

            var kembali = parseFloat($("#jumlah-bayar").val()) - parseFloat($(".grand-harga.session").val());

            if (kembali >= 0) {

                $("#jumlah-kembali").val(kembali);
                $("#total-kembali").html(kembali);
                $("#total-kembali").currency({' . Yii::$app->params['currencyOptions'] . '});
            }

            clearInputBayar();
        }
    };
';

$jscriptAction = '
    $("a#bayar").on("click", function() {

        var thisObj = $(this);

        var kembali = parseFloat($("#jumlah-bayar").val()) - parseFloat($(".grand-harga.session").val());

        var order = null;
        var payment = null;

        if (kembali >= 0) {

            orderId = [];
            trObj = [];
            i = 0;

            $("#order-menu").children("tr#menu-row").each(function() {

                if ($(this).find(".is-void.order").val() == 0) {

                    var row = {};
                    row["menu_id"] = $(this).find(".menu-id.order").val();
                    row["catatan"] = $(this).find(".catatan.order").val();
                    row["jumlah"] = $(this).find(".jumlah.order").val();
                    row["discount_type"] = $(this).find(".discount-type.order").val();
                    row["discount"] = $(this).find(".discount.order").val();
                    row["harga_satuan"] = $(this).find(".harga-satuan.order").val();
                    row["is_free_menu"] = $(this).find(".is-free-menu.order").val();

                    setOrderId(row, $(this));
                }
            });

            order = orderId;

            orderId = [];
            trObj = [];
            i = 0;

            $("#payment").children(".payment-row").each(function() {

                var row = {};
                row["payment_method_id"] = $(this).find(".payment-method-id.payment").val();
                row["jumlah_bayar"] = $(this).find(".jumlah-bayar.payment").val();
                row["keterangan"] = $(this).find(".keterangan.payment").val();
                row["kode"] = $(this).find(".kode.payment").val();

                setOrderId(row, $(this));
            });

            payment = orderId;

            $.ajax({
                cache: false,
                dataType: "json",
                type: "POST",
                url: thisObj.attr("href"),
                data: {
                    "sess_id": $(".sess-id.session").val(),
                    "jumlah_harga": $(".jumlah-harga.session").val(),
                    "discount_type": $(".discount-type.session").val(),
                    "discount": $(".discount.session").val(),
                    "pajak": $(".pajak.session").val(),
                    "service_charge": $(".service-charge.session").val(),
                    "jumlah_bayar": $("#jumlah-bayar").val(),
                    "jumlah_kembali": $("#jumlah-kembali").val(),
                    "order_id": order,
                    "payment": payment
                },
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {

                    if (response.success) {

                        getDateTime();
                        var text = "";
                        var totalQty = 0;
                        var totalSubtotal = 0;

                        text += "\n" + $("#struk-invoice-header").val() + "\n";
                        text += separatorPrint(paperWidth, "-") + "\n";
                        text += "Tgl/Jam Print" + separatorPrint(spaceLength - "Tgl/Jam Print".length) + ": " + datetime + "\n";
                        text += separatorPrint(paperWidth, "-") + "\n";
                        text += "Meja" + separatorPrint(spaceLength - "Meja".length) + ": " + $(".mtable-nama.session").val() + "\n";
                        text += "Tamu" + separatorPrint(spaceLength - "Tamu".length) + ": " + $(".nama-tamu.session").val() + "\n";
                        text += "Tgl/Jam Open" + separatorPrint(spaceLength - "Tgl/Jam Open".length) + ": " + $(".open-table-at.session").val() + "\n";
                        text += "Faktur" + separatorPrint(spaceLength - "Faktur".length) + ": " + response.id + "\n";
                        text += "Kasir" + separatorPrint(spaceLength - "Kasir".length) + ": " + $("#user-active").val() + "\n";

                        text += separatorPrint(paperWidth, "-") + "\n"
                        text += separatorPrint(16) + "Pembayaran \n";
                        text += separatorPrint(paperWidth, "-") + "\n"

                        $("#order-menu").children("tr#menu-row").each(function() {

                            if ($(this).find(".is-void.order").val() == 0) {

                                var discountType = $(this).find(".discount-type.order").val();
                                var discount = parseFloat($(this).find(".discount.order").val());
                                var harga = parseFloat($(this).find(".harga-satuan.order").val());
                                var qty = parseFloat($(this).find(".jumlah.order").val());

                                var menu = $(this).find("#menu").children("span").html().replace("<i class=\"fa fa-plus\" style=\"color:green\"></i>", "(+) ");

                                var textDisc = "";

                                if ($(this).find(".is-free-menu.order").val() == 1) {
                                    textDisc = "Free";
                                } else {
                                    if (discount > 0) {

                                        if (discountType == "Percent") {

                                            harga = harga - Math.round(discount * 0.01 * harga);
                                            textDisc = "Disc: " + discount + "%";
                                        } else if (discountType == "Value") {

                                            harga = harga - discount;

                                            var discSpan = $("<span>").html(discount);
                                            discSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                            textDisc = "Disc: " + discSpan.html();
                                        }
                                    }
                                }

                                var jmlHarga = harga * qty;

                                var hargaSpan = $("<span>").html(harga);
                                hargaSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                                var subtotal = jmlHarga;
                                var subtotalSpan = $("<span>").html(subtotal);
                                subtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                                totalQty += qty;
                                totalSubtotal += subtotal;

                                var line2 = qty + " X " + hargaSpan.html();

                                text += menu + separatorPrint(paperWidth - (menu + textDisc).length) + textDisc + "\n";
                                text += line2 + separatorPrint(paperWidth - (line2 + subtotalSpan.html()).length) + subtotalSpan.html() + "\n";
                            }
                        });

                        text += separatorPrint(paperWidth, "-") + "\n";

                        var totalFreeMenu = parseFloat($("input#total-free-menu").val());
                        var totalFreeMenuSpan = $("<span>").html(totalFreeMenu);
                        totalFreeMenuSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                        text += "Free Menu" + separatorPrint(paperWidth - ("Free Menu" + "(" + totalFreeMenuSpan.html() + ")").length) + "(" + totalFreeMenuSpan.html() + ")" + "\n";                        

                        totalSubtotal -= totalFreeMenu;

                        var totalSubtotalSpan = $("<span>").html(totalSubtotal);
                        totalSubtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                        var scp = hitungServiceChargePajak(totalSubtotal, $(".service-charge.session").val(), $(".pajak.session").val());

                        var scText = "";
                        var serviceCharge = 0;
                        if (parseFloat($(".service-charge.session").val()) > 0) {
                            serviceCharge = scp["serviceCharge"];
                            var serviceChargeSpan = $("<span>").html(serviceCharge);
                            serviceChargeSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                            var sc = "Service Charge (" + $(".service-charge.session").val() + "%)";

                            scText = sc + separatorPrint(paperWidth - (sc + serviceChargeSpan.html()).length) + serviceChargeSpan.html() +"\n";
                        }

                        var pjkText = "";
                        var pajak = 0;
                        if (parseFloat($(".pajak.session").val()) > 0) {
                            pajak = scp["pajak"];
                            var pajakSpan = $("<span>").html(pajak);
                            pajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                            var pjk = "Pajak (" + $(".pajak.session").val() + "%)";

                            pjkText = pjk + separatorPrint(paperWidth - (pjk + pajakSpan.html()).length) + pajakSpan.html() +"\n";
                        }

                        var discBill = hitungDiscBill();
                        var discBillSpan = $("<span>").html(discBill);
                        discBillSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                        discBillSpan.html("(" + discBillSpan.html() + ")");

                        var grandTotal = totalSubtotal + serviceCharge + pajak - hitungDiscBill();
                        var grandTotalSpan = $("<span>").html(grandTotal);
                        grandTotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                        text += separatorPrint(paperWidth, "-") + "\n";

                        text += "Total item" + separatorPrint(paperWidth - ("Total item" + totalQty).length) + totalQty +"\n";
                        text += "Total" + separatorPrint(paperWidth - ("Total" + totalSubtotalSpan.html()).length) + totalSubtotalSpan.html() +"\n";
                        text += scText;
                        text += pjkText;
                        text += "Discount Bill" + separatorPrint(paperWidth - ("Discount Bill" + discBillSpan.html()).length) + discBillSpan.html() +"\n";

                        text += separatorPrint(paperWidth, "-") + "\n";

                        text += "Grand Total" + separatorPrint(paperWidth - ("Grand Total" + grandTotalSpan.html()).length) + grandTotalSpan.html() +"\n";

                        text += separatorPrint(paperWidth, "-") + "\n";

                        $("#payment").find(".payment-row").each(function() {
                            var paymentMethod = $(this).find("#payment-method-id").children("span").html();
                            var keterangan = $(this).find(".keterangan.payment").val();

                            var jumlahBayar = parseFloat($(this).find(".jumlah-bayar.payment").val());
                            var jumlahBayarValue = $("<span>").html(jumlahBayar);
                            jumlahBayarValue.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                            text += paymentMethod + separatorPrint(paperWidth - (paymentMethod + jumlahBayarValue.html()).length) + jumlahBayarValue.html() + "\n";
                            text += keterangan + "\n";
                        });

                        text += separatorPrint(paperWidth, "-") + "\n";
                        text += "Bayar" + separatorPrint(paperWidth - ("Bayar" + $("#total-bayar").html()).length) + $("#total-bayar").html() + "\n";

                        text += "Kembali" + separatorPrint(paperWidth - ("Kembali" + $("#total-kembali").html()).length) + $("#total-kembali").html() + "\n";

                        text += separatorPrint(paperWidth, "-") + "\n";

                        if (response.is_correction) {
                            text += "***Koreksi Invoice***\n";
                        }

                        text += $("textarea#struk-invoice-footer").val() + "\n";

                        var content = [];

                        $("input#printerKasir").each(function() {
                            content[$(this).val()] = text;
                        });

                        printContentToServer("", "", content, false, function() {

                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: response.table,
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
                        });

                    } else {
                        swal("Error", response.message, "error");
                    }

                    $(".overlay").hide();
                    $(".loading-img").hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                    $(".overlay").hide();
                    $(".loading-img").hide();

                    swal("Error", "Terjadi kesalahan dalam proses pembayaran.", "error");
                }
            });
        } else {
            swal("Error", "Lunasi pembayaran terlebih dahulu.", "error");
        }

        return false;
    });
';

$jscriptExe = '
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

    $("button#rp").on("click", function(event) {
        var value = parseFloat($("#input-bayar").val()) + parseFloat($(this).attr("data-rp"));

        $("#input-bayar").val(value);
        $("#input-bayar-disp").val(value);
        $("#input-bayar-disp").maskMoney("mask", value);
    });

    $("button#number").on("click", function(event) {
        var value = $("#input-bayar").val() + $(this).attr("data-number");

        value = parseFloat(value);

        $("#input-bayar").val(value);
        $("#input-bayar-disp").val(value);
        $("#input-bayar-disp").maskMoney("mask", value);
    });

    $("#all").on("click", function(event) {
        var value = parseFloat($("#input-jumlah-tagihan").val());

        $("#input-bayar").val(value);
        $("#input-bayar-disp").val(value);
        $("#input-bayar-disp").maskMoney("mask", value);
    });

    $("#clear").on("click", function(event) {
        var str = $("#input-bayar").val();

        if (str.length > 1) {

            var value = str.substring(0, str.length - 1);
            value = parseFloat(value);
            $("#input-bayar").val(value);
            $("#input-bayar-disp").val(value);
            $("#input-bayar-disp").maskMoney("mask", value);
        } else {
            clearInputBayar();
        }
    });

    $("#clear-all").on("click", function(event) {
        clearInputBayar();
    });

    $("a#payment-method").on("click", function(event) {

        var thisObj = $(this);

        $(".dropdown-toggle").dropdown("toggle");

        var valueBayar = parseFloat($("#input-bayar").val());

        if (thisObj.attr("data-id") == "XLIMIT" || thisObj.attr("data-id") == "XVCHR") {

            var form = $("#temp-limit-karyawan").clone();

            if (thisObj.attr("data-id") == "XLIMIT") {

                var form = $("#temp-limit-karyawan").clone();

                swal({
                    title: "Limit Karyawan",
                    html:
                        "<div id=\"limit-karyawan-container\">" +
                            form.html() +
                        "</div>",
                    showCancelButton: true,
                    onOpen: function () {

                        $("#limit-karyawan-container").find(".kode-karyawan.temp").focus();

                        ' . $virtualKeyboard->keyboardQwerty('$("#limit-karyawan-container").find(".kode-karyawan.temp")', true) . '
                    }
                }).then(
                    function(result) {

                        var kodeKaryawan = $("#limit-karyawan-container").find(".kode-karyawan.temp").val();

                        var jmlLimit = 0;

                        $("#payment").children(".payment-row").each(function() {

                            if ($(this).find(".payment-method-id.payment").val() == "XLIMIT" && $(this).find(".kode.payment").val() == kodeKaryawan) {
                                jmlLimit += parseFloat($(this).find(".jumlah-bayar.payment").val());
                            }
                        });

                        $.ajax({
                            cache: false,
                            dataType: "json",
                            type: "POST",
                            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/limit-karyawan']) . '",
                            data: {
                                "kode_karyawan": kodeKaryawan,
                                "jml_limit" : (valueBayar + jmlLimit)
                            },
                            beforeSend: function(xhr) {
                                $(".overlay").show();
                                $(".loading-img").show();
                            },
                            success: function(response) {

                                if (response.success) {
                                    paymentMethod(thisObj, " (" + kodeKaryawan + ")", kodeKaryawan);
                                } else {
                                    swal("Error", response.message, "error");
                                }

                                $(".overlay").hide();
                                $(".loading-img").hide();
                            },
                            error: function (xhr, ajaxOptions, thrownError) {

                                $(".overlay").hide();
                                $(".loading-img").hide();

                                swal("Error", "Terjadi kesalahan dalam proses verifikasi limit karyawan.", "error");
                            }
                        });
                    },
                    function(dismiss) {

                    }
                );
            } else if (thisObj.attr("data-id") == "XVCHR") {

                var form = $("#temp-voucher").clone();

                swal({
                    title: "Voucher",
                    html:
                        "<div id=\"voucher-container\">" +
                            form.html() +
                        "</div>",
                    showCancelButton: true,
                    onOpen: function () {

                        $("#voucher-container").find(".kode-voucher.temp").focus();

                        ' . $virtualKeyboard->keyboardQwerty('$("#voucher-container").find(".kode-voucher.temp")', true) . '
                    }
                }).then(
                    function(result) {

                        var kodeVoucher = $("#voucher-container").find(".kode-voucher.temp").val();

                        var flag = true;

                        $("#payment").children(".payment-row").each(function() {

                            if ($(this).find(".payment-method-id.payment").val() == "XVCHR" && $(this).find(".kode.payment").val() == kodeVoucher) {
                                flag = false;
                                return false;
                            }
                        });

                        if (flag) {

                            $.ajax({
                                cache: false,
                                dataType: "json",
                                type: "POST",
                                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/voucher']) . '",
                                data: {
                                    "kode_voucher": kodeVoucher,
                                    "tagihan": parseFloat($(".grand-harga.session").val())
                                },
                                beforeSend: function(xhr) {
                                    $(".overlay").show();
                                    $(".loading-img").show();
                                },
                                success: function(response) {

                                    if (response.success) {

                                        $("#input-bayar").val(response.jumlah_voucher);
                                        $("#input-bayar-disp").val(response.jumlah_voucher);
                                        $("#input-bayar-disp").maskMoney("mask", response.jumlah_voucher);

                                        paymentMethod(thisObj, " (" + kodeVoucher + ")", kodeVoucher);
                                    } else {
                                        swal("Error", response.message, "error");
                                    }

                                    $(".overlay").hide();
                                    $(".loading-img").hide();
                                },
                                error: function (xhr, ajaxOptions, thrownError) {

                                    $(".overlay").hide();
                                    $(".loading-img").hide();

                                    swal("Error", "Terjadi kesalahan dalam proses verifikasi voucher.", "error");
                                }
                            });
                        } else {
                            swal("Error", "Kode voucher sudah diinputkan. Kode voucher tidak boleh ada yang sama", "error");
                        }
                    },
                    function(dismiss) {

                    }
                );
            }
        } else {
            paymentMethod(thisObj, "");
        }

        return false;
    });
';

$this->registerJs($jscript . $jscriptInit . $jscriptAction . $jscriptExe); ?>