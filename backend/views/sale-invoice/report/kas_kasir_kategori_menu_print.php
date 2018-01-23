<?php
use restotech\standard\backend\components\Tools;

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

        $scp = Tools::hitungServiceChargePajak($jumlahSubtotal - ($jumlahSubtotalDiskon + $jumlahSubtotalRefund + $jumlahSubtotalFreeMenu), $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);                                        
        $serviceCharge = $scp['serviceCharge'];
        $pajak = $scp['pajak']; 
        $grandTotal = ($jumlahSubtotal - ($jumlahSubtotalDiskon + $jumlahSubtotalRefund + $jumlahSubtotalFreeMenu)) + $serviceCharge + $pajak - $discBillValue;

        $jumlahKembalian += $dataSaleInvoice['jumlah_kembali'];

        $jumlahServiceCharge += $serviceCharge;
        $jumlahPajak += $pajak;
        $jumlahGrandTotal += $grandTotal;

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

    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <tbody>
                    <tr>
                        <td>Petugas</td>
                        <td><?= Yii::$app->session->get('user_data')['employee']['nama'] ?></td>                      
                    </tr>
                </tbody>
            </table>

            <table class="table">
                <tbody>                                
                    <tr>
                        <th style="width: 300px">Menu</th>
                        <th style="width: 80px">Qty</th>    
                        <th class="number" style="width: 150px">Jumlah Harga</th>    
                    </tr>

                    <?php
                    asort($dataMenu);
                    
                    foreach ($dataMenu as $keyC => $menuCategory): ?>

                        <tr>
                            <td colspan="3" style="font-weight: bold">- <?= $menuCategory['namaCategory'] ?> -</td>                        
                        </tr>

                        <?php
                        asort($menuCategory);
                        
                        foreach ($menuCategory as $key => $menu): 
                            
                            if ($key != 'namaCategory'): ?>

                                <tr>
                                    <td><?= $menu['nama_menu'] ?></td>
                                    <td><?= $menu['qty'] ?></td>
                                    <td class="number"><?= Tools::convertToCurrency($menu['subtotal'], ($print == 'pdf')) ?></td>
                                </tr>

                            <?php
                            endif;
                        endforeach; ?>

                        <tr>
                            <td colspan="3"></td>                        
                        </tr>

                    <?php
                    endforeach; ?> 

                </tbody>
            </table>

            <table class="table">
                <tbody>
                    <tr>
                        <td style="width: 100px">Total Faktur</td>
                        <td class="number" style="width: 200px"><?= $jumlahFaktur ?></td>                      
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>                      
                    </tr>
                    <tr>
                        <td>Total Penjualan (Gross)</td>
                        <td class="number"><?= Tools::convertToCurrency($jumlahTotal, ($print == 'pdf')) ?></td>                      
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>                      
                    </tr>
                    <tr>
                        <td>Total Disc Item</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahDiskon, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <td>Total Free Menu</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahFreeMenu, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>                      
                    </tr>
                    <tr>
                        <td>Total Refund</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahRefund, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>                      
                    </tr>
                    <tr>
                        <td>Total Penjualan (Netto)</td>
                        <td class="number"><?= Tools::convertToCurrency($jumlahTotal - ($jumlahDiskon + $jumlahFreeMenu + $jumlahRefund), ($print == 'pdf')) ?></td>                      
                    </tr>
                    <tr>
                        <td>Total Service Charge</td>
                        <td class="number"><?= Tools::convertToCurrency($jumlahServiceCharge, ($print == 'pdf')) ?></td>                      
                    </tr>
                    <tr>
                        <td>Total Pajak</td>
                        <td class="number"><?= Tools::convertToCurrency($jumlahPajak, ($print == 'pdf')) ?></td>                      
                    </tr>
                    <tr>
                        <td>Total Discount Bill</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahDiscBill, ($print == 'pdf')) ?>)</td>       
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>                      
                    </tr>
                    <tr>
                        <td>GRAND TOTAL</td>
                        <td class="number"><?= Tools::convertToCurrency($jumlahGrandTotal, ($print == 'pdf')) ?></td>                      
                    </tr>
                </tbody>
            </table>

            <table class="table">
                <tbody>
                    <tr>
                        <th style="width: 300px">Payment</th> 
                        <th class="number" style="width: 150px">Jumlah</th>    
                    </tr>

                    <?php
                    asort($dataPayment);
                    
                    $cashPayment = 0;
                    
                    foreach ($dataPayment as $payment): 
                        
                        if ($payment['method'] == 'Cash') {
                                $cashPayment += $payment['jumlahBayar'];
                        } ?>

                        <tr>
                            <td><?= '(' . $payment['count'] . ') ' . $payment['namaPayment'] ?></td>
                            <td class="number"><?= Tools::convertToCurrency($payment['jumlahBayar'], ($print == 'pdf')) ?></td>
                        </tr>

                    <?php
                    endforeach; ?> 

                </tbody>
            </table>

            <table class="table">
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>                      
                    </tr>
                    <tr>
                        <td style="width: 100px">Total Kembalian</td>
                        <td class="number" style="width: 200px">(<?= Tools::convertToCurrency($jumlahKembalian, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>                      
                    </tr>
                    <tr>
                        <td>Total Refund</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahRefund, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <td>Total Service Charge</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahRefundServiceCharge, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <td>Total Pajak</td>
                        <td class="number">(<?= Tools::convertToCurrency($jumlahRefundPajak, ($print == 'pdf')) ?>)</td>                      
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>                      
                    </tr>
                    <tr>
                        <td>SALDO AWAL</td>
                        <td class="number"><?= Tools::convertToCurrency($saldoKasirAwal, ($print == 'pdf')) ?></td>                      
                    <tr>
                        <th></th>
                        <th></th>                      
                    </tr>
                    <tr>
                        <td>KAS KASIR</td>
                        <td class="number"><?= Tools::convertToCurrency(($cashPayment - ($jumlahKembalian + $jumlahRefund + $jumlahRefundServiceCharge + $jumlahRefundPajak)) + $saldoKasirAwal, ($print == 'pdf')) ?></td>                      
                    </tr>
                </tbody>
            </table>
        </div>
    </div>   

<?php
else:
    echo 'Tidak ada data';
endif; ?>