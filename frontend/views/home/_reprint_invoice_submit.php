<?php
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
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
                    
                    <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
                        <div class="white-panel pn" style="height: auto; color: #000">
                            <div class="white-header" style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-6 goleft">
                                        <a id="reprint" class="btn btn-success" href=""><i class="fa fa-print" style="font-size: 12px; color: white"></i> Reprint</a>
                                    </div>
                                    <div class="col-md-6 goright">
                                        <a id="back" class="btn btn-danger" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/reprint-invoice']) ?>"><i class="fa fa-undo" style="font-size: 12px; color: white"></i> Back</a>
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

                                    <?= Html::hiddenInput('jumlah_bayar', $modelSaleInvoice->jumlah_bayar, ['id' => 'jumlah-bayar']) ?>
                                    <?= Html::hiddenInput('jumlah_kembali', $modelSaleInvoice->jumlah_kembali, ['id' => 'jumlah-kembali']) ?>

                                    <thead>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <th class="goleft">                                    
                                                Total Bayar
                                            </th>
                                            <th id="total-bayar" class="goright" style="width: 45%"><?= $modelSaleInvoice->jumlah_bayar ?></th>
                                        </tr>                                    
                                    </thead>
                                    <tbody id="payment">
                                        
                                        <?php
                                        foreach ($modelSaleInvoice->saleInvoicePayments as $saleInvoicePayment): 
                                            
                                            $payment = $saleInvoicePayment->paymentMethod->nama_payment ?>
                                        
                                            <tr class="payment-row">

                                                <?= Html::hiddenInput('jumlah_bayar', $saleInvoicePayment->jumlah_bayar, ['class' => 'jumlah-bayar payment']) ?>
                                                <?= Html::hiddenInput('keterangan', $saleInvoicePayment->keterangan, ['class' => 'keterangan payment']) ?>

                                                <td id="payment-method-id" class="goleft"><?= $payment ?></td>
                                                <td id="payment-value" class="goright"><?= $saleInvoicePayment->jumlah_bayar ?></td>
                                            </tr>
                                            
                                        <?php
                                        endforeach; ?>
                                            
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td class="goleft">                                    
                                                Kembali
                                            </td>
                                            <td id="total-kembali" class="goright"><?= $modelSaleInvoice->jumlah_kembali ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="white-header" style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-6 goleft">
                                        <a id="reprint" class="btn btn-success" href=""><i class="fa fa-print" style="font-size: 12px; color: white"></i> Reprint</a>
                                    </div>
                                    <div class="col-md-6 goright">
                                        <a id="back" class="btn btn-danger" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/reprint-invoice']) ?>"><i class="fa fa-undo" style="font-size: 12px; color: white"></i> Back</a>
                                    </div>
                                </div>                                
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
    $("#total-bayar").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-kembali").currency({' . Yii::$app->params['currencyOptions'] . '});
    
    $("#payment").find("td#payment-value").each(function() {
    
        $(this).currency({' . Yii::$app->params['currencyOptions'] . '});  
    });
';

