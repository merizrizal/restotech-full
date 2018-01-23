<?php
use restotech\standard\backend\components\Tools; ?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 100px">No. Invoice</th>
                    <th style="width: 50px">Tanggal</th>
                    <th style="width: 200px">Supplier</th>
                    <th style="width: 180px" class="number">Jumlah Hutang</th>
                    <th style="width: 180px" class="number">Dibayar</th>
                    <th style="width: 180px" class="number">Sisa</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $totalHutang = 0;
                $totalBayar = 0;
                $totalSisa = 0;
                
                foreach ($modelSupplierDeliveryInvoice as $dataSupplierDeliveryInvoice):                                         
                    
                    $jumlahSisa = $dataSupplierDeliveryInvoice['jumlah_harga'] - $dataSupplierDeliveryInvoice['jumlah_bayar'];
                
                    $totalHutang += $dataSupplierDeliveryInvoice['jumlah_harga'];
                    $totalBayar += $dataSupplierDeliveryInvoice['jumlah_bayar'];
                    $totalSisa += $jumlahSisa; ?>

                    <tr>
                        <td class="line"><?= $dataSupplierDeliveryInvoice['id'] ?></td>
                        <td class="line"><?= Yii::$app->formatter->asDate($dataSupplierDeliveryInvoice['date']) ?></td>                   
                        <td class="line"><?= $dataSupplierDeliveryInvoice['supplierDelivery']['kdSupplier']['nama'] ?></td>                        
                        <td class="line number"><?= Tools::convertToCurrency($dataSupplierDeliveryInvoice['jumlah_harga'], ($print == 'pdf')) ?></td>
                        <td class="line number"><?= Tools::convertToCurrency($dataSupplierDeliveryInvoice['jumlah_bayar'], ($print == 'pdf')) ?></td>
                        <td class="line number"><?= Tools::convertToCurrency($jumlahSisa, ($print == 'pdf')) ?></td>
                    </tr>

                <?php   
                endforeach; ?>                                    

            </tbody>
            <tfoot>
                <tr style="border:1px solid">                    
                    <th></th> 
                    <th></th>     
                    <th style="font-size: 16px">Grand Total</th>                                                  
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalHutang, ($print == 'pdf')) ?></th>                                                  
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalBayar, ($print == 'pdf')) ?></th>
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($totalSisa, ($print == 'pdf')) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>           