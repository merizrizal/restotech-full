<?php
use yii\helpers\Html;

foreach ($data as $dataSDTrx): ?>    

    <tr>
        <td id="nama_item"><?= $dataSDTrx['item']['nama_item'] ?></td>
        <td id="nama_sku"><?= $dataSDTrx['itemSku']['nama_sku'] ?></td>
        <td id="jumlah_terima"><?= $dataSDTrx['jumlah_terima'] ?></td>
        <td id="harga_satuan"><?= Yii::$app->formatter->asCurrency($dataSDTrx['harga_satuan']) ?></td>
        
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][item_id]', $dataSDTrx['item_id'], ['id' => 'item-id']) ?>
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][item_sku_id]', $dataSDTrx['item_sku_id'], ['id' => 'item-sku-id']) ?>
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][jumlah_item]', $dataSDTrx['jumlah_terima'], ['id' => 'jumlah-item']) ?>
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][harga_satuan]', $dataSDTrx['harga_satuan'], ['id' => 'harga-satuan']) ?>        
    </tr>
    
<?php
endforeach; ?>