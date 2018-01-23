<?php
use restotech\standard\backend\components\Tools;

Tools::loadIsIncludeScp();

$totalGrandTotal = 0;

$dataPayment = [];
$paymentJumlahTotal = 0;

foreach ($modelSaleInvoice as $dataSaleInvoice): ?>
    
    <div>
        <div class="row">
            <div class="col-lg-12 fs14">
                No. Invoice: <?= $dataSaleInvoice['id'] ?> &nbsp; &nbsp; &nbsp; Tanggal: <?= Yii::$app->formatter->asDate($dataSaleInvoice['date']) ?>
            </div>           
        </div>

        <div class="row">
            <div class="col-lg-12">
                <table class="table">
                    <thead>
                        <tr style="border:1px solid">
                            <th style="width: 10px">#</th>
                            <th style="width: 300px">Menu Pesanan</th>
                            <th style="width: 150px" class="number">Harga</th>
                            <th style="width: 90px" class="number">Qty</th>                    
                            <th style="width: 150px">Diskon</th>       
                            <th style="width: 150px" class="number">Subtotal</th>                               
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        
                        $discBill = empty($dataSaleInvoice['discount']) ? 0 : $dataSaleInvoice['discount'];
                        $discBillType = $dataSaleInvoice['discount_type'];
                        $discBillText = '';
                        $discBillValue = 0;

                        if ($discBillType == 'Percent') {           
                            
                            $discBillValue = $discBill * 0.01 * $dataSaleInvoice['jumlah_harga']; 
                            $discBillText = '(' . $discBill . '%)';
                        } else if ($discBillType == 'Value') {
                            $discBillValue = $discBill;        
                        }

                        $jumlahTotal = 0;
                        
                        foreach ($dataSaleInvoice['saleInvoiceTrxes'] as $key => $dataSaleInvoiceTrx): 
                            
                            $subtotal = $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];                             

                            $discount = '';
                            if ($dataSaleInvoiceTrx['discount_type'] == 'Percent') {
                                
                                $discount = Tools::convertToCurrency($dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['discount'] / 100, ($print == 'pdf'));
                                $subtotal = $subtotal - ($subtotal * $dataSaleInvoiceTrx['discount'] / 100);
                            } else if ($dataSaleInvoiceTrx['discount_type'] == 'Value') {
                                
                                $discount = Tools::convertToCurrency($dataSaleInvoiceTrx['discount'], ($print == 'pdf'));
                                $subtotal = $subtotal - ($dataSaleInvoiceTrx['discount'] * $dataSaleInvoiceTrx['jumlah']);
                            }                             
                            
                            $jumlahTotal += $subtotal; ?>

                            <tr>
                                <td class="line"><?= $key + 1 ?></td>
                                <td class="line"><?= $dataSaleInvoiceTrx['menu']['nama_menu'] ?></td>                   
                                <td class="line number"><?= Tools::convertToCurrency($dataSaleInvoiceTrx['harga_satuan'], ($print == 'pdf')) ?></td>
                                <td class="line number"><?= $dataSaleInvoiceTrx['jumlah'] ?></td>
                                <td class="line number"><?= $discount ?></td>                    
                                <td class="line number"><?= Tools::convertToCurrency($subtotal, ($print == 'pdf')) ?></td>                                                  
                            </tr>

                        <?php
                        endforeach; 
                        
                        $scp = Tools::hitungServiceChargePajak($jumlahTotal, $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);                                        
                        $serviceCharge = $scp['serviceCharge'];
                        $pajak = $scp['pajak']; 
                        $grandTotal = $jumlahTotal + $serviceCharge + $pajak - $discBillValue; 
                        
                        $totalGrandTotal += $grandTotal; ?>

                    </tbody>
                </table>
            </div>
        </div>
                
        <div class="row">
            <div class="col-lg-12">                
                <table class="table" style="border: none">
                    <tbody style="border: none">
                        <tr>
                            <td style="width: 480px">
                                
                                <table class="table" style="width: 300px">
                                    <tbody>
                                        <tr>
                                            <td style="width: 140px; font-weight: bold; text-decoration: underline">PEMBAYARAN</td>
                                            <td style="width: 10px"></td>       
                                            <td class="number" style="width: 120px"></td>       
                                        </tr>

                                        <?php
                                        foreach ($dataSaleInvoice['saleInvoicePayments'] as $dataSaleInvoicePayment): ?>
                                        
                                            <tr>
                                                <td><?= $dataSaleInvoicePayment['paymentMethod']['nama_payment'] ?></td>
                                                <td>:</td>       
                                                <td class="number"><?= Tools::convertToCurrency($dataSaleInvoicePayment['jumlah_bayar'], ($print == 'pdf')) ?></td>       
                                            </tr>
                                        
                                        <?php
                                        endforeach; ?>
                                            
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
                                            <td>Service Charge <?= '(' . $dataSaleInvoice['service_charge'] . ' %)' ?></td>
                                            <td>:</td>       
                                            <td class="number"><?= Tools::convertToCurrency($serviceCharge, ($print == 'pdf')) ?></td>       
                                        </tr>
                                        <tr>
                                            <td>Pajak <?= '(' . $dataSaleInvoice['pajak'] . ' %)' ?></td>
                                            <td>:</td>       
                                            <td class="number"><?= Tools::convertToCurrency($pajak, ($print == 'pdf')) ?></td>       
                                        </tr>
                                        <tr>
                                            <td>Discount Bill <?= $discBillText ?></td>
                                            <td>:</td>       
                                            <td class="number">(<?= Tools::convertToCurrency($discBillValue, ($print == 'pdf')) ?>)</td>       
                                        </tr>
                                        <tr>
                                            <td>Grand Total</td>
                                            <td>:</td>       
                                            <td class="number"><?= Tools::convertToCurrency($grandTotal, ($print == 'pdf')) ?></td>       
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

    <?php
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

endforeach; ?>

<div class="mb">
    <div class="row">
        <div class="col-lg-12">
            <table class="table" style="border: none">
                <tbody style="border: none">
                    <tr>
                        <td style="width: 480px">                                                                
                            <table class="table" style="width: 300px">
                                <tbody>
                                    <tr>
                                        <td style="width: 140px; font-weight: bold; text-decoration: underline"> TOTAL PEMBAYARAN</td>
                                        <td style="width: 10px"></td>       
                                        <td class="number" style="width: 120px"></td>       
                                    </tr>

                                    <?php
                                    foreach ($dataPayment as $payment): ?>

                                        <tr>
                                            <td style="font-weight: bold"><?= $payment['namaPayment'] ?></td>
                                            <td style="font-weight: bold">:</td>       
                                            <td class="number" style="font-weight: bold"><?= Tools::convertToCurrency($payment['jumlahBayar'], ($print == 'pdf')) ?></td>       
                                        </tr>

                                    <?php
                                    endforeach; ?>
                                        
                                    <tr>
                                        <td style="border-top: 1px solid; font-weight: bold">GRAND TOTAL</td>
                                        <td style="border-top: 1px solid; font-weight: bold">:</td>       
                                        <td class="number" style="border-top: 1px solid; font-weight: bold"><?= Tools::convertToCurrency($paymentJumlahTotal, ($print == 'pdf')) ?></td>       
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                        <td style="width: 370px; padding: 0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold">GRAND TOTAL</td>
                                        <td style="font-weight: bold">:</td>       
                                        <td class="number" style="font-weight: bold"><?= Tools::convertToCurrency($totalGrandTotal, ($print == 'pdf')) ?></td>       
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