$jscriptAction = '
    $("a#reprint").on("click", function() {
                    
        getDateTime();
        var text = "";
        var totalQty = 0;
        var totalSubtotal = 0;

        text += "\n" + $("#struk-invoice-header").val() + "\n";
        text += separatorPrint(40, "-") + "\n";
        text += "Tgl/Jam Print" + separatorPrint(14 - "Tgl/Jam Print".length) + ": " + datetime + "\n";
        text += separatorPrint(40, "-") + "\n";
        text += "Meja" + separatorPrint(14 - "Meja".length) + ": " + $(".mtable-nama.session").val() + "\n";
        text += "Tgl/Jam Open" + separatorPrint(14 - "Tgl/Jam Open".length) + ": " + $(".open-table-at.session").val() + "\n";
        text += "Kasir" + separatorPrint(14 - "Kasir".length) + ": " + $("#user-active").val() + "\n";

        text += separatorPrint(40, "-") + "\n"
        text += separatorPrint(10) + "Reprint Pembayaran \n";                        
        text += separatorPrint(40, "-") + "\n"                        

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

                text += menu + separatorPrint(40 - (menu + textDisc).length) + textDisc + "\n";                    
                text += line2 + separatorPrint(40 - (line2 + subtotalSpan.html()).length) + subtotalSpan.html() + "\n";
            }
        });
                
        text += separatorPrint(40, "-") + "\n";

        var totalFreeMenu = parseFloat($("input#total-free-menu").val());
        var totalFreeMenuSpan = $("<span>").html(totalFreeMenu);
        totalFreeMenuSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
        text += "Free Menu" + separatorPrint(40 - ("Free Menu" + "(" + totalFreeMenuSpan.html() + ")").length) + "(" + totalFreeMenuSpan.html() + ")" + "\n";

        var totalVoid = parseFloat($("input#total-void").val());
        var totalVoidSpan = $("<span>").html(totalVoid);
        totalVoidSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
        text += "Void Menu" + separatorPrint(40 - ("Void Menu" + "(" + totalVoidSpan.html() + ")").length) + "(" + totalVoidSpan.html() + ")" + "\n";

        totalSubtotal -= (totalFreeMenu + totalVoid);

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

            scText = sc + separatorPrint(40 - (sc + serviceChargeSpan.html()).length) + serviceChargeSpan.html() +"\n";
        }

        var pjkText = "";
        var pajak = 0;
        if (parseFloat($(".pajak.session").val()) > 0) {
            pajak = scp["pajak"];
            var pajakSpan = $("<span>").html(pajak);
            pajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
            var pjk = "Pajak (" + $(".pajak.session").val() + "%)";

            pjkText = pjk + separatorPrint(40 - (pjk + pajakSpan.html()).length) + pajakSpan.html() +"\n";
        }

        var discBill = hitungDiscBill();
        var discBillSpan = $("<span>").html(discBill);
        discBillSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
        discBillSpan.html("(" + discBillSpan.html() + ")");

        var grandTotal = totalSubtotal + serviceCharge + pajak - hitungDiscBill();
        var grandTotalSpan = $("<span>").html(grandTotal);
        grandTotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});                        

        text += separatorPrint(40, "-") + "\n";   

        text += "Total item" + separatorPrint(40 - ("Total item" + totalQty).length) + totalQty +"\n";
        text += "Total" + separatorPrint(40 - ("Total" + totalSubtotalSpan.html()).length) + totalSubtotalSpan.html() +"\n";
        text += scText;
        text += pjkText;
        text += "Discount Bill" + separatorPrint(40 - ("Discount Bill" + discBillSpan.html()).length) + discBillSpan.html() +"\n";

        text += separatorPrint(40, "-") + "\n"; 

        text += "Grand Total" + separatorPrint(40 - ("Grand Total" + grandTotalSpan.html()).length) + grandTotalSpan.html() +"\n";

        text += separatorPrint(40, "-") + "\n";

        $("#payment").find(".payment-row").each(function() {
        
            var paymentMethod = $(this).find("#payment-method-id").html();
            var keterangan = $(this).find(".keterangan.payment").val();

            var jumlahBayar = parseFloat($(this).find(".jumlah-bayar.payment").val());
            var jumlahBayarValue = $("<span>").html(jumlahBayar);
            jumlahBayarValue.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});   

            text += paymentMethod + separatorPrint(40 - (paymentMethod + jumlahBayarValue.html()).length) + jumlahBayarValue.html() + "\n";
            text += keterangan + "\n";
        });

        text += separatorPrint(40, "-") + "\n";     
        text += "Bayar" + separatorPrint(40 - ("Bayar" + $("#total-bayar").html()).length) + $("#total-bayar").html() + "\n";

        text += "Kembali" + separatorPrint(40 - ("Kembali" + $("#total-kembali").html()).length) + $("#total-kembali").html() + "\n";

        text += separatorPrint(40, "-") + "\n";
        
        text += "***Reprint***\n";

        text += $("textarea#struk-invoice-footer").val() + "\n";                    

        var content = [];

        $("input#printerKasir").each(function() {
            content[$(this).val()] = text;
        });

        printContentToServer("", "", content, false, function() {

            $.ajax({
                cache: false,
                type: "POST",
                url: $("#back").attr("href"),
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
';

$this->registerJs($jscript . $jscriptInit . $jscriptAction . $jscriptExe); ?>