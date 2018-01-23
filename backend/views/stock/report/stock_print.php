<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 10px">#</th>
                    <th style="width: 200px">Nama Item</th>
                    <th style="width: 90px">Satuan</th>                                     
                    <th style="width: 150px">Gudang</th>
                    <th style="width: 80px">Rak</th>
                    <th style="width: 90px" class="number">Qty Stok</th>                                            
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 0;
                foreach ($modelStock as $dataStock): 
                    $i++; ?>

                    <tr>
                        <td class="line"><?= $i ?></td>
                        <td class="line"><?= $dataStock['item']['nama_item'] ?></td>    
                        <td class="line"><?= $dataStock['itemSku']['nama_sku'] ?></td>  
                        <td class="line"><?= '(' . $dataStock['storage_id'] . ') ' . $dataStock['storage']['nama_storage'] ?></td>
                        <td class="line"><?= $dataStock['storageRack']['nama_rak'] ?></td>
                        <td class="line number"><?= $dataStock['jumlah_stok'] ?></td>                                                                  
                    </tr>

                <?php
                endforeach; ?>

            </tbody>
        </table>
    </div>
</div>        