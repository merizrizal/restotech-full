<?php
use yii\helpers\Html;

foreach ($data as $dataRPTrx): ?>    

    <tr>
        <td id="nama_item"><?= $dataRPTrx['item']['nama_item'] ?></td>
        <td id="nama_sku"><?= $dataRPTrx['itemSku']['nama_sku'] ?></td>
        <td id="jumlah_terima"><?= $dataRPTrx['jumlah_item'] ?></td>
        <td id="harga_satuan"><?= Yii::$app->formatter->asCurrency($dataRPTrx['harga_satuan']) ?></td>
        
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][item_id]', $dataRPTrx['item_id'], ['id' => 'item-id']) ?>
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][item_sku_id]', $dataRPTrx['item_sku_id'], ['id' => 'item-sku-id']) ?>
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][jumlah_item]', -1 * $dataRPTrx['jumlah_item'], ['id' => 'jumlah-item']) ?>
        <?= Html::hiddenInput('SupplierDeliveryInvoiceTrx[index][harga_satuan]', $dataRPTrx['harga_satuan'], ['id' => 'harga-satuan']) ?>
    </tr>
    
<?php
endforeach; ?>