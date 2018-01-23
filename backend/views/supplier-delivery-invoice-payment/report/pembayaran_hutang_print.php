<?php
use restotech\standard\backend\components\Tools; ?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 100px">No. Invoice</th>
                    <th style="width: 200px">Supplier</th>
                    <th style="width: 50px">Tanggal Bayar</th>                    
                    <th style="width: 180px" class="number">Jumlah Bayar</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $totalBayar = 0;
                
                foreach ($modelSupplierDeliveryInvoice as $dataSupplierDeliveryInvoice):
                    
                    foreach ($dataSupplierDeliveryInvoice['supplierDeliveryInvoicePayments'] as $dataSupplierDeliveryInvoicePayment):                        
                    
                        $totalBayar += $dataSupplierDeliveryInvoicePayment['jumlah_bayar'] ?>

                        <tr>
                            <td class="line"><?= $dataSupplierDeliveryInvoice['id'] ?></td>                                               
                            <td class="line"><?= $dataSupplierDeliveryInvoice['supplierDelivery']['kdSupplier']['nama'] ?></td>
                            <td class="line"><?= Yii::$app->formatter->asDate($dataSupplierDeliveryInvoicePayment['date']) ?></td>
                            <td class="line number"><?= Tools::convertToCurrency($dataSupplierDeliveryInvoicePayment['jumlah_bayar'], ($print == 'pdf')) ?></td>
                        </tr>

                    <?php
                    endforeach;
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