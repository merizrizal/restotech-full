<?php
use restotech\standard\backend\components\Tools; ?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 100px">ID</th>
                    <th style="width: 50px">Tanggal</th>
                    <th style="width: 150px">Supplier</th>
                    <th style="width: 250px">Nama Item</th>
                    <th style="width: 200px">Gudang</th>
                    <th style="width: 100px">Rak</th>
                    <th style="width: 90px" class="number">Qty</th>
                    <th style="width: 90px">Satuan</th>
                    <th style="width: 120px" class="number">Harga</th>
                    <th style="width: 160px" class="number">Total Harga</th>                        
                </tr>
            </thead>
            <tbody>

                <?php
                $jumlahTotal = 0;
                
                foreach ($modelSupplierDelivery as $dataSupplierDelivery):                                                    

                    $jumlahTotal += $dataSupplierDelivery['jumlah_harga']; ?>

                    <tr>
                        <td class="line"><?= $dataSupplierDelivery['supplier_delivery_id'] ?></td>
                        <td class="line"><?= Yii::$app->formatter->asDate($dataSupplierDelivery['supplierDelivery']['date']) ?></td>                   
                        <td class="line"><?= $dataSupplierDelivery['supplierDelivery']['kdSupplier']['nama'] ?></td>
                        <td class="line"><?= $dataSupplierDelivery['item']['nama_item'] ?></td>                    
                        <td class="line"><?= '(' . $dataSupplierDelivery['storage']['id'] . ') ' . $dataSupplierDelivery['storage']['nama_storage'] ?></td>
                        <td class="line"><?= $dataSupplierDelivery['storageRack']['nama_rak'] ?></td>
                        <td class="line number"><?= $dataSupplierDelivery['jumlah_order'] ?></td> 
                        <td class="line"><?= $dataSupplierDelivery['itemSku']['nama_sku'] ?></td>     
                        <td class="line number"><?= Tools::convertToCurrency($dataSupplierDelivery['harga_satuan'], ($print == 'pdf')) ?></td>                                                  
                        <td class="line number"><?= Tools::convertToCurrency($dataSupplierDelivery['jumlah_harga'], ($print == 'pdf')) ?></td>                                                  
                    </tr>

                <?php   
                endforeach; ?>       
                    
                <tr>
                    <th></th>
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

            </tbody>
        </table>
    </div>
</div>           