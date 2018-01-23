<?php
use restotech\standard\backend\components\Tools; ?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 100px">No. Faktur</th>
                    <th style="width: 200px">Atas Nama</th>
                    <th style="width: 50px">Tanggal Bayar</th>                    
                    <th style="width: 180px" class="number">Jumlah Bayar</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $totalBayar = 0;
                
                foreach ($modelSaleInvoiceArPayment as $dataSaleInvoiceArPayment):
                    
                    $totalBayar += $dataSaleInvoiceArPayment['jumlah_bayar']; ?>

                    <tr>
                        <td class="line"><?= $dataSaleInvoiceArPayment['saleInvoicePayment']['saleInvoice']['id'] ?></td>                                           
                        <td class="line"><?= $dataSaleInvoiceArPayment['saleInvoicePayment']['saleInvoice']['mtableSession']['nama_tamu'] ?></td>
                        <td class="line"><?= Yii::$app->formatter->asDate($dataSaleInvoiceArPayment['date']) ?></td>
                        <td class="line number"><?= Tools::convertToCurrency($dataSaleInvoiceArPayment['jumlah_bayar'], ($print == 'pdf')) ?></td>
                    </tr>

                <?php   
                endforeach; ?>                                    

            </tbody>
            <tfoot>
                <tr style="border:1px solid">
                    <th></th>
                    <th></th>
                    <th style="font-size: 16px">Grand Total</th>
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalBayar, ($print == 'pdf')) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>           