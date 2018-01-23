<?php
use restotech\standard\backend\components\Tools; ?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 100px">No. Faktur</th>
                    <th style="width: 50px">Tanggal</th>
                    <th style="width: 200px">Atas Nama</th>
                    <th style="width: 200px">Keterangan</th>
                    <th style="width: 180px" class="number">Jumlah Piutang</th>
                    <th style="width: 180px" class="number">Dibayar</th>
                    <th style="width: 180px" class="number">Sisa</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $totalPiutang = 0;
                $totalBayar = 0;
                $totalSisa = 0;
                
                foreach ($modelSaleInvoicePayment as $dataSaleInvoicePayment):
                    
                    $jumlahBayar = 0;
                    
                    foreach ($dataSaleInvoicePayment['saleInvoiceArPayments'] as $dataSaleInvoiceArPayment) {
                        
                        $jumlahBayar += $dataSaleInvoiceArPayment['jumlah_bayar'];
                    }
                    
                    $jumlahSisa = $dataSaleInvoicePayment['jumlah_bayar'] - $jumlahBayar;
                    
                    $totalPiutang += $dataSaleInvoicePayment['jumlah_bayar'];
                    $totalBayar += $jumlahBayar;
                    $totalSisa += $jumlahSisa; ?>

                    <tr>
                        <td class="line"><?= $dataSaleInvoicePayment['saleInvoice']['id'] ?></td>
                        <td class="line"><?= Yii::$app->formatter->asDate($dataSaleInvoicePayment['saleInvoice']['date']) ?></td>                   
                        <td class="line"><?= $dataSaleInvoicePayment['saleInvoice']['mtableSession']['nama_tamu'] ?></td>                        
                        <td class="line"><?= $dataSaleInvoicePayment['keterangan'] ?></td>
                        <td class="line number"><?= Tools::convertToCurrency($dataSaleInvoicePayment['jumlah_bayar'], ($print == 'pdf')) ?></td>
                        <td class="line number"><?= Tools::convertToCurrency($jumlahBayar, ($print == 'pdf')) ?></td>
                        <td class="line number"><?= Tools::convertToCurrency($jumlahSisa, ($print == 'pdf')) ?></td>
                    </tr>

                <?php   
                endforeach; ?>                                    

            </tbody>
            <tfoot>
                <tr style="border:1px solid">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style="font-size: 16px">Grand Total</th>
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalPiutang, ($print == 'pdf')) ?></th>                                                  
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalBayar, ($print == 'pdf')) ?></th>
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalSisa, ($print == 'pdf')) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>           