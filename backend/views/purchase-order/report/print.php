<?php
use restotech\standard\backend\components\Tools;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';
?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <tbody>
                <tr>
                    <td style="font-size: 30px; font-weight: bold; text-align: center" colspan="2">PURCHASE ORDER</td>
                </tr>
                <tr>
                    <td style="width: 400px">
                        
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Supplier: <?= $model->kdSupplier->nama ?></td>
                                </tr>
                                <tr>
                                    <td><?= $model->kdSupplier->alamat ?></td>
                                </tr>
                                <tr>
                                    <td>Telp: <?= $model->kdSupplier->telp ?></td>
                                </tr>
                                <tr>
                                    <td>Fax: <?= $model->kdSupplier->telp ?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </td>
                    <td style="width: 200px">
                        
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>No. PO</td>
                                    <td>:<?= $model->id ?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>: <?= Yii::$app->formatter->asDate($model->date, 'dd-MM-yyyy') ?></td>
                                </tr>
                                <tr>
                                    <td>Print At</td>
                                    <td>: <?= Yii::$app->formatter->asDate(time(), 'dd-MM-yyyy HH:mm:ss') ?></td>
                                </tr>
                                <tr>
                                    <td>Print By</td>
                                    <td>: <?= Yii::$app->session->get('user_data')['employee']['nama'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        
                        <table class="table" style="font-size: 12px">
                            <tbody>
                                <tr style="border: 1px solid">
                                    <th>Item ID</th>
                                    <th>Nama Item</th>
                                    <th>Satuan</th>
                                    <th class="number">Jumlah</th>
                                    <th class="number">Harga Satuan</th>
                                    <th class="number">Subtotal</th>
                                </tr>
                                
                                <?php
                                foreach ($modelPurchaseOrderTrxs as $dataPurchaseOrderTrx): ?>
                                
                                    <tr>
                                        <td><?= $dataPurchaseOrderTrx->item->id ?></td>
                                        <td><?= $dataPurchaseOrderTrx->item->nama_item ?></td>
                                        <td><?= $dataPurchaseOrderTrx->itemSku->nama_sku ?></td>
                                        <td class="number"><?= $dataPurchaseOrderTrx->jumlah_order ?></td>
                                        <td class="number"><?= Tools::convertToCurrency($dataPurchaseOrderTrx->harga_satuan) ?></td>
                                        <td class="number"><?= Tools::convertToCurrency($dataPurchaseOrderTrx->jumlah_harga) ?></td>
                                    </tr>
                                
                                <?php
                                endforeach;?>                            
                                
                                <tr style="border: 1px solid">
                                    <th></th>
                                    <th></th>
                                    <th>TOTAL</th>
                                    <th class="number"><?= $model->jumlah_item ?></th>
                                    <th></th>
                                    <th class="number"><?= Tools::convertToCurrency($model->jumlah_harga) ?></th>
                                </tr>    
                            </tbody>
                        </table>
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>