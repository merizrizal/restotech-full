<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use restotech\standard\backend\components\Tools;
use restotech\standard\backend\components\PrinterDialog;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SaleInvoice */

yii\widgets\MaskedInputAsset::register($this);

$this->title = 'Laporan Kas Kasir';
$this->params['breadcrumbs'][] = $this->title; ?>

<?= Html::beginForm() ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="sale-invoice-form">              
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="control-label" for="jenis">Jenis</label>
                                </div>
                                <div class="col-lg-6">
                                    <?= Html::radioList('jenis', 'Kategori-Menu',                                            
                                        [
                                            'Kategori-Menu' => 'By Kategori Menu',
                                            'Faktur' => 'By Faktur',
                                        ],
                                        [
                                            'separator' => '<br>',
                                        ]) ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="control-label" for="tanggal">Tanggal</label>
                                </div>
                                <div class="col-lg-4">
                                    <?= DatePicker::widget([
                                        'id' => 'tanggal',
                                        'name' => 'tanggal',
                                        'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                    ]); ?>                                 
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?= Html::submitButton('<i class="fa fa-print"></i> Print', ['name' => 'print', 'value' => 'print', 'class' => 'btn btn-primary']) ?>
                                    &nbsp;&nbsp;
                                    <?= Html::submitButton('<i class="fa fa-file-pdf-o"></i> PDF', ['name' => 'print', 'value' => 'pdf', 'class' => 'btn btn-primary']) ?>
                                    &nbsp;&nbsp;
                                    <?= Html::submitButton('<i class="fa fa-file-excel-o"></i> Excel', ['name' => 'print', 'value' => 'excel', 'class' => 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->

<?= Html::endForm() ?>

<?php

if (!empty($modelSaleInvoice)):
    
    Tools::loadIsIncludeScp();    

    $dataMenu = [];
    $jumlahFaktur = 0;
    $jumlahDiskon = 0;
    $jumlahTotal = 0;
    $jumlahServiceCharge = 0;
    $jumlahPajak = 0;
    $jumlahRefund = 0;
    $jumlahRefundServiceCharge = 0;
    $jumlahRefundPajak = 0;
    $jumlahVoid = 0;
    $jumlahFreeMenu = 0;
    $jumlahGrandTotal = 0;
    $jumlahKembalian = 0;

    $dataPayment = [];
    $paymentJumlahTotal = 0;
    
    $jumlahDiscBill = 0;

    foreach ($modelSaleInvoice as $dataSaleInvoice) {
        
        $discBill = empty($dataSaleInvoice['discount']) ? 0 : $dataSaleInvoice['discount'];
        $discBillType = $dataSaleInvoice['discount_type'];
        $discBillValue = 0;

        if ($discBillType == 'Percent') {                                                    
            $discBillValue = $discBill * 0.01 * $dataSaleInvoice['jumlah_harga']; 
        } else if ($discBillType == 'Value') {
            $discBillValue = $discBill;        
        }

        $jumlahDiscBill += $discBillValue;

        $jumlahFaktur ++;
        $jumlahSubtotal = 0;
        $jumlahSubtotalDiskon = 0;
        $jumlahSubtotalRefund = 0;
        $jumlahSubtotalRefundServiceCharge = 0;
        $jumlahSubtotalRefundPajak = 0;
        $jumlahSubtotalVoid = 0;
        $jumlahSubtotalFreeMenu = 0;

        foreach ($dataSaleInvoice['saleInvoiceTrxes'] as $dataSaleInvoiceTrx) {
            
            $keyMenu = $dataSaleInvoiceTrx['menu']['menuCategory']['id'];
            $keyMenu2 = $dataSaleInvoiceTrx['menu']['id'];

            $dataMenu[$keyMenu]['namaCategory'] = $dataSaleInvoiceTrx['menu']['menuCategory']['nama_category'];

            $dataMenu[$keyMenu][$keyMenu2]['nama_menu'] = $dataSaleInvoiceTrx['menu']['nama_menu'];

            if (!empty($dataMenu[$keyMenu][$keyMenu2]['qty'])) {
                $dataMenu[$keyMenu][$keyMenu2]['qty'] += $dataSaleInvoiceTrx['jumlah'];
            } else {
                $dataMenu[$keyMenu][$keyMenu2]['qty'] = $dataSaleInvoiceTrx['jumlah'];
            }


            $subtotal = $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];
            $discount = 0;
            
            if ($dataSaleInvoiceTrx['discount_type'] == 'Percent') {
                $discount = ($dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['discount'] / 100) * $dataSaleInvoiceTrx['jumlah'];
            } else if ($dataSaleInvoiceTrx['discount_type'] == 'Value') {
                $discount = $dataSaleInvoiceTrx['discount'] * $dataSaleInvoiceTrx['jumlah']; 
            }

            if ($dataSaleInvoiceTrx['is_free_menu']) {
                
                $jumlahSubtotalFreeMenu += $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];
                $jumlahFreeMenu += $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];
            }

            if (!empty($dataMenu[$keyMenu][$keyMenu2]['subtotal'])) {
                $dataMenu[$keyMenu][$keyMenu2]['subtotal'] += $subtotal;
            } else {
                $dataMenu[$keyMenu][$keyMenu2]['subtotal'] = $subtotal;
            }


            $subtotalRefund = 0;
            
            foreach ($dataSaleInvoiceTrx['saleInvoiceReturs'] as $saleInvoiceRetur) {
                
                $refund = $saleInvoiceRetur['harga'] * $saleInvoiceRetur['jumlah'];
                
                if ($saleInvoiceRetur['discount_type'] == 'Percent') {
                    $refund = $refund - ($refund * $saleInvoiceRetur['discount'] / 100);
                } else if ($saleInvoiceRetur['discount_type'] == 'Value') {
                    $refund = $refund - ($saleInvoiceRetur['discount'] * $saleInvoiceRetur['jumlah']);
                }
                
                $subtotalRefund += $refund;
            }

            $scp = Tools::hitungServiceChargePajak($subtotalRefund, $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);              

            $jumlahRefund += $subtotalRefund;
            $jumlahRefundServiceCharge += $scp['serviceCharge'];
            $jumlahRefundPajak += $scp['pajak'];

            $jumlahSubtotalRefund += $subtotalRefund;
            $jumlahSubtotalRefundServiceCharge += $scp['serviceCharge'];
            $jumlahSubtotalRefundPajak += $scp['pajak'];

            $jumlahDiskon += $discount;
            $jumlahSubtotalDiskon += $discount;
            $jumlahTotal += $subtotal;
            $jumlahSubtotal += $subtotal;                        
        }            

        $scp = Tools::hitungServiceChargePajak($jumlahSubtotal - ($jumlahSubtotalDiskon + $jumlahSubtotalRefund + $jumlahSubtotalVoid + $jumlahSubtotalFreeMenu), $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);                                        
        $serviceCharge = $scp['serviceCharge'];
        $pajak = $scp['pajak']; 
        $grandTotal = ($jumlahSubtotal - ($jumlahSubtotalDiskon + $jumlahSubtotalRefund + $jumlahSubtotalVoid + $jumlahSubtotalFreeMenu)) + $serviceCharge + $pajak - $discBillValue;

        $jumlahKembalian += $dataSaleInvoice['jumlah_kembali'];

        $jumlahServiceCharge += $serviceCharge;
        $jumlahPajak += $pajak;
        $jumlahGrandTotal += $grandTotal;
        
        $keyInvoice = $dataSaleInvoice['id'];
        $dataInvoice[$keyInvoice]['invoiceId'] = $dataSaleInvoice['id'];
        $dataInvoice[$keyInvoice]['jumlah'] = $jumlahSubtotal;
        $dataInvoice[$keyInvoice]['jam'] = Yii::$app->formatter->asDatetime($dataSaleInvoice['date'], 'yyyy-MM-dd / HH:mm');

        foreach ($dataSaleInvoice['saleInvoicePayments'] as $dataPaymentMethod) {
            
            $keyMenu = $dataPaymentMethod['paymentMethod']['id'];

            $dataPayment[$keyMenu]['namaPayment'] = $dataPaymentMethod['paymentMethod']['nama_payment'];
            $dataPayment[$keyMenu]['method'] = $dataPaymentMethod['paymentMethod']['method'];

            if (!empty($dataPayment[$keyMenu]['jumlahBayar'])) {
                $dataPayment[$keyMenu]['jumlahBayar'] += $dataPaymentMethod['jumlah_bayar'];
            } else {
                $dataPayment[$keyMenu]['jumlahBayar'] = $dataPaymentMethod['jumlah_bayar'];
            }

            if (!empty($dataPayment[$keyMenu]['count'])) {
                $dataPayment[$keyMenu]['count'] += 1;
            } else {
                $dataPayment[$keyMenu]['count'] = 1;
            }

            $paymentJumlahTotal += $dataPaymentMethod['jumlah_bayar'];            
        }
    } 

    $saldoKasirAwal = !empty($modelSaldoKasir['saldo_awal']) ? $modelSaldoKasir['saldo_awal'] : 0; ?>

    <?= Html::hiddenInput('tanggalTransaksi', Yii::$app->formatter->asDate($tanggal), ['id' => 'tanggalTransaksi']) ?>
    <?= Html::hiddenInput('jumlahFaktur', $jumlahFaktur, ['id' => 'jumlahFaktur']) ?>
    <?= Html::hiddenInput('jumlahTotal', $jumlahTotal, ['id' => 'jumlahTotal']) ?>
    <?= Html::hiddenInput('jumlahDiskon', $jumlahDiskon, ['id' => 'jumlahDiskon']) ?>
    <?= Html::hiddenInput('jumlahServiceCharge', $jumlahServiceCharge, ['id' => 'jumlahServiceCharge']) ?>
    <?= Html::hiddenInput('jumlahPajak', $jumlahPajak, ['id' => 'jumlahPajak']) ?>
    <?= Html::hiddenInput('jumlahRefund', $jumlahRefund, ['id' => 'jumlahRefund']) ?>
    <?= Html::hiddenInput('jumlahRefundServiceCharge', $jumlahRefundServiceCharge, ['id' => 'jumlahRefundServiceCharge']) ?>
    <?= Html::hiddenInput('jumlahRefundPajak', $jumlahRefundPajak, ['id' => 'jumlahRefundPajak']) ?>
    <?= Html::hiddenInput('jumlahVoid', $jumlahVoid, ['id' => 'jumlahVoid']) ?>
    <?= Html::hiddenInput('jumlahFreeMenu', $jumlahFreeMenu, ['id' => 'jumlahFreeMenu']) ?>
    <?= Html::hiddenInput('jumlahDiscBill', $jumlahDiscBill, ['id' => 'jumlahDiscBill']) ?>
    <?= Html::hiddenInput('jumlahGrandTotal', $jumlahGrandTotal, ['id' => 'jumlahGrandTotal']) ?>    

    <?= Html::hiddenInput('paymentJumlahTotal', $paymentJumlahTotal, ['id' => 'paymentJumlahTotal']) ?>

    <?= Html::hiddenInput('jumlahKembalian', $jumlahKembalian, ['id' => 'jumlahKembalian']) ?>

    <?= Html::hiddenInput('saldoKasirAwal', $saldoKasirAwal, ['id' => 'saldoKasirAwal']) ?>

    <?php
    asort($dataMenu);
    foreach ($dataMenu as $menuCategory): ?>

        <div class="rowCategoryMenu" style="display: none">
            <?= Html::hiddenInput('namaCategory', $menuCategory['namaCategory'], ['id' => 'namaCategory']) ?>
            
            <?php
            asort($menuCategory);
            foreach ($menuCategory as $key => $menu): 
                if ($key != 'namaCategory'): ?>           

                    <div class="rowMenu" style="display: none">
                        <?= Html::hiddenInput('namaMenu', $menu['nama_menu'], ['id' => 'namaMenu']) ?>
                        <?= Html::hiddenInput('qty', $menu['qty'], ['id' => 'qty']) ?>
                        <?= Html::hiddenInput('subtotal', $menu['subtotal'], ['id' => 'subtotal']) ?>
                    </div>

                <?php
                endif;
            endforeach; ?>
         
        </div>

    <?php
    endforeach; 
    
    asort($dataInvoice);
    foreach ($dataInvoice as $invoice): ?>

        <div class="rowFaktur" style="display: none">
            
            <?= Html::hiddenInput('invoiceId', $invoice['invoiceId'], ['id' => 'invoiceId']) ?>
            <?= Html::hiddenInput('jam', $invoice['jam'], ['id' => 'jam']) ?>
            <?= Html::hiddenInput('subtotal', $invoice['jumlah'], ['id' => 'subtotal']) ?>
         
        </div>

    <?php
    endforeach; 
    
    asort($dataPayment);
    foreach ($dataPayment as $payment): ?>

        <div class="rowPayment" style="display: none">
            <?= Html::hiddenInput('namaPayment', $payment['namaPayment'], ['id' => 'namaPayment']) ?>
            <?= Html::hiddenInput('jumlahBayar', $payment['jumlahBayar'], ['id' => 'jumlahBayar']) ?>
            <?= Html::hiddenInput('method', $payment['method'], ['id' => 'method']) ?>
            <?= Html::hiddenInput('count', $payment['count'], ['id' => 'count']) ?>
        </div>

    <?php
    endforeach;
endif; ?>    

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/jquery-currency/jquery.currency.js', ['depends' => 'yii\web\YiiAsset']); 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#tanggal").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
';

if (!empty($modelSaleInvoice)) {
    
    if ($print == 'print') {
        
        $printerDialog = new PrinterDialog();
        $printerDialog->theScript();
        echo $printerDialog->renderDialog('backend');

        $jscript .= '        

            var print = function() {            
                var text = "";

                text += "\n" + separatorPrint(14) + "KAS KASIR\n\n";
                text += separatorPrint(40, "-") + "\n";
                text += "Tanggal" + separatorPrint(14 - "Tanggal".length) + ": " + $("input#tanggalTransaksi").val() + "\n";
                text += "Petugas" + separatorPrint(14 - "Petugas".length) + ": ' . Yii::$app->session->get('user_data')['employee']['nama'] . '" + "\n";
                text += separatorPrint(40, "-") + "\n";';
        
            if ($jenis == 'Kategori-Menu') {
                
                $jscript .= '
            

                $("div.rowCategoryMenu").each(function() {
                    text += "- " + $(this).find("input#namaCategory").val() + " -\n";
                    
                    $(this).find("div.rowMenu").each(function() {
                        var menu = $(this).find("input#namaMenu").val();
                        var qty = $(this).find("input#qty").val();

                        var subtotal = $(this).find("input#subtotal").val();                    
                        var subtotalSpan = $("<span>").html(subtotal);
                        subtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                        var separatorLength = 40 - (qty.length + subtotalSpan.html().length);                                        

                        text += menu + "\n";
                        text += qty + separatorPrint(separatorLength) + subtotalSpan.html() + "\n";
                    });
                    
                    text += "\n";
                });';
            } else if ($jenis == 'Faktur') {
                
                $jscript .= '
            

                $("div.rowFaktur").each(function() {

                    var invoiceId = $(this).find("input#invoiceId").val();
                    var jam = $(this).find("input#jam").val();

                    var subtotal = $(this).find("input#subtotal").val();                    
                    var subtotalSpan = $("<span>").html(subtotal);
                    subtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                    var separatorLength = 40 - (jam.length + subtotalSpan.html().length);                                        

                    text += invoiceId + "\n";
                    text += jam + separatorPrint(separatorLength) + subtotalSpan.html() + "\n";
                    
                    text += "\n";
                });';
            }
            
            $jscript .= ' 
                text += separatorPrint(40, "-") + "\n";

                text += "Total Faktur" + separatorPrint(40 - ("Total Faktur" + $("input#jumlahFaktur").val()).length) + $("input#jumlahFaktur").val() + "\n";           

                var jumlahSubtotal = $("input#jumlahTotal").val();                    
                var jumlahSubtotalSpan = $("<span>").html(jumlahSubtotal);
                jumlahSubtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Penjualan (Gross)" + separatorPrint(40 - ("Total Penjualan (Gross)" + jumlahSubtotalSpan.html()).length) + jumlahSubtotalSpan.html() + "\n";

                text += separatorPrint(40, "-") + "\n";

                var jumlahDisc = parseFloat($("input#jumlahDiskon").val());                    
                var jumlahDiscSpan = $("<span>").html(jumlahDisc);
                jumlahDiscSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Disc Item" + separatorPrint(40 - ("Total Disc Item" + "(" + jumlahDiscSpan.html() + ")").length) + "(" + jumlahDiscSpan.html() + ")\n";                       

                var jumlahFreeMenu = parseFloat($("input#jumlahFreeMenu").val());                    
                var jumlahFreeMenuSpan = $("<span>").html(jumlahFreeMenu);
                jumlahFreeMenuSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Free Menu" + separatorPrint(40 - ("Total Free Menu" + "(" + jumlahFreeMenuSpan.html() + ")").length) + "(" + jumlahFreeMenuSpan.html() + ")\n";

                text += separatorPrint(40, "-") + "\n";

                var jumlahRefund = parseFloat($("input#jumlahRefund").val());                    
                var jumlahRefundSpan = $("<span>").html(jumlahRefund);
                jumlahRefundSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Refund" + separatorPrint(40 - ("Total Refund" + "(" + jumlahRefundSpan.html() + ")").length) + "(" + jumlahRefundSpan.html() + ")\n";

                var jumlahVoid = parseFloat($("input#jumlahVoid").val());                    
                var jumlahVoidSpan = $("<span>").html(jumlahVoid);
                jumlahVoidSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Void" + separatorPrint(40 - ("Total Void" + "(" + jumlahVoidSpan.html() + ")").length) + "(" + jumlahVoidSpan.html() + ")\n";

                text += separatorPrint(40, "-") + "\n";

                var jumlahSubtotal = parseFloat($("input#jumlahTotal").val()) - (jumlahDisc + jumlahRefund + jumlahFreeMenu + jumlahVoid) ;                    
                var jumlahSubtotalSpan = $("<span>").html(jumlahSubtotal);
                jumlahSubtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Penjualan (Netto)" + separatorPrint(40 - ("Total Penjualan (Netto)" + jumlahSubtotalSpan.html()).length) + jumlahSubtotalSpan.html() + "\n";                                

                var jumlahSc = $("input#jumlahServiceCharge").val();                    
                var jumlahScSpan = $("<span>").html(jumlahSc);
                jumlahScSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Service Charge" + separatorPrint(40 - ("Total Service Charge" + jumlahScSpan.html()).length) + jumlahScSpan.html() + "\n";

                var jumlahPajak = $("input#jumlahPajak").val();                    
                var jumlahPajakSpan = $("<span>").html(jumlahPajak);
                jumlahPajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Pajak" + separatorPrint(40 - ("Total Pajak" + jumlahPajakSpan.html()).length) + jumlahPajakSpan.html() + "\n";     
                
                var discBill = $("input#jumlahDiscBill").val(); 
                var discBillSpan = $("<span>").html(discBill);
                discBillSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                discBillSpan.html("(" + discBillSpan.html() + ")");
                text += "Total Discount Bill" + separatorPrint(40 - ("Total Discount Bill" + discBillSpan.html()).length) + discBillSpan.html() +"\n";

                text += separatorPrint(40, "-") + "\n";                        

                var jumlahGrandTotal = parseFloat($("input#jumlahGrandTotal").val());              
                var jumlahGrandTotalSpan = $("<span>").html(jumlahGrandTotal);
                jumlahGrandTotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "GRAND TOTAL" + separatorPrint(40 - ("GRAND TOTAL" + jumlahGrandTotalSpan.html()).length) + jumlahGrandTotalSpan.html() + "\n";   

                text += separatorPrint(40, "-") + "\n";

                var cashPayment = 0;

                $("div.rowPayment").each(function() {                

                    var namaPayment = $(this).find("input#namaPayment").val();

                    var jumlahBayar = $(this).find("input#jumlahBayar").val();                          
                    var jumlahBayarSpan = $("<span>").html(jumlahBayar);
                    jumlahBayarSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                    var count = $(this).find("input#count").val();

                    var separatorLength = 40 - (qty.length + jumlahBayarSpan.html().length);                                   

                    text += "(" + count + ") " + namaPayment + separatorPrint(40 - ("(" + count + ") " + namaPayment + jumlahBayarSpan.html()).length) + jumlahBayarSpan.html() + "\n";

                    if ($(this).find("input#method").val() == "Cash") {
                        cashPayment += parseFloat(jumlahBayar);
                    }
                });        

                text += separatorPrint(40, "-") + "\n";

                var jumlahKembalian = parseFloat($("input#jumlahKembalian").val());                    
                var jumlahKembalianSpan = $("<span>").html(jumlahKembalian);
                jumlahKembalianSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Kembalian" + separatorPrint(40 - ("Total Kembalian" + "(" + jumlahKembalianSpan.html() + ")").length) + "(" + jumlahKembalianSpan.html() + ")\n";

                text += separatorPrint(40, "-") + "\n";

                text += "Total Refund" + separatorPrint(40 - ("Total Refund" + "(" + jumlahRefundSpan.html() + ")").length) + "(" + jumlahRefundSpan.html() + ")\n";

                var jumlahRefundSc = parseFloat($("input#jumlahRefundServiceCharge").val());                    
                var jumlahRefundScSpan = $("<span>").html(jumlahRefundSc);
                jumlahRefundScSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Service Charge" + separatorPrint(40 - ("Total Service Charge" + "(" + jumlahRefundScSpan.html() + ")").length) + "(" + jumlahRefundScSpan.html() + ")\n";

                var jumlahRefundPajak = parseFloat($("input#jumlahRefundPajak").val());                    
                var jumlahRefundPajakSpan = $("<span>").html(jumlahRefundPajak);
                jumlahRefundPajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Total Pajak" + separatorPrint(40 - ("Total Pajak" + "(" + jumlahRefundPajakSpan.html() + ")").length) + "(" + jumlahRefundPajakSpan.html() + ")\n";

                text += separatorPrint(40, "-") + "\n";

                var jumlahSaldoAwal = parseFloat($("input#saldoKasirAwal").val());                    
                var jumlahSaldoAwalSpan = $("<span>").html(jumlahSaldoAwal);
                jumlahSaldoAwalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "SALDO AWAL" + separatorPrint(40 - ("SALDO AWAL" + jumlahSaldoAwalSpan.html()).length) + jumlahSaldoAwalSpan.html() + "\n";

                text += separatorPrint(40, "-") + "\n";

                var jumlahCashOnHand = (cashPayment - (jumlahKembalian + jumlahRefund + jumlahRefundSc + jumlahRefundPajak)) + jumlahSaldoAwal;                    
                var jumlahCashOnHandSpan = $("<span>").html(jumlahCashOnHand);
                jumlahCashOnHandSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                text += "Kas Kasir" + separatorPrint(40 - ("CASH ON HAND" + jumlahCashOnHandSpan.html()).length) + jumlahCashOnHandSpan.html() + "\n";

                var content = [];

                $("input#printerKasir").each(function() {
                    content[$(this).val()] = text;
                });                
                
                printContentToServer("", "\n\n\n\n\n\n\n\n\n\n\n\n", content);
            };

            print();
        ';   
    }
} else {
    if (!empty(Yii::$app->request->post())) {
        
        $notif = new NotificationDialog([
            'status' => 'danger',
            'message1' => 'Alert',
            'message2' => 'Tidak ada data.',
        ]);

        $notif->theScript();
        echo $notif->renderDialog();
    }
}

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>