<?php
use restotech\standard\backend\components\Tools;

Tools::loadIsIncludeScp();

$dataMenu = [];
$jumlahQty = 0;
$jumlahDiskon = 0;
$jumlahTotal = 0;
$jumlahServiceCharge = 0;
$jumlahPajak = 0;
$jumlahGrandTotal = 0;

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
    
    $jumlahSubtotal = 0;
    
    foreach ($dataSaleInvoice['saleInvoiceTrxes'] as $dataSaleInvoiceTrx) {
        
        $keyMenu = $dataSaleInvoiceTrx['menu']['id'];
        
        $dataMenu[$keyMenu]['nama_menu'] = $dataSaleInvoiceTrx['menu']['nama_menu'];
        
        if (!empty($dataMenu[$keyMenu]['qty'])) {
            $dataMenu[$keyMenu]['qty'] += $dataSaleInvoiceTrx['jumlah'];
        } else {
            $dataMenu[$keyMenu]['qty'] = $dataSaleInvoiceTrx['jumlah'];
        }

        $jumlahQty += $dataSaleInvoiceTrx['jumlah'];        
        
        $subtotal = $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];
        $discount = 0;
        
        if ($dataSaleInvoiceTrx['discount_type'] == 'Percent') {
            
            $discount = ($dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['discount'] / 100) * $dataSaleInvoiceTrx['jumlah'];
            $subtotal = $subtotal - $discount;
        } else if ($dataSaleInvoiceTrx['discount_type'] == 'Value') {
            
            $discount = $dataSaleInvoiceTrx['discount'] * $dataSaleInvoiceTrx['jumlah']; 
            $subtotal = $subtotal - $discount;
        }
        
        if (!empty($dataMenu[$keyMenu]['subtotal'])) {
            $dataMenu[$keyMenu]['subtotal'] += $subtotal;
        } else {
            $dataMenu[$keyMenu]['subtotal'] = $subtotal;
        }

        $jumlahDiskon += $discount;
        $jumlahTotal += $subtotal;
        $jumlahSubtotal += $subtotal;
    }    
    
    $scp = Tools::hitungServiceChargePajak($jumlahSubtotal, $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);                                        
    $serviceCharge = $scp['serviceCharge'];
    $pajak = $scp['pajak']; 
    $grandTotal = $jumlahSubtotal + $serviceCharge + $pajak - $discBillValue;
    
    $jumlahServiceCharge += $serviceCharge;
    $jumlahPajak += $pajak;
    $jumlahGrandTotal += $grandTotal;
} ?>

<div class="mb">    

    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead>
                    <tr style="border:1px solid">
                        <th style="width: 10px">#</th>
                        <th style="width: 370px">Menu Pesanan</th>
                        <th style="width: 90px">Qty</th>
                        <th style="width: 180px" class="number">Subtotal</th>                               
                    </tr>
                </thead>
                <tbody>                   
                    <?php
                    
                    $i = 0;
                    
                    foreach ($dataMenu as $value): 
                        
                        $i++; ?>

                        <tr>
                            <td class="line"><?= $i ?></td>
                            <td class="line"><?= $value['nama_menu'] ?></td>                   
                            <td class="line"><?= $value['qty'] ?></td>                    
                            <td class="line number"><?= Tools::convertToCurrency($value['subtotal'], ($print == 'pdf')) ?></td>                                                  
                        </tr>

                    <?php
                    endforeach; ?>
                        
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">                
            <table class="table" style="border: none">
                <tbody style="border: none">
                    <tr>
                        <td style="width: 480px; padding: 0">

                            <table class="table" style="width: 300px">
                                <tbody>
                                    <tr>
                                        <td style="width: 140px">Total Invoice</td>
                                        <td style="width: 10px">:</td>       
                                        <td class="number" style="width: 120px"><?= count($modelSaleInvoice) ?></td>       
                                    </tr>
                                    <tr>
                                        <td>Total Quantity Item</td>
                                        <td>:</td>       
                                        <td class="number"><?= $jumlahQty ?></td>       
                                    </tr>
                                    <tr>
                                        <td>Total Diskon Item</td>
                                        <td>:</td>       
                                        <td class="number"><?= Tools::convertToCurrency($jumlahDiskon, ($print == 'pdf')) ?></td>       
                                    </tr>
                                </tbody>
                            </table>

                        </td>
                        <td style="width: 370px; padding: 0">

                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td style="width: 170px">Total</td>
                                        <td style="width: 10px">:</td>       
                                        <td class="number" style="width: 150px"><?= Tools::convertToCurrency($jumlahTotal, ($print == 'pdf')) ?></td>       
                                    </tr>                                                                            
                                    <tr>
                                        <td>Total Service Charge</td>
                                        <td>:</td>       
                                        <td class="number"><?= Tools::convertToCurrency($jumlahServiceCharge, ($print == 'pdf')) ?></td>       
                                    </tr>
                                    <tr>
                                        <td>Total Pajak</td>
                                        <td>:</td>       
                                        <td class="number"><?= Tools::convertToCurrency($jumlahPajak, ($print == 'pdf')) ?></td>       
                                    </tr>
                                    <tr>
                                        <td>Total Discount Bill</td>
                                        <td>:</td>       
                                        <td class="number">(<?= Tools::convertToCurrency($jumlahDiscBill, ($print == 'pdf')) ?>)</td>       
                                    </tr>
                                    <tr>
                                        <td>Grand Total</td>
                                        <td>:</td>       
                                        <td class="number"><?= Tools::convertToCurrency($jumlahGrandTotal, ($print == 'pdf')) ?></td>       
                                    </tr>
                                </tbody>
                            </table>

                        </td>       
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    
</div>