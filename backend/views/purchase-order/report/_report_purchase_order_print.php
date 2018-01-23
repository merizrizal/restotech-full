<?php
use restotech\standard\backend\components\Tools;
?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 100px">No. PO</th>
                    <th style="width: 50px">Tanggal</th>
                    <th style="width: 200px">Supplier</th>
                    <th style="width: 90px">No. SKU</th>
                    <th style="width: 200px">Nama Item</th>
                    <th style="width: 90px" class="number">Qty</th>
                    <th style="width: 90px">Satuan</th>
                    <th style="width: 180px" class="number">Harga</th>
                    <th style="width: 200px" class="number">Total Harga</th>                        
                </tr>
            </thead>
            <tbody>

                <?php
                $jumlahTotal = 0;
                foreach ($modelPurchaseOrder as $dataPurchaseOrder):                                                    

                    $jumlahTotal += $dataPurchaseOrder['jumlah_harga']; ?>

                    <tr>
                        <td class="line"><?= $dataPurchaseOrder['purchase_order_id'] ?></td>
                        <td class="line"><?= Yii::$app->formatter->asDate($dataPurchaseOrder['purchaseOrder']['date']) ?></td>                   
                        <td class="line"><?= $dataPurchaseOrder['purchaseOrder']['kdSupplier']['nama'] ?></td>
                        <td class="line"><?= $dataPurchaseOrder['itemSku']['id'] ?></td>
                        <td class="line"><?= $dataPurchaseOrder['item']['nama_item'] ?></td>                    
                        <td class="line number"><?= $dataPurchaseOrder['jumlah_order'] ?></td> 
                        <td class="line"><?= $dataPurchaseOrder['itemSku']['nama_sku'] ?></td>     
                        <td class="line number"><?= Tools::convertToCurrency($dataPurchaseOrder['harga_satuan'], ($print == 'pdf')) ?></td>                                                  
                        <td class="line number"><?= Tools::convertToCurrency($dataPurchaseOrder['jumlah_harga'], ($print == 'pdf')) ?></td>                                                  
                    </tr>

                    <?php

                    
                endforeach; ?>                                    

            </tbody>
            <tfoot>
                <tr style="border:1px solid">
                    <th></th>
                    <th></th>                   
                    <th></th>
                    <th></th>
                    <th></th>                    
                    <th></th> 
                    <th></th>     
                    <th style="font-size: 16px">Grand Total</th>                                                  
                    <th class="number" style="font-size: 16px"><?= Tools::convertToCurrency($jumlahTotal, ($print == 'pdf')) ?></th>                                                  
                </tr>
            </tfoot>
        </table>
    </div>
</div>           