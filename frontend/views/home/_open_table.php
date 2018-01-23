<?php
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\Tools;
use restotech\standard\backend\components\PrinterDialog;
use restotech\standard\backend\components\VirtualKeyboard;

Yii::$app->formatter->timeZone = 'Asia/Jakarta';

$form = new ActiveForm();

$virtualKeyboard = new VirtualKeyboard();

Tools::loadIsIncludeScp();

$discBillValue = 0;

if ($modelMtableSession->discount_type == 'Percent') {      
    
    $discBillValue = round($modelMtableSession->discount * 0.01 * $modelMtableSession->jumlah_harga); 
} else if ($modelMtableSession->discount_type == 'Value') {
    
    $discBillValue = $modelMtableSession->discount;                                                
}

if (!empty($settingsArray)) {
    echo Html::textarea('struk_invoice_header', $settingsArray['struk_invoice_header'], ['id' => 'struk-invoice-header', 'style' => 'display:none']);
    echo Html::textarea('struk_invoice_footer', $settingsArray['struk_invoice_footer'], ['id' => 'struk-invoice-footer', 'style' => 'display:none']);
    echo Html::textarea('struk_order_header', $settingsArray['struk_order_header'], ['id' => 'struk-order-header', 'style' => 'display:none']);
    echo Html::textarea('struk_order_footer', $settingsArray['struk_order_footer'], ['id' => 'struk-order-footer', 'style' => 'display:none']); 
} 

echo Html::hiddenInput('sess_id', $modelMtableSession->id, ['class' => 'sess-id session']);
echo Html::hiddenInput('mtable_id', $modelMtableSession->mtable->id, ['class' => 'mtable-id session']);
echo Html::hiddenInput('nama_tamu', $modelMtableSession->nama_tamu, ['class' => 'nama-tamu session']);
echo Html::hiddenInput('jumlah_tamu', $modelMtableSession->jumlah_tamu, ['class' => 'jumlah-tamu session']);
echo Html::hiddenInput('catatan', $modelMtableSession->catatan, ['class' => 'catatan session']);
echo Html::hiddenInput('jumlah_harga', $modelMtableSession->jumlah_harga, ['class' => 'jumlah-harga session']);
echo Html::hiddenInput('discount_type', $modelMtableSession->discount_type, ['class' => 'discount-type session']);
echo Html::hiddenInput('discount', $modelMtableSession->discount, ['class' => 'discount session']);
echo Html::hiddenInput('pajak', $modelMtableSession->pajak, ['class' => 'pajak session']);
echo Html::hiddenInput('service_charge', $modelMtableSession->service_charge, ['class' => 'service-charge session']);
echo Html::hiddenInput('bill_printed', $modelMtableSession->bill_printed, ['class' => 'bill-printed session']);                                       
echo Html::hiddenInput('opened_table_at', Yii::$app->formatter->asDatetime($modelMtableSession->opened_at, 'dd-MM-yyyy HH:mm'), ['class' => 'open-table-at session']);

echo Html::hiddenInput('user_active', Yii::$app->session->get('user_data')['employee']['nama'], ['id' => 'user-active']); 

echo Html::hiddenInput('mtable_nama', $modelMtableSession->mtable->nama_meja, ['class' => 'mtable-nama session']);

echo Html::hiddenInput('after_split', Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/view-session', 'id' => $modelMtableSession->mtable->id, 'cid' => $modelMtableSession->mtable->mtable_category_id, 'sessId' => $modelMtableSession->id]), ['id' => 'after-split']); ?>

<table id="temp-order" style="display: none">
    <tbody>
        <tr id="menu-row" style="cursor: pointer">

            <?= Html::hiddenInput('order_id', null, ['class' => 'order-id order']) ?>
            <?= Html::hiddenInput('parent_id', null, ['class' => 'parent-id order']) ?>
            <?= Html::hiddenInput('menu_id', null, ['class' => 'menu-id order']) ?>
            <?= Html::hiddenInput('catatan', null, ['class' => 'catatan order']) ?>
            <?= Html::hiddenInput('discount_type', null, ['class' => 'discount-type order']) ?>
            <?= Html::hiddenInput('discount', null, ['class' => 'discount order']) ?>
            <?= Html::hiddenInput('harga_satuan', null, ['class' => 'harga-satuan order']) ?>
            <?= Html::hiddenInput('jumlah', null, ['class' => 'jumlah order']) ?>
            <?= Html::hiddenInput('is_free_menu', null, ['class' => 'is-free-menu order']) ?>
            <?= Html::hiddenInput('is_void', null, ['class' => 'is-void order']) ?>
            <?= Html::hiddenInput('printer', null, ['class' => 'printer order']) ?>

            <td id="no"></td>
            <td id="menu" class="goleft">
                <span></span>
            </td>
            <td id="qty" class="centered">
                <span></span>
                <br>
                <span id="badge-queue"></span>
            </td>
            <td id="subtotal" class="goright">
                <span id="span-discount">Disc: <span id="val-discount">0</span></span>
                <br>
                <span id="span-subtotal"></span>
            </td>
        </tr>
    </tbody>
</table>

<div id="temp-container" class="hidden">
    <div id="temp-info-tamu">    
        <form>

            <?= $form->field($modelMtableSession, 'jumlah_tamu')->textInput(['class' => 'form-control jumlah-tamu temp-session', 'data-validation' => 'number', 'data-validation-error-msg-number' => 'Jumlah Tamu harus berupa angka.']) ?>

            <?= $form->field($modelMtableSession, 'nama_tamu')->textInput(['class' => 'form-control nama-tamu temp-session']) ?>
            
            <?= $form->field($modelMtableSession, 'catatan')->textInput(['class' => 'form-control catatan temp-session']) ?>

        </form>
    </div>
    
    <div id="temp-discount-bill">    
        <form>            
            
            
            <?= Html::hiddenInput('discount_type', 'Percent', ['class' => 'discount-type temp-session']) ?>
            
            <?= Html::radio('discount_type-temp', true, [
                'label' => 'Percent',
                'value' => 'Percent',
                'id' => 'discount_type-percent',
                'class' => 'discount-type-temp'
            ]); ?>
                        
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 

            <?= Html::radio('discount_type-temp', false, [
                'label' => 'Value',
                'value' => 'Value',
                'id' => 'discount_type-value',
                'class' => 'discount-type-temp'
            ]); ?>
            
            <?= $form->field($modelMtableSession, 'discount')->widget(MaskMoney::className(), ['class' => 'form-control discount temp-session']) ?>

        </form>
    </div>
    
    <?php
    $modelMtableOrder = new restotech\standard\backend\models\MtableOrder(); ?>
    
    <div id="temp-catatan">    
        <form>            
            
            <?= $form->field($modelMtableOrder, 'catatan')->textInput(['class' => 'form-control catatan temp-order', 'data-validation' => 'required', 'data-validation-error-msg-required' => 'Catatan tidak boleh kosong.']) ?>

        </form>
    </div>
    
    <div id="temp-discount-menu">    
        <form>            
            
            
            <?= Html::hiddenInput('discount_type', 'Percent', ['class' => 'discount-type temp-order']) ?>
            
            <?= Html::radio('discount_type-temp', true, [
                'label' => 'Percent',
                'value' => 'Percent',
                'id' => 'discount_type-percent',
                'class' => 'discount-type-temp'
            ]); ?>
                        
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 

            <?= Html::radio('discount_type-temp', false, [
                'label' => 'Value',
                'value' => 'Value',
                'id' => 'discount_type-value',
                'class' => 'discount-type-temp'
            ]); ?>
            
            <?= $form->field($modelMtableOrder, 'discount')->widget(MaskMoney::className(), ['class' => 'form-control discount temp-order']) ?>

        </form>
    </div>
    
    <div id="temp-close-table">    
        <form>
            
            <?= $form->field($modelMtableSession, 'catatan')->textInput(['class' => 'form-control catatan temp-session']) ?>

        </form>
    </div>
    
</div>

<div class="col-lg-12">

    <div class="row">
        <div class="col-md-12 col-sm-12 mb">
            <div class="white-panel pn" style="height: auto">
                <div class="white-header"></div>
                <div style="padding: 0 10px 15px 10px">
                    <div class="row goleft">
                        <div class="col-md-12 col-sm-12 btnMenu">
                            <a id="info-tamu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/info-tamu']) ?>"><i class="ion ion-ios-people" style="font-size: 16px; color: white"></i> Info Tamu</a>
                            <a id="catatan" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/catatan']) ?>"><i class="ion ion-ios-list-outline" style="font-size: 16px; color: white"></i> Catatan Menu</a>
                            <a id="discount-menu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/discount-menu']) ?>"><i class="ion ion-ios-pricetags" style="font-size: 16px; color: white"></i> Discount Menu</a>                            
                            <a id="free-menu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/free-menu']) ?>"><i class="ion ion-bag" style="font-size: 16px; color: white"></i> Free Menu</a>
                            <a id="queue-menu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/queue-menu']) ?>"><i class="ion ion-ios-paper-outline" style="font-size: 16px; color: white"></i> Antrikan Menu</a>                            
                            <a id="void-menu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/void-menu']) ?>"><i class="ion ion-backspace" style="font-size: 16px; color: white"></i> Void Menu</a>                            
                            <a id="split" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/split']) ?>"><i class="ion ion-android-done-all" style="font-size: 16px; color: white"></i> Split</a>                            
                        </div>                                                   
                    </div>
                    
                    <div class="row goleft" style="margin-top: 10px">
                        <div class="col-md-12 col-sm-12 btnMenu">                            
                            <a id="discount-bill" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/discount-bill']) ?>"><i class="ion ion-ios-pricetags" style="font-size: 16px; color: white"></i> Diskon Tagihan</a>
                            <a id="transfer-table" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/transfer-table']) ?>"><i class="ion ion-arrow-return-right" style="font-size: 16px; color: white"></i> Transfer Meja</a>
                            <a id="transfer-menu" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/transfer-menu']) ?>"><i class="ion ion-arrow-swap" style="font-size: 16px; color: white"></i> Transfer Menu</a>
                            <a id="join-table" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/join-table']) ?>"><i class="ion ion-arrow-shrink" style="font-size: 16px; color: white"></i> Gabung Meja</a>                                                                                    
                            <a id="cashdrawer" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/cashdrawer']) ?>"><i class="ion ion-eject" style="font-size: 16px; color: white"></i> Cashdrawer</a>                                                        
                            <a id="print-bill" class="btn btn-primary btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/print-bill']) ?>"><i class="ion ion-ios-printer" style="font-size: 16px; color: white"></i> Cetak Tagihan</a>                                                                                            
                            <a id="close-table" class="btn btn-danger btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/close-table']) ?>"><i class="ion ion-ios-upload" style="font-size: 16px; color: white"></i> Close Table</a>                                                                                    
                        </div>                                                    
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="weather-2 pn" style="height: auto; padding-bottom: 20px">
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-4 col-xs-4">                            
                            <p>
                                Meja: <?= $modelMtableSession->mtable->nama_meja . ' (' . $modelMtableSession->mtable->id . ')' ?>
                            </p>         
                        </div>
                        <div class="col-sm-4 col-xs-4">                            
                            <p>
                                <?= Html::a('<i class="ion ion-unlocked" style="font-size: 16px; color: white"></i> Unlock Bill', Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/unlock-bill']), ['id' => 'unlock-bill', 'class' => 'btn btn-success btn-lg ' . ($modelMtableSession->bill_printed ? '' : 'hidden')]) ?>                                
                                <a id="payment" class="btn btn-danger btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'home/payment', 'id' => $modelMtableSession->id, 'isCorrection' => $isCorrection]) ?>"><i class="ion ion-cash" style="font-size: 16px; color: white"></i> Payment</a>
                            </p>         
                        </div>
                        <div class="col-sm-4 col-xs-4 pull-right">
                            <p class="pull-right">
                                
                                <?php
                                $url = [Yii::$app->params['module'] . 'home/table', 'id' => $modelMtableSession->mtable->mtable_category_id];
                                
                                if ($isCorrection) {
                                    $url = [Yii::$app->params['module'] . 'home/correction-invoice'];
                                } ?>
                                
                                <?= Html::a('<i class="fa fa-undo" style="color: white"></i> Back', Yii::$app->urlManager->createUrl($url), ['id' => 'back', 'class' => 'btn btn-danger btn-lg']) ?>
                                
                            </p>
                        </div>
                    </div>                    
                </div>
                
                <div class="row data mt">                    
                    
                    <div class="col-lg-4">
                        <div class="white-panel pn" style="height: auto; color: #000">
                            <div class="white-header" style="padding: 10px">
                                <div class="row">
                                    <div class="col-md-7 goleft">
                                        <a id="qty-plus" class="btn btn-primary btn-sm qty" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/change-qty']) ?>"><i class="fa fa-plus" style="font-size: 12px; color: white"></i></a>
                                        <a id="qty-minus" class="btn btn-danger btn-sm qty" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'action/change-qty']) ?>"><i class="fa fa-minus" style="font-size: 12px; color: white"></i></a>
                                    </div>
                                    <div class="col-md-5 goright">
                                        <button id="select-all" class="btn btn-primary btn-sm" type="button">
                                            <i class="fa fa-check-square-o" style="font-size: 12px; color: white"></i>All                                            
                                        </button>
                                        <button id="unselect-all" class="btn btn-primary btn-sm" type="button">
                                            <i class="fa fa-square-o" style="font-size: 12px; color: white"></i>All
                                        </button>
                                    </div>
                                </div>                                
                            </div>
                            
                            
                            
                            <div class="table-responsive">
                                
                                <table class="table table-advance table-hover">
                                    
                                    <thead>
                                        <tr>
                                            <th class="goleft">#</th>
                                            <th class="goleft">Menu</th>
                                            <th class="centered" style="width: 60px">Qty</th>
                                            <th class="goright" style="width: 35%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-menu">
                                        
                                        <?php                                                                                
                                        $number = 1;
                                        
                                        $jumlah_harga = 0;
                                        $serviceCharge = 0;
                                        $pajak = 0;
                                        $grandTotal = 0;                                                                                                                        

                                        $totalFreeMenu = 0;
                                        $totalVoid = 0;

                                        if (count($modelMtableSession->mtableOrders) > 0): 
                                            
                                            foreach ($modelMtableSession->mtableOrders as $mtableOrderData):   
                                            
                                                $data = [];
                                                
                                                if (!empty($mtableOrderData->mtableOrders)) {
                                                    $data[] = $mtableOrderData;
                                                    $data = array_merge($data, $mtableOrderData->mtableOrders);
                                                } else {
                                                    $data[] = $mtableOrderData;
                                                }
                                                
                                                foreach ($data as $mtableOrderData): 

                                                    $subtotal = $mtableOrderData->jumlah * $mtableOrderData->harga_satuan;

                                                    if ($mtableOrderData->is_free_menu) {
                                                        
                                                        $totalFreeMenu += $subtotal;
                                                    }

                                                    if ($mtableOrderData->is_void) {
                                                        
                                                        $totalVoid += $subtotal;
                                                    }
                                                    
                                                    $disc = 0;

                                                    if ($mtableOrderData->discount_type == 'Percent') {           
                                                        
                                                        $disc = round($mtableOrderData->discount * 0.01 * $subtotal);
                                                    } else if ($mtableOrderData->discount_type == 'Value') {
                                                        
                                                        $disc = $mtableOrderData->jumlah * $mtableOrderData->discount;
                                                    }                                                         
                                                    
                                                    $subtotal = $subtotal - $disc; 

                                                    if (!$mtableOrderData->is_free_menu && !$mtableOrderData->is_void) {
                                                        
                                                        $jumlah_harga += $subtotal;
                                                    } ?>

                                                    <tr id="menu-row" class="<?= ($mtableOrderData->is_void ? 'voided ' : ($mtableOrderData->is_free_menu ? 'free-menu' : '')) ?>" style="cursor: pointer">
                                                        
                                                        <?= Html::hiddenInput('order_id', $mtableOrderData->id, ['class' => 'order-id order']) ?>
                                                        <?= Html::hiddenInput('parent_id', $mtableOrderData->parent_id, ['class' => 'parent-id order']) ?>
                                                        <?= Html::hiddenInput('menu_id', $mtableOrderData->menu_id, ['class' => 'menu-id order']) ?>
                                                        <?= Html::hiddenInput('catatan', $mtableOrderData->catatan, ['class' => 'catatan order']) ?>
                                                        <?= Html::hiddenInput('discount_type', $mtableOrderData->discount_type, ['class' => 'discount-type order']) ?>
                                                        <?= Html::hiddenInput('discount', $mtableOrderData->discount, ['class' => 'discount order']) ?>
                                                        <?= Html::hiddenInput('harga_satuan', $mtableOrderData->harga_satuan, ['class' => 'harga-satuan order']) ?>
                                                        <?= Html::hiddenInput('jumlah', $mtableOrderData->jumlah, ['class' => 'jumlah order']) ?>
                                                        <?= Html::hiddenInput('is_free_menu', $mtableOrderData->is_free_menu, ['class' => 'is-free-menu order']) ?>
                                                        <?= Html::hiddenInput('is_void', $mtableOrderData->is_void, ['class' => 'is-void order']) ?>
                                                        
                                                        <?php
                                                        if (!empty($mtableOrderData->menu->menuCategory->menuCategoryPrinters)) {
                                                            
                                                            foreach ($mtableOrderData->menu->menuCategory->menuCategoryPrinters as $value) {
                                                                
                                                                if (!empty($value['printer0']) && !$value->printer0->not_active) {
                                                                    
                                                                        echo Html::hiddenInput('printer', $value->printer0->printer, ['class' => 'printer order']);
                                                                    }
                                                                }
                                                        } ?>
                                                        
                                                        <td id="no"><?= empty($mtableOrderData->parent_id) ? $number : '<i class="fa fa-plus" style="color:green"></i>' ?></td>
                                                        <td id="menu" class="goleft">
                                                            <span><?= $mtableOrderData->menu->nama_menu ?></span>                                                                                                                                                                                                                                                                                                                                                                    

                                                            <?php
                                                            $badgeMenuQueue = '';

                                                            if (!empty($mtableOrderData->mtableOrderQueue)) {

                                                                if ($mtableOrderData->mtableOrderQueue->is_finish)
                                                                    $badgeMenuQueue = '<div class="badge bg-success"><i class="fa fa-thumbs-up" style="color:#000"></i></div>';
                                                                else
                                                                    $badgeMenuQueue = '<div class="badge bg-important"><i class="fa fa-thumbs-o-up" style="color:#FFF"></i></div>';
                                                            } ?>

                                                        </td>
                                                        <td id="qty" class="centered">
                                                            
                                                            <span><?= $mtableOrderData->jumlah ?></span>
                                                            <br>
                                                            <span id="badge-queue"><?= $badgeMenuQueue ?></span>
                                                        </td>
                                                        <td id="subtotal" class="goright">
                                                            <span id="span-discount">Disc: <span id="val-discount"><?= $mtableOrderData->discount ?></span></span>
                                                            <br>
                                                            <span id="span-subtotal"><?= $subtotal ?></span>
                                                        </td>
                                                    </tr>

                                                    <?php
                                                    
                                                endforeach;                                                    
                                                
                                                $number++;
                                                
                                            endforeach;                                                                                        

                                            $scp = Tools::hitungServiceChargePajak($jumlah_harga, $modelMtableSession->service_charge, $modelMtableSession->pajak);                                        
                                            $serviceCharge = $scp['serviceCharge'];
                                            $pajak = $scp['pajak']; 
                                            $grandTotal = $jumlah_harga + $serviceCharge + $pajak - $discBillValue;

                                        endif; 
                                        
                                        echo Html::hiddenInput('index', $number, ['id' => 'index']);?>

                                    </tbody>
                                    <tfoot>
                                        <tr id="free-menu-row">
                                            <td colspan="2" class="goleft">Total Free Menu</td>
                                            <td colspan="2" class="goright">
                                                <span>(</span><span id="total-free-menu"><?= $totalFreeMenu ?></span><span>)</span>
                                            </td>
                                            <?= Html::hiddenInput('total_free_menu', $totalFreeMenu, ['id' => 'total-free-menu']) ?>
                                        </tr>
                                        <tr id="void-row">
                                            <td colspan="2" class="goleft">Total Void</td>
                                            <td colspan="2" class="goright">
                                                <span>(</span><span id="total-void"><?= $totalVoid ?></span><span>)</span>
                                            </td>
                                            <?= Html::hiddenInput('total_void', $totalVoid, ['id' => 'total-void']) ?>
                                        </tr>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td colspan="2" class="goleft">Total</td>
                                            <td colspan="2" id="total-harga" class="goright"><?= $jumlah_harga ?></td>
                                        </tr>                                        
                                        <tr>
                                            <td colspan="2" class="goleft">Service (<?= $modelMtableSession->service_charge ?> %)</td>
                                            <td colspan="2" id="service-charge-amount" class="goright"><?= $serviceCharge ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="goleft">Ppn (<?= $modelMtableSession->pajak ?> %)</td>
                                            <td colspan="2" id="tax-amount" class="goright"><?= $pajak ?></td>
                                        </tr>  
                                        <tr>                                                   
                                            <td colspan="2" class="goleft">Discount Bill <span id="discbill-text"><?= $modelMtableSession->discount_type === 'Percent' ? '(' . $modelMtableSession->discount . '%)' : '' ?></span></td>
                                            <td colspan="2" class="goright">
                                                (<span id="discbill"><?= $discBillValue ?></span>)
                                            </td>
                                            
                                        </tr>
                                        <tr style="font-weight: bold; font-size: 16px">
                                            <td colspan="2" class="goleft">Grand Total</td>
                                            <td colspan="2" id="grand-harga" class="goright"><?= $grandTotal ?></td>                            
                                        </tr>
                                    </tfoot>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="darkblue-panel pn" style="height: auto; padding: 0 10px 10px 10px">
                            <div class="darkblue-header" style="padding-top: 10px; text-align: left">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-3 text-right">
                                            <?= Html::label('Search Menu', 'search-menu', ['class' => 'control-label', 'style' => 'color:white;font-size:18px']) ?>
                                        </div>                                    
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <?= Html::textInput('search_menu', null, ['class' => 'form-control', 'id' => 'search-menu']) ?>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-search"></i></button>
                                                    <button id="cancel-search-menu" class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i></button>
                                                </span>
                                            </div>
                                        </div>                                                                        
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <a id="add" class="add-condiment btn btn-success btn-lg" href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/condiment']) ?>"><i class="glyphicon glyphicon-plus"></i> Add Condiment</a>
                                        <a id="cancel" class="add-condiment btn btn-danger btn-lg" style="display: none"><i class="glyphicon glyphicon-remove"></i> Cancel Condiment</a>
                                        <?= Html::hiddenInput('val_add_condiment', "", ['id' => 'val-add-condiment']) ?>
                                    </div>
                                </div>
                                
                            </div>                            
                            <div class="darkblue-header" style="padding-top: 10px; text-align: left">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?= Html::button('<i class="fa fa-chevron-circle-left"></i> Back', ['class' => 'btn btn-danger', 'id' => 'load-menu-back', 'style' => 'display:none']) ?> 
                                    </div>
                                </div>
                            </div>
                            <div id="menu-container" class="row" style="height: auto; max-height: paperWidth0px; overflow-x: hidden">
                                <br><br><br><br><br><br><br><br><br><br>
                            </div>

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php

$printerDialog = new PrinterDialog();
$printerDialog->theScript();
echo $printerDialog->renderDialog('pos');

$jscript = '
    var datetime;
    var getDateTime = function() {  
    
        datetime = 0;
        
        $.when(
            $.ajax({
                async: false,
                type: "GET",
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/datetime']) . '",            
                success: function(data) {
                    datetime = data.datetime;
                }
            })
        ).done(function() {
            return datetime;
        });
    };
    
    var searchMenu = function(namaMenu) {
        
        $.ajax({
            cache: false,
            type: "POST",
            data: {"namaMenu" : namaMenu},
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/search-menu']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#menu-container").html(response);
                
                $(".overlay").hide();
                $(".loading-img").hide();                                        
            }
        });
    };
    
    var loadMenuCategory = function() {
    
        $.ajax({
            cache: false,
            type: "POST",
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/menu-category']) . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#menu-container").html(response);
                
                $("#load-menu-back").css("display", "none");
                
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
    };
    
    var action = function(thisObj, _title, _onOpen, _data, _response) {
    
        var form = $("#temp-" + thisObj.attr("id")).clone();        
        
        swal({
            title: _title,            
            html: 
                "<div id=\"" + thisObj.attr("id") + "-container\">" + 
                    form.html() + 
                "</div>",
            showCancelButton: true,
            onOpen: function () {
                
                $.validate();
                
                $("#" + thisObj.attr("id") + "-container").find("form").on("submit", function() {
                
                    return false;
                });
                
                _onOpen();
            },
            preConfirm: function () {                            
                
                return new Promise(function (resolve, reject) {
                    
                    if($("#" + thisObj.attr("id") + "-container").find(".has-error").length > 0) {
                        reject("Inputkan data dengan benar.");
                    } else {
                        resolve(true);
                    }
                });
            }
        }).then(        
            function(result) {
                if (result) {
                
                    $.ajax({
                        cache: false,
                        dataType: "json",
                        type: "POST",
                        url: thisObj.attr("href"),
                        data: _data(thisObj),
                        beforeSend: function(xhr) {
                            $(".overlay").show();
                            $(".loading-img").show();
                        },
                        success: function(response) {

                            $(".overlay").hide();
                            $(".loading-img").hide();   
                            
                            if (response.success) {
                                
                                _response(response);
                            } else {
                                swal("Error", "Terjadi kesalahan dalam proses input " + _title + ".", "error");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {                     

                            $(".overlay").hide();
                            $(".loading-img").hide();

                            swal("Error", xhr.responseText, "error");
                        }
                    });
                }
            },
            function(dismiss) {
                                
            }
        );
    };
    
    ' . Tools::jsHitungServiceChargePajak() . '
    
    var hitungDiscBill = function() {
    
        var discountType = $(".discount-type.session").val();
        
        var discount = parseFloat($(".discount.session").val());
        
        var harga = parseFloat($(".jumlah-harga.session").val());
        
        var hargaDisc = 0; 
        
        if (discountType == "Percent") {
        
            hargaDisc = Math.round(discount * 0.01 * harga);
        } else if (discountType == "Value") {
        
            hargaDisc = discount; 
        }        
        
        return hargaDisc;
    };
    
    var hitungTotal = function(totalHarga) {
    
        $(".jumlah-harga.session").val(totalHarga);
        $("#total-harga").html(totalHarga);
        $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '}); 

        var scp = hitungServiceChargePajak(totalHarga, $(".service-charge.session").val(), $(".pajak.session").val());                                   
        var serviceCharge = scp["serviceCharge"];        
        var pajak = scp["pajak"];
        var grandTotal = totalHarga + serviceCharge + pajak - hitungDiscBill();

        $("#service-charge-amount").html(serviceCharge);
        $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

        $("#tax-amount").html(pajak);
        $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});

        $("#discbill").html(hitungDiscBill());
        $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});

        $("#grand-harga").html(grandTotal);
        $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
    };
    
    var orderId = [];
    var trObj = [];
    var i = 0;            
    var setOrderId = function(id, obj) {

        orderId[i] = id;
        trObj[i] = obj;
        i++;
    };

    var percentMaskMoney = {
        thousands: ".",
        decimal: ",",
        prefix: "",
        suffix: "",
        precision: 0
    };
    
    var valueMaskMoney = {
        thousands: ".",
        decimal: ",",
        prefix: "Rp ",
        suffix: "",
        precision: 0
    };
        
';

$jscriptInit = '
    
    $("#order-menu").find(".discount-type.order").each(function() {
    
        if ($(this).val() == "Percent") {

        } else if ($(this).val() == "Value") {
            $(this).parent().find("td#subtotal #span-discount #val-discount").currency({' . Yii::$app->params['currencyOptions'] . '});
        }
    });
    
    $("span#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-void").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#total-harga").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#service-charge-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#tax-amount").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
    $("#grand-harga").currency({' . Yii::$app->params['currencyOptions'] . '});           
    
    ' . $virtualKeyboard->keyboardQwerty('#search-menu', false) . '
';

$jscriptAction = '
    $("#info-tamu").on("click", function() {
    
        var onOpen = function() {
        
            $("#info-tamu-container").find(".nama-tamu.temp-session").val($(".nama-tamu.session").val());
            $("#info-tamu-container").find(".jumlah-tamu.temp-session").val($(".jumlah-tamu.session").val());
            $("#info-tamu-container").find(".catatan.temp-session").val($(".catatan.session").val());

            $("#info-tamu-container").find(".jumlah-tamu.temp-session").focus();

            ' . $virtualKeyboard->keyboardNumeric('$("#info-tamu-container").find(".jumlah-tamu.temp-session")', true) . '
            ' . $virtualKeyboard->keyboardQwerty('$("#info-tamu-container").find(".nama-tamu.temp-session")', true) . '
            ' . $virtualKeyboard->keyboardQwerty('$("#info-tamu-container").find(".catatan.temp-session")', true) . '
        };
        
        var data = function(thisObj) {
            
            return {
                "sess_id": $(".sess-id.session").val(),
                "jumlah_tamu": $("#" + thisObj.attr("id") + "-container").find(".jumlah-tamu.temp-session").val(),
                "nama_tamu": $("#" + thisObj.attr("id") + "-container").find(".nama-tamu.temp-session").val(),
                "catatan": $("#" + thisObj.attr("id") + "-container").find(".catatan.temp-session").val(),
            };
        };
        
        var response = function(response) {
        
            if (response.success) {

                $(".nama-tamu.session").val(response.nama_tamu);
                $(".jumlah-tamu.session").val(response.jumlah_tamu);
                $(".catatan.session").val(response.catatan);
            } else {
                swal("Error", "Terjadi kesalahan dalam proses input Info Tamu.", "error");
            }
        };
        
        action($(this), "Info Tamu", onOpen, data, response);
        
        return false;
    });
    
    $("#catatan").on("click", function() {
    
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
    
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {                       
        
            var onOpen = function() {
        
                $("#catatan-container").find(".catatan.temp-order").val($("#order-menu").children("tr#menu-row.highlight").find(".catatan.order").val());

                $("#catatan-container").find(".catatan.temp-order").focus();

                ' . $virtualKeyboard->keyboardQwerty('$("#catatan-container").find(".catatan.temp-order")', true) . '
            };

            var data = function(thisObj) {

                return {
                    "order_id": $("#order-menu").children("tr#menu-row.highlight").find(".order-id.order").val(),
                    "catatan": $("#" + thisObj.attr("id") + "-container").find(".catatan.temp-order").val()
                };
            };

            var response = function(response) {

                if (response.success) {

                    $("#order-menu").children("tr#menu-row.highlight").find(".catatan.order").val(response.catatan);
                } else {
                    swal("Error", "Terjadi kesalahan dalam proses input Catatan Menu.", "error");
                }
            };

            action($(this), "Catatan Menu", onOpen, data, response);
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        } 
        
        return false;
    });
    
    $("#free-menu").on("click", function() {
        
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;        
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {
        
            swal({
                title: "Free menu untuk order ini?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(        
                function () {
                    var isFree = false;                    
                    
                    var jmlFreeMenu = parseFloat($("input#total-free-menu").val());
                    var jmlHargaTemp = 0

                    $("#order-menu").children("tr#menu-row.highlight").each(function() {                                                 
                                                                    
                        if (parseFloat($(this).find(".is-free-menu.order").val()) == 0) {
                                              
                            var discountType = $(this).find(".discount-type.order");
                            var discount = $(this).find(".discount.order");                            
                            var jumlah = $(this).find(".jumlah.order");
                            var harga = $(this).find(".harga-satuan.order"); 

                            var hargaTemp = 0;

                            if (discountType.val() == "Percent") {
                                hargaTemp = parseFloat(harga.val()) - Math.round(parseFloat(discount.val()) * 0.01 * parseFloat(harga.val()));
                            } else if (discountType.val() == "Value") {
                                hargaTemp = parseFloat(harga.val()) - parseFloat(discount.val());
                            }                            

                            jmlHargaTemp += hargaTemp * parseFloat(jumlah.val());   
                            
                            var jmlHarga = parseFloat(harga.val()) * parseFloat(jumlah.val());

                            jmlFreeMenu +=  jmlHarga;                                                           
                            
                            setOrderId($(this).find(".order-id.order").val(), $(this));
                            
                        } else {
                            isFree = true;
                            return false;
                        }
                    });
                    
                    var totalHarga = parseFloat($(".jumlah-harga.session").val()) - jmlHargaTemp;
                                        
                    if (isFree) {
                        swal("Error", "Salah satu order atau lebih yang Anda pilih sudah dalam free menu.", "error");
                    } else {
                    
                        $.ajax({
                            cache: false,
                            dataType: "json",
                            type: "POST",
                            url: thisObj.attr("href"),
                            data: {
                                "sess_id": $(".sess-id.session").val(),
                                "jumlah_harga": totalHarga,
                                "order_id": orderId
                            },
                            beforeSend: function(xhr) {
                                $(".overlay").show();
                                $(".loading-img").show();
                            },
                            success: function(response) {
                                
                                if (response.success) {
                                
                                    $.each(trObj, function(i, val) {
                                    
                                        val.find(".discount.order").val(0);
                                        val.find(".discount-type.order").val("Percent");
                                        val.find(".is-free-menu.order").val(1);

                                        val.attr("class", "free-menu");

                                        val.find("#subtotal").children("#span-subtotal").html(parseFloat(val.find(".harga-satuan.order").val()) * parseFloat(val.find(".jumlah.order").val()));
                                        val.find("#subtotal").children("#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});

                                        val.find("#subtotal").children("#span-discount").children("#val-discount").html(0);
                                    });
                                
                                    $("input#total-free-menu").val(jmlFreeMenu);
                                    $("span#total-free-menu").html(jmlFreeMenu);
                                    $("span#total-free-menu").currency({' . Yii::$app->params['currencyOptions'] . '});                                   

                                    hitungTotal(totalHarga);
                                }

                                $(".overlay").hide();
                                $(".loading-img").hide();   
                            },
                            error: function (xhr, ajaxOptions, thrownError) {                     

                                $(".overlay").hide();
                                $(".loading-img").hide();

                                swal("Error", xhr.responseText, "error");
                            }
                        });                                                
                    }
                },
                function(dismiss) {

                }
            );
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        } 
        
        return false;
    });
    
    $("#void-menu").on("click", function() {
    
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {
        
            swal({
                title: "Void menu untuk order ini?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(
                function () {
                
                    var jmlVoid = parseFloat($("input#total-void").val());
                    var jmlHargaTemp = 0

                    $("#order-menu").children("tr#menu-row.highlight").each(function() {
                    
                        if (parseFloat($(this).find(".is-free-menu.order").val()) == 0) {
                        
                            var discountType = $(this).find(".discount-type.order");
                            var discount = $(this).find(".discount.order");                            
                            var jumlah = $(this).find(".jumlah.order");
                            var harga = $(this).find(".harga-satuan.order"); 

                            var hargaTemp = 0;

                            if (discountType.val() == "Percent") {
                                hargaTemp = parseFloat(harga.val()) - Math.round(parseFloat(discount.val()) * 0.01 * parseFloat(harga.val()));
                            } else if (discountType.val() == "Value") {
                                hargaTemp = parseFloat(harga.val()) - parseFloat(discount.val());
                            }                            

                            jmlHargaTemp += hargaTemp * parseFloat(jumlah.val());   
                            
                            var jmlHarga = parseFloat(harga.val()) * parseFloat(jumlah.val());

                            jmlVoid +=  jmlHarga;
                        }

                        setOrderId($(this).find(".order-id.order").val(), $(this));
                    });
                    
                    var totalHarga = parseFloat($(".jumlah-harga.session").val()) - jmlHargaTemp;
                                        
                    $.ajax({
                        cache: false,
                        dataType: "json",
                        type: "POST",
                        url: thisObj.attr("href"),
                        data: {
                            "sess_id": $(".sess-id.session").val(),
                            "jumlah_harga": totalHarga,
                            "order_id": orderId
                        },
                        beforeSend: function(xhr) {
                            $(".overlay").show();
                            $(".loading-img").show();
                        },
                        success: function(response) {

                            if (response.success) {

                                $.each(trObj, function(i, val) {

                                    val.find(".discount.order").val(0);
                                    val.find(".discount-type.order").val("Percent");
                                    val.find(".is-void.order").val(1);

                                    val.attr("class", "voided");

                                    val.find("#subtotal").children("#span-subtotal").html(parseFloat(val.find(".harga-satuan.order").val()) * parseFloat(val.find(".jumlah.order").val()));
                                    val.find("#subtotal").children("#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});

                                    val.find("#subtotal").children("#span-discount").children("#val-discount").html(0);
                                });

                                $("input#total-void").val(jmlVoid);
                                $("span#total-void").html(jmlVoid);
                                $("span#total-void").currency({' . Yii::$app->params['currencyOptions'] . '});                                   

                                hitungTotal(totalHarga);
                            }

                            $(".overlay").hide();
                            $(".loading-img").hide();   
                        },
                        error: function (xhr, ajaxOptions, thrownError) {                     

                            $(".overlay").hide();
                            $(".loading-img").hide();

                            swal("Error", xhr.responseText, "error");
                        }
                    });
                },
                function(dismiss) {

                }
            );
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        } 
        
        return false;
    });
    
    $("#discount-bill").on("click", function() {
    
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
    
        var onOpen = function() {
        
            $("#discount-bill-container").find(".discount-type.temp-session").val($(".discount-type.session").val());
            $("#discount-bill-container").find("#mtablesession-discount").val($(".discount.session").val());

            $("#discount-bill-container").find(".discount-type.temp-session").focus();
            
            $("#discount-bill-container").find(".discount-type-temp").iCheck({
                checkboxClass: "icheckbox_minimal-red",
                radioClass: "iradio_minimal-red"
            });
            
            if ($("#discount-bill-container").find(".discount-type.temp-session").val() == "Percent") {                     
                $("#discount-bill-container").find(".discount-type-temp#discount_type-percent").iCheck("check");
            } else if ($("#discount-bill-container").find(".discount-type.temp-session").val() == "Value") {              
                $("#discount-bill-container").find(".discount-type-temp#discount_type-value").iCheck("check");
            }
            
            var checkDiscountType = function(discountType) {
                
                if (discountType == "Percent") {
                    $("#discount-bill-container").find("#mtablesession-discount-disp").maskMoney(percentMaskMoney);
                } else if (discountType == "Value") {
                    $("#discount-bill-container").find("#mtablesession-discount-disp").maskMoney(valueMaskMoney);
                }
                
                $("#discount-bill-container").find("#mtablesession-discount-disp").maskMoney("mask", parseFloat($("#discount-bill-container").find("#mtablesession-discount").val()));
            };
            
            $("#discount-bill-container").find(".discount-type-temp").on("ifChecked", function(event) {                                    
            
                $("#discount-bill-container").find(".discount-type.temp-session").val($(this).val());
                
                checkDiscountType($(this).val());
            });            
            
            checkDiscountType($("#discount-bill-container").find(".discount-type.temp-session").val());
            
            $("#discount-bill-container").find("#mtablesession-discount-disp").on("change", function () {
                var numDecimal = $("#discount-bill-container").find("#mtablesession-discount-disp").maskMoney("unmasked")[0];
                $("#discount-bill-container").find("#mtablesession-discount").val(numDecimal);
                $("#discount-bill-container").find("#mtablesession-discount").trigger("change");
            });

            ' . $virtualKeyboard->keyboardNumeric('$("#discount-bill-container").find("#mtablesession-discount-disp")', true) . '
        };
        
        var data = function(thisObj) {
            
            return {
                "sess_id": $(".sess-id.session").val(),
                "discount_type": $("#" + thisObj.attr("id") + "-container").find(".discount-type.temp-session").val(),
                "discount": $("#" + thisObj.attr("id") + "-container").find("#mtablesession-discount").val()
            };
        };
        
        var response = function(response) {
        
            if (response.success) {
            
                $(".discount-type.session").val(response.discount_type);
                $(".discount.session").val(response.discount);
            
                var hargaDisc = 0; 
            
                if (response.discount_type == "Percent") {
                
                    hargaDisc = Math.round(parseFloat(response.discount) * 0.01 * parseFloat($(".jumlah-harga.session").val())); 
                    $("#discbill-text").html("(" + response.discount + "%)");
                } else if (response.discount_type == "Value") {
                
                    hargaDisc = parseFloat(response.discount); 
                    $("#discbill-text").html("");
                }
                
                $("#discbill").html(hargaDisc);
                $("#discbill").currency({' . Yii::$app->params['currencyOptions'] . '});
                    
                hitungTotal(parseFloat($(".jumlah-harga.session").val()));               
            } else {
                swal("Error", "Terjadi kesalahan dalam proses input Discount Bill.", "error");
            }
        };
        
        action($(this), "Discount Bill", onOpen, data, response);
    
        return false;
    });
    
    $("#discount-menu").on("click", function() {
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
    
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {
        
            var onOpen = function() {
            
                $("#discount-menu-container").find(".discount-type.temp-order").val($("#order-menu").children("tr#menu-row.highlight").find(".discount-type.order").val());
                $("#discount-menu-container").find("#mtableorder-discount").val($("#order-menu").children("tr#menu-row.highlight").find(".discount.order").val());

                $("#discount-menu-container").find(".discount-type.temp-order").focus();

                $("#discount-menu-container").find(".discount-type-temp").iCheck({
                    checkboxClass: "icheckbox_minimal-red",
                    radioClass: "iradio_minimal-red"
                });

                if ($("#discount-menu-container").find(".discount-type.temp-order").val() == "Percent") {                     
                    $("#discount-menu-container").find(".discount-type-temp#discount_type-percent").iCheck("check");
                } else if ($("#discount-menu-container").find(".discount-type.temp-order").val() == "Value") {              
                    $("#discount-menu-container").find(".discount-type-temp#discount_type-value").iCheck("check");
                }

                var checkDiscountType = function(discountType) {

                    if (discountType == "Percent") {
                        $("#discount-menu-container").find("#mtableorder-discount-disp").maskMoney(percentMaskMoney);
                    } else if (discountType == "Value") {
                        $("#discount-menu-container").find("#mtableorder-discount-disp").maskMoney(valueMaskMoney);
                    }

                    $("#discount-menu-container").find("#mtableorder-discount-disp").maskMoney("mask", parseFloat($("#discount-menu-container").find("#mtableorder-discount").val()));
                };

                $("#discount-menu-container").find(".discount-type-temp").on("ifChecked", function(event) {                                    

                    $("#discount-menu-container").find(".discount-type.temp-order").val($(this).val());

                    checkDiscountType($(this).val());
                });            

                checkDiscountType($("#discount-menu-container").find(".discount-type.temp-order").val());

                $("#discount-menu-container").find("#mtableorder-discount-disp").on("change", function () {
                    var numDecimal = $("#discount-menu-container").find("#mtableorder-discount-disp").maskMoney("unmasked")[0];
                    $("#discount-menu-container").find("#mtableorder-discount").val(numDecimal);
                    $("#discount-menu-container").find("#mtableorder-discount").trigger("change");
                });

                ' . $virtualKeyboard->keyboardNumeric('$("#discount-menu-container").find("#mtableorder-discount-disp")', true) . '
            };

            var data = function(thisObj) {
            
                var jmlHarga = 0;                
            
                $("#order-menu").children("tr#menu-row.highlight").each(function() {                                              
                    
                    if (parseFloat($(this).find(".is-free-menu.order").val()) == 0) {
                        
                        var discountType = $("#discount-menu-container").find(".discount-type.temp-order");
                        var discount = $("#discount-menu-container").find("#mtableorder-discount");                            
                        var jumlah = $(this).find(".jumlah.order");
                        var harga = $(this).find(".harga-satuan.order"); 

                        var hargaAkhir = 0;
                        var hargaAwal = 0;

                        if (discountType.val() == "Percent") {
                            hargaAkhir = parseFloat(harga.val()) - Math.round(parseFloat(discount.val()) * 0.01 * parseFloat(harga.val()));
                        } else if (discountType.val() == "Value") {
                            hargaAkhir = parseFloat(harga.val()) - parseFloat(discount.val());                            
                        }
                        
                        if ($(this).find(".discount-type.order").val() == "Percent") {
                            hargaAwal = parseFloat(harga.val()) - Math.round(parseFloat($(this).find(".discount.order").val()) * 0.01 * parseFloat(harga.val()));
                        } else if ($(this).find(".discount-type.order").val() == "Value") {
                            hargaAwal = parseFloat(harga.val()) - parseFloat($(this).find(".discount.order").val());
                        }

                        jmlHarga += (hargaAwal - hargaAkhir) * parseFloat(jumlah.val());
                    }

                    setOrderId($(this).find(".order-id.order").val(), $(this));
                });                
                
                var totalHarga = parseFloat($(".jumlah-harga.session").val()) - jmlHarga;

                return {                    
                    "sess_id": $(".sess-id.session").val(),
                    "jumlah_harga": totalHarga,
                    "order_id": orderId,
                    "discount_type": $("#" + thisObj.attr("id") + "-container").find(".discount-type.temp-order").val(),
                    "discount": $("#" + thisObj.attr("id") + "-container").find("#mtableorder-discount").val()
                };
            };

            var response = function(response) {

                if (response.success) {
                    
                    $.each(trObj, function(i, val) {

                        val.find(".discount.order").val(response.discount);
                        val.find(".discount-type.order").val(response.discount_type);
                        
                        var harga = 0;
                        
                        if (val.find(".discount-type.order").val() == "Percent") {
                            harga = parseFloat(val.find(".harga-satuan.order").val()) - Math.round(parseFloat(val.find(".discount.order").val()) * 0.01 * parseFloat(val.find(".harga-satuan.order").val()));                            
                        } else if (val.find(".discount-type.order").val() == "Value") {
                            harga = parseFloat(val.find(".harga-satuan.order").val()) - parseFloat(val.find(".discount.order").val());                            
                        }

                        val.find("#subtotal").children("#span-subtotal").html(harga * parseFloat(val.find(".jumlah.order").val()));
                        val.find("#subtotal").children("#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});

                        val.find("#subtotal").children("#span-discount").children("#val-discount").html(val.find(".discount.order").val());
                        
                        if (val.find(".discount-type.order").val() == "Percent") {

                        } else if (val.find(".discount-type.order").val() == "Value") {
                            val.find("#subtotal").children("#span-discount").children("#val-discount").currency({' . Yii::$app->params['currencyOptions'] . '});
                        }
                    });                              

                    hitungTotal(parseFloat(response.jumlah_harga));
                } else {
                    swal("Error", "Terjadi kesalahan dalam proses input Discount Menu.", "error");
                }
            };

            action($(this), "Discount Menu", onOpen, data, response);
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        } 
        
        return false;
    });        
    
    $("#close-table").on("click", function() {
    
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
    
        var onOpen = function() {
        
            $("#close-table-container").find(".catatan.temp-session").val($(".catatan.session").val());

            $("#close-table-container").find(".catatan.temp-session").focus();

            ' . $virtualKeyboard->keyboardQwerty('$("#close-table-container").find(".catatan.temp-session")', true) . '
        };
        
        var data = function(thisObj) {
            
            return {
                "sess_id": $(".sess-id.session").val(),
                "catatan": $("#" + thisObj.attr("id") + "-container").find(".catatan.temp-session").val(),
            };
        };
        
        var response = function(response) {
        
            if (response.success) {
                $("#back").trigger("click");
            } else {
                swal("Error", "Terjadi kesalahan dalam proses input Close Table.", "error");
            }
        };
        
        action($(this), "Close Table", onOpen, data, response);
        
        return false;
    });
    
    $("#split").on("click", function() {            
    
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
    
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {                                    
            
            swal({
                title: "Split untuk order ini?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(
                function () {

                    $("#order-menu").children("tr#menu-row.highlight").each(function() {

                        setOrderId($(this).find(".order-id.order").val(), $(this));
                    });

                    $.ajax({
                        cache: false,
                        dataType: "json",
                        type: "POST",
                        url: thisObj.attr("href"),
                        data: {
                            "sess_id": $(".sess-id.session").val(),
                            "mtable_id": $(".mtable-id.session").val(),
                            "jumlah_tamu": $(".jumlah-tamu.session").val(),
                            "nama_tamu": $(".nama-tamu.session").val(),
                            "order_id": orderId
                        },
                        beforeSend: function(xhr) {
                            $(".overlay").show();
                            $(".loading-img").show();
                        },
                        success: function(response) {

                            if (response.success) {
                                
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    url: $("#after-split").val(),
                                    beforeSend: function(xhr) {
                                        $(".overlay").show();
                                        $(".loading-img").show();
                                    },
                                    success: function(response) {
                                        $("#home-content").html(response);

                                        $(".overlay").hide();
                                        $(".loading-img").hide();                
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {     
                                        $("#home-content").html(xhr.responseText);

                                        $(".overlay").hide();
                                        $(".loading-img").hide();
                                    }
                                });
                            }

                            $(".overlay").hide();
                            $(".loading-img").hide();   
                        },
                        error: function (xhr, ajaxOptions, thrownError) {                     

                            $(".overlay").hide();
                            $(".loading-img").hide();

                            swal("Error", xhr.responseText, "error");
                        }
                    });
                },
                function(dismiss) {

                }
            );
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        } 
        
        return false;
    });
    
    $("#queue-menu").on("click", function() {
    
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {                                    
            
            swal({
                title: "Antrikan menu untuk order ini?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(
                function () {
                    
                    $("#order-menu").children("tr#menu-row.highlight").each(function() {
                        
                        var item = {};
                        item["order_id"] = $(this).find(".order-id.order").val();
                        item["menu_id"] = $(this).find(".menu-id.order").val();
                        item["jumlah"] = $(this).find(".jumlah.order").val();
                        item["catatan"] = $(this).find(".catatan.order").val();                        
                        
                        setOrderId(item, $(this));
                    });
                    
                    $.ajax({
                        cache: false,
                        dataType: "json",
                        type: "POST",
                        url: thisObj.attr("href"),
                        data: {
                            "order_id": orderId
                        },
                        beforeSend: function(xhr) {
                            $(".overlay").show();
                            $(".loading-img").show();
                        },
                        success: function(response) {

                            if (response.success) {
                                
                                $.each(trObj, function(i, val) {
                                    
                                    val.find("#badge-queue").html("<div class=\"badge bg-important\"><i class=\"fa fa-thumbs-o-up\" style=\"color:#FFF\"></i></div>");
                                });
                                
                                getDateTime();
                                var header = "";
                                header += "\n" + $("#struk-order-header").val() + "\n";
                                header += separatorPrint(paperWidth, "-") + "\n";
                                header += "Tgl/Jam Print" + separatorPrint(spaceLength - "Tgl/Jam Print".length) + ": " + datetime + "\n";
                                header += separatorPrint(paperWidth, "-") + "\n";
                                header += "Meja" + separatorPrint(spaceLength - "Meja".length) + ": " + $(".mtable-nama.session").val() + "\n";
                                header += "Tamu" + separatorPrint(spaceLength - "Tamu".length) + ": " + $(".nama-tamu.session").val() + "\n";
                                header += "Tgl/Jam Open" + separatorPrint(spaceLength - "Tgl/Jam Open".length) + ": " + $(".open-table-at.session").val() + "\n";
                                header += "Kasir" + separatorPrint(spaceLength - "Kasir".length) + ": " + $("#user-active").val() + "\n";                                

                                header += separatorPrint(paperWidth, "-") + "\n";
                                header += separatorPrint(14) + "Menu Pesanan \n";                        
                                header += separatorPrint(paperWidth, "-") + "\n";           


                                var content = {};

                                $.each(trObj, function(i, val) {

                                    val.find(".printer.order").each(function() {
                                        var printer = $(this).val();

                                        if (content[printer] === undefined)
                                            content[printer] = "";

                                        var menu = val.find("#menu").children("span").html();
                                        var qty = val.find("#qty").children("span").html();
                                        var separatorLength = paperWidth - (menu.length + qty.length);                                        

                                        content[printer] += qty + separatorPrint(separatorLength) + menu + "\n";

                                        var catatan = val.find(".catatan.order").val();                                     
                                        content[printer] += catatan + "\n";                      
                                    });
                                });

                                var footer = "";
                                footer += separatorPrint(paperWidth, "-") + "\n";
                                footer += $("#struk-order-footer").val() + "\n";

                                printContentToServer(header, footer, content);
                            } else {
                                
                                swal("Error", "Terjadi kesalahan dalam proses antri menu. Terdapat menu yang sudah masuk antrian.", "error");
                            }

                            $(".overlay").hide();
                            $(".loading-img").hide();   
                        },
                        error: function (xhr, ajaxOptions, thrownError) {                     

                            $(".overlay").hide();
                            $(".loading-img").hide();

                            swal("Error", xhr.responseText, "error");
                        }
                    });
                },
                function(dismiss) {

                }
            );
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        }
        
        return false;
    });
    
    $("#print-bill").on("click", function() {
    
        var thisObj = $(this);
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Cetak tagihan tidak bisa dilakukan karena tagihan sudah dicetak.", "error");
            return false;
        }

        swal({
            title: "Cetak tagihan?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes"
        }).then(
            function () {
                $.ajax({
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    url: thisObj.attr("href"),
                    data: {
                        "sess_id": $(".sess-id.session").val()
                    },
                    beforeSend: function(xhr) {
                        $(".overlay").show();
                        $(".loading-img").show();
                    },
                    success: function(response) {

                        if (response.success) {

                            $(".bill-printed.session").val(1);
                            $("#unlock-bill").removeClass("hidden");
                            
                            getDateTime();
                            var text = "";
                            var totalQty = 0;
                            var totalSubtotal = 0;
                            
                            text += "\n" + $("#struk-invoice-header").val() + "\n";
                            text += separatorPrint(paperWidth, "-") + "\n";
                            text += "Tgl/Jam Print" + separatorPrint(spaceLength - "Tgl/Jam Print".length) + ": " + datetime + "\n";
                            text += separatorPrint(paperWidth, "-") + "\n";
                            text += "Meja" + separatorPrint(spaceLength - "Meja".length) + ": " + $(".mtable-nama.session").val() + "\n";
                            text += "Tamu" + separatorPrint(spaceLength - "Tamu".length) + ": " + $(".nama-tamu.session").val() + "\n";
                            text += "Tgl/Jam Open" + separatorPrint(spaceLength - "Tgl/Jam Open".length) + ": " + $(".open-table-at.session").val() + "\n";
                            text += "Kasir" + separatorPrint(spaceLength - "Kasir".length) + ": " + $("#user-active").val() + "\n";

                            text += separatorPrint(paperWidth, "-") + "\n"
                            text += separatorPrint(16) + "Tagihan \n";                        
                            text += separatorPrint(paperWidth, "-") + "\n"                        

                            $("#order-menu").children("tr#menu-row").each(function() {
                                  
                                var discountType = $(this).find(".discount-type.order").val();
                                var discount = parseFloat($(this).find(".discount.order").val());
                                var harga = parseFloat($(this).find(".harga-satuan.order").val());
                                var qty = parseFloat($(this).find(".jumlah.order").val());

                                var menu = $(this).find("#menu").children("span").html().replace("<i class=\"fa fa-plus\" style=\"color:green\"></i>", "(+) ");                                

                                var textDisc = "";

                                if ($(this).find(".is-void.order").val() == 1) {
                                    textDisc = "Void";
                                } else if ($(this).find(".is-free-menu.order").val() == 1) {
                                    textDisc = "Free";
                                } else {
                                    if (discount > 0) {
                                    
                                        if (discountType == "Percent") {
                                        
                                            harga = harga - Math.round(discount * 0.01 * harga);
                                            textDisc = "Disc: " + discount + "%";
                                        } else if (discountType == "Value") {
                                        
                                            harga = harga - discount; 

                                            var discSpan = $("<span>").html(discount);
                                            discSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                            textDisc = "Disc: " + discSpan.html();
                                        }
                                    }
                                }

                                var jmlHarga = harga * qty;                                            

                                var hargaSpan = $("<span>").html(harga);
                                hargaSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                                var subtotal = jmlHarga;
                                var subtotalSpan = $("<span>").html(subtotal);
                                subtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                                totalQty += qty;
                                totalSubtotal += subtotal;

                                var line2 = qty + " X " + hargaSpan.html();                        

                                text += menu + separatorPrint(paperWidth - (menu + textDisc).length) + textDisc + "\n";                    
                                text += line2 + separatorPrint(paperWidth - (line2 + subtotalSpan.html()).length) + subtotalSpan.html() + "\n";
                            });

                            text += separatorPrint(paperWidth, "-") + "\n";

                            var totalFreeMenu = parseFloat($("input#total-free-menu").val());
                            var totalFreeMenuSpan = $("<span>").html(totalFreeMenu);
                            totalFreeMenuSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                            text += "Free Menu" + separatorPrint(paperWidth - ("Free Menu" + "(" + totalFreeMenuSpan.html() + ")").length) + "(" + totalFreeMenuSpan.html() + ")" + "\n";

                            var totalVoid = parseFloat($("input#total-void").val());
                            var totalVoidSpan = $("<span>").html(totalVoid);
                            totalVoidSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                            text += "Void Menu" + separatorPrint(paperWidth - ("Void Menu" + "(" + totalVoidSpan.html() + ")").length) + "(" + totalVoidSpan.html() + ")" + "\n";

                            totalSubtotal -= (totalFreeMenu + totalVoid);

                            var totalSubtotalSpan = $("<span>").html(totalSubtotal);
                            totalSubtotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});

                            var scp = hitungServiceChargePajak(totalSubtotal, $(".service-charge.session").val(), $(".pajak.session").val());

                            var scText = "";
                            var serviceCharge = 0;
                            if (parseFloat($(".service-charge.session").val()) > 0) {
                                serviceCharge = scp["serviceCharge"];
                                var serviceChargeSpan = $("<span>").html(serviceCharge);
                                serviceChargeSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                var sc = "Service Charge (" + $(".service-charge.session").val() + "%)";

                                scText = sc + separatorPrint(paperWidth - (sc + serviceChargeSpan.html()).length) + serviceChargeSpan.html() +"\n";
                            }

                            var pjkText = "";
                            var pajak = 0;
                            if (parseFloat($(".pajak.session").val()) > 0) {
                                pajak = scp["pajak"];
                                var pajakSpan = $("<span>").html(pajak);
                                pajakSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                                var pjk = "Pajak (" + $(".pajak.session").val() + "%)";

                                pjkText = pjk + separatorPrint(paperWidth - (pjk + pajakSpan.html()).length) + pajakSpan.html() +"\n";
                            }

                            var discBill = hitungDiscBill();
                            var discBillSpan = $("<span>").html(discBill);
                            discBillSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});
                            discBillSpan.html("(" + discBillSpan.html() + ")");

                            var grandTotal = totalSubtotal + serviceCharge + pajak - hitungDiscBill();
                            var grandTotalSpan = $("<span>").html(grandTotal);
                            grandTotalSpan.currency({' . Yii::$app->params['currencyOptionsPrint'] . '});                        

                            text += separatorPrint(paperWidth, "-") + "\n";   

                            text += "Total item" + separatorPrint(paperWidth - ("Total item" + totalQty).length) + totalQty +"\n";
                            text += "Total" + separatorPrint(paperWidth - ("Total" + totalSubtotalSpan.html()).length) + totalSubtotalSpan.html() +"\n";
                            text += scText;
                            text += pjkText;
                            text += "Discount Bill" + separatorPrint(paperWidth - ("Discount Bill" + discBillSpan.html()).length) + discBillSpan.html() +"\n";

                            text += separatorPrint(paperWidth, "-") + "\n"; 

                            text += "Grand Total" + separatorPrint(paperWidth - ("Grand Total" + grandTotalSpan.html()).length) + grandTotalSpan.html() +"\n";

                            text += separatorPrint(paperWidth, "-") + "\n";         

                            text += $("textarea#struk-invoice-footer").val() + "\n";                    

                            var content = [];

                            $("input#printerKasir").each(function() {
                                content[$(this).val()] = text;
                            });

                            printContentToServer("", "", content, false);
                        }

                        $(".overlay").hide();
                        $(".loading-img").hide();   
                    },
                    error: function (xhr, ajaxOptions, thrownError) {                     

                        $(".overlay").hide();
                        $(".loading-img").hide();

                        swal("Error", xhr.responseText, "error");
                    }
                });
            },
            function(dismiss) {

            }
        );
        
        return false;
    });
    
    $("#unlock-bill").on("click", function() {
    
        var thisObj = $(this);
    
        swal({
            title: "Unlock tagihan?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes"
        }).then(
            function () {
                $.ajax({
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    url: thisObj.attr("href"),
                    data: {
                        "sess_id": $(".sess-id.session").val()
                    },
                    beforeSend: function(xhr) {
                        $(".overlay").show();
                        $(".loading-img").show();
                    },
                    success: function(response) {

                        if (response.success) {
                            
                            $(".bill-printed.session").val(0);
                            thisObj.addClass("hidden");                                                        
                        }

                        $(".overlay").hide();
                        $(".loading-img").hide();   
                    },
                    error: function (xhr, ajaxOptions, thrownError) {                     

                        $(".overlay").hide();
                        $(".loading-img").hide();

                        swal("Error", xhr.responseText, "error");
                    }
                });
            },
            function(dismiss) {

            }
        );
        
        return false;
    });
    
    $("#cashdrawer").on("click", function() {
        
        var thisObj = $(this);
        
        $.ajax({
            cache: false,
            dataType: "json",
            type: "POST",
            url: thisObj.attr("href"),
            data: {
                "sess_id": $(".sess-id.session").val()
            },
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {

                if (response.success) {
                
                    content = [];
                    $("input#printerKasir").each(function() {
                        content[$(this).val()] = "";
                    });

                    printContentToServer("", "", content, true);
                }

                $(".overlay").hide();
                $(".loading-img").hide();   
            },
            error: function (xhr, ajaxOptions, thrownError) {                     

                $(".overlay").hide();
                $(".loading-img").hide();

                swal("Error", xhr.responseText, "error");
            }
        });
        
        return false;
    });
    
    $("#transfer-table").on("click", function() {
        
        var thisObj = $(this);
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        swal({
            title: "Transfer Meja",            
            width: "50%",
            html: 
                "<div id=\"container-table-list\">" +
                    "<div id=\"content\"><br><br><br><br></div>" +
                    "<div class=\"overlay\" style=\"display: none; position: absolute\"></div>" +
                    "<div class=\"loading-img\" style=\"display: none; position: absolute\"></div>" +
                "</div>",
            showCancelButton: true,
            onOpen: function () {                                
                
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/table-category']) . '",
                    beforeSend: function(xhr) {
                        $("#container-table-list").children(".overlay").show();
                        $("#container-table-list").children(".loading-img").show();
                    },
                    success: function(response) {
                        $("#container-table-list").children("#content").html(response);

                        $("#container-table-list").children(".overlay").hide();
                        $("#container-table-list").children(".loading-img").hide();
                    }
                });
                                
            }
        }).then(        
            function() {                            
                
                $.ajax({
                    cache: false,
                    dataType: "json",
                    type: "POST",
                    url: thisObj.attr("href"),
                    data: {
                        "sess_id": $(".sess-id.session").val(),
                        "mtable_id": $("#container-table-list").find("input.table:checked").val()
                    },
                    beforeSend: function(xhr) {
                        $(".overlay").show();
                        $(".loading-img").show();
                    },
                    success: function(response) {

                        $(".overlay").hide();
                        $(".loading-img").hide();   

                        if (response.success) {

                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: response.open_table,
                                beforeSend: function(xhr) {
                                    $(".overlay").show();
                                    $(".loading-img").show();
                                },
                                success: function(response) {
                                    $("#home-content").html(response);

                                    $(".overlay").hide();
                                    $(".loading-img").hide();         
                                    
                                    swal("Transfer Meja", "Proses transfer meja telah berhasil dilakukan.", "success");
                                },
                                error: function (xhr, ajaxOptions, thrownError) {     
                                    $("#home-content").html(xhr.responseText);

                                    $(".overlay").hide();
                                    $(".loading-img").hide();
                                }
                            });
                        } else {
                            swal("Error", "Terjadi kesalahan dalam proses transfer meja.", "error");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {                     

                        $(".overlay").hide();
                        $(".loading-img").hide();

                        swal("Error", xhr.responseText, "error");
                    }
                });
            },
            function(dismiss) {
                                
            }
        );
        
        return false;
    });
    
    $("#transfer-menu").on("click", function() {
        
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {                                    
            
            swal({
                title: "Transfer Menu",            
                width: "50%",
                html: 
                    "<div id=\"container-table-list\">" +
                        "<div id=\"content\"><br><br><br><br></div>" +
                        "<div class=\"overlay\" style=\"display: none; position: absolute\"></div>" +
                        "<div class=\"loading-img\" style=\"display: none; position: absolute\"></div>" +
                    "</div>",
                showCancelButton: true,
                onOpen: function () {                                

                    $.ajax({
                        cache: false,
                        type: "POST",
                        url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/table-category', 'isOpened' => true]) . '",
                        beforeSend: function(xhr) {
                            $("#container-table-list").children(".overlay").show();
                            $("#container-table-list").children(".loading-img").show();
                        },
                        success: function(response) {
                            $("#container-table-list").children("#content").html(response);

                            $("#container-table-list").children(".overlay").hide();
                            $("#container-table-list").children(".loading-img").hide();
                        }
                    });

                }
            }).then(
                function () {                                    
                    
                    var mtableId = $("#container-table-list").find("input.table:checked").val();
                    
                    var execute = function() {
                    
                        $("#order-menu").children("tr#menu-row.highlight").each(function() {                                           
                        
                            setOrderId($(this).find(".order-id.order").val(), $(this));
                        });

                        $.ajax({
                            cache: false,
                            dataType: "json",
                            type: "POST",
                            url: thisObj.attr("href"),
                            data: {
                                "sess_id": $(".sess-id.session").val(),
                                "mtable_id": mtableId,
                                "order_id": orderId
                            },
                            beforeSend: function(xhr) {
                                $(".overlay").show();
                                $(".loading-img").show();
                            },
                            success: function(response) {

                                $(".overlay").hide();
                                $(".loading-img").hide();   

                                if (response.success) {

                                    $.ajax({
                                        cache: false,
                                        type: "POST",
                                        url: response.open_table,
                                        beforeSend: function(xhr) {
                                            $(".overlay").show();
                                            $(".loading-img").show();
                                        },
                                        success: function(response) {
                                            $("#home-content").html(response);

                                            $(".overlay").hide();
                                            $(".loading-img").hide();         

                                            swal("Transfer Menu", "Proses transfer menu telah berhasil dilakukan.", "success");
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {     
                                            $("#home-content").html(xhr.responseText);

                                            $(".overlay").hide();
                                            $(".loading-img").hide();
                                        }
                                    });
                                } else {
                                    swal("Error", "Terjadi kesalahan dalam proses transfer menu.", "error");
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {                     

                                $(".overlay").hide();
                                $(".loading-img").hide();

                                swal("Error", xhr.responseText, "error");
                            }
                        });
                    };
                    
                    if ($("#container-table-list").find(".splitted#" + mtableId).val() == 1) {                                            
                    
                        swal({
                            title: "Transfer Menu",
                            text: "Meja ini dalam keadaan split. Tetap ingin melanjutkan transfer menu?",
                            type: "warning",
                            showCancelButton: true
                        }).then(
                            function () {
                                execute();
                            },
                            function(dismiss) {
                                
                            }
                        );
                    } else {
                        execute();
                    }
                },
                function(dismiss) {

                }
            );
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        }
        
        return false;
    });
    
    $("#join-table").on("click", function() {
    
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        swal({
            title: "Gabung Meja",            
            width: "50%",
            html: 
                "<div id=\"container-table-list\">" +
                    "<div id=\"content\"><br><br><br><br></div>" +
                    "<div class=\"overlay\" style=\"display: none; position: absolute\"></div>" +
                    "<div class=\"loading-img\" style=\"display: none; position: absolute\"></div>" +
                "</div>",
            showCancelButton: true,
            onOpen: function () {                                

                $.ajax({
                    cache: false,
                    type: "POST",
                    url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'data/table-category', 'isOpened' => true]) . '",
                    beforeSend: function(xhr) {
                        $("#container-table-list").children(".overlay").show();
                        $("#container-table-list").children(".loading-img").show();
                    },
                    success: function(response) {
                        $("#container-table-list").children("#content").html(response);

                        $("#container-table-list").children(".overlay").hide();
                        $("#container-table-list").children(".loading-img").hide();
                    }
                });

            }
        }).then(
            function () {
            
                var mtableId = $("#container-table-list").find("input.table:checked").val();
                
                if ($("#container-table-list").find(".splitted#" + mtableId).val() == 0) {
                
                    $("#order-menu").children("tr#menu-row").each(function() {                                           
                        
                        setOrderId($(this).find(".order-id.order").val(), $(this));
                    });
            
                    $.ajax({
                        cache: false,
                        dataType: "json",
                        type: "POST",
                        url: thisObj.attr("href"),
                        data: {
                            "sess_id": $(".sess-id.session").val(),
                            "mtable_id": $("#container-table-list").find("input.table:checked").val(),
                            "order_id": orderId
                        },
                        beforeSend: function(xhr) {
                            $(".overlay").show();
                            $(".loading-img").show();
                        },
                        success: function(response) {

                            $(".overlay").hide();
                            $(".loading-img").hide();   

                            if (response.success) {

                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    url: response.open_table,
                                    beforeSend: function(xhr) {
                                        $(".overlay").show();
                                        $(".loading-img").show();
                                    },
                                    success: function(response) {
                                        $("#home-content").html(response);

                                        $(".overlay").hide();
                                        $(".loading-img").hide();         

                                        swal("Transfer Meja", "Proses gabung meja telah berhasil dilakukan.", "success");
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {     
                                        $("#home-content").html(xhr.responseText);

                                        $(".overlay").hide();
                                        $(".loading-img").hide();
                                    }
                                });
                            } else {
                                swal("Error", "Terjadi kesalahan dalam proses gabung meja." + response.message, "error");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {                     

                            $(".overlay").hide();
                            $(".loading-img").hide();

                            swal("Error", xhr.responseText, "error");
                        }
                    });
                } else {
                    swal("Error", "Tagihan di meja dalam keadaan split, jadi tidak bisa dilakukan proses gabung meja", "error");
                }
            },
            function(dismiss) {

            }
        );
    
        return false;
    });
    
    $("#add.add-condiment").on("click", function() {
    
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
        
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) {
        
            var row = $("#order-menu").children("tr#menu-row.highlight");
        
            if (row.length == 1) {
            
                if (row.find(".parent-id.order").val() == "") {
                    
                    $.ajax({
                        cache: false,
                        type: "POST",
                        data: {
                            "parent_id": row.find(".menu-id.order").val(),
                            "order_parent_id": row.find(".order-id.order").val(),
                        },
                        url: thisObj.attr("href"),
                        beforeSend: function(xhr) {
                            $(".overlay").show();
                            $(".loading-img").show();
                        },
                        success: function(response) {
                            $("#menu-container").html(response);

                            $("input#valAddCondiment").val(row.find("input.inputId").val());
                            $("a.add-condiment").toggle();
                            
                            $("#load-menu-back").css("display", "none");

                            $(".overlay").hide();
                            $(".loading-img").hide();
                        }
                    });
                } else {
                    swal("Error", "Tidak bisa menambahkan condiment ke dalam condiment.", "error");
                }
            } else {
                swal("Error", "Hanya boleh memilih satu menu order untuk ditambahkan condiment.", "error");
            }
        
        } else {
            swal("Error", "Tidak ada order yang dipilih.", "error");
        }
        
        return false;
    });
    
    $("#cancel.add-condiment").on("click", function() {
    
        loadMenuCategory();
        
        $("input#valAddCondiment").val("");
        
        $("#order-menu").children("tr#menu-row.highlight").removeClass("highlight");
        
        $("a.add-condiment").toggle();
        
        return false;
    });
    
    $("#payment").on("click", function() {
    
        if ($("#order-menu").children("tr#menu-row").length > 0) {
            
            $.ajax({
                cache: false,
                type: "POST",
                url: $(this).attr("href"),
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    $("#home-content").html(response);

                    $(".overlay").hide();
                    $(".loading-img").hide();                
                },
                error: function (xhr, ajaxOptions, thrownError) {     
                    swal("Error", xhr.responseText, "error");

                    $(".overlay").hide();
                    $(".loading-img").hide();
                }
            });            
        } else {
            swal("Error", "Tidak bisa melanjutkan ke payment. Karena tidak ada order.", "error");
        }
        
        return false;
    });
';

$jscriptExe = '
    $("#back").on("click", function() {
    
        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).attr("href"),
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#home-content").html(response);
                
                $(".overlay").hide();
                $(".loading-img").hide();                
            },
            error: function (xhr, ajaxOptions, thrownError) {     
                swal("Error", xhr.responseText, "error");
                
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });        
    
    $(document).off("click");
    $(document).on("click", "#order-menu > tr#menu-row", function(event) {
    
        if ($(this).hasClass("highlight")) {
        
            $(this).removeClass("highlight");
            
            if (parseFloat($(this).find(".is-free-menu.order").val()) == 1) {
            
                $(this).addClass("free-menu");
            }
            
        } else if ($(this).hasClass("free-menu")) {
        
            $(this).removeClass("free-menu");
            $(this).addClass("highlight");            
        } else if (!$(this).hasClass("voided")) {
        
            $(this).addClass("highlight");
        }
    });
    
    $(".qty").on("click", function(event) {
    
        var thisObj = $(this);
        
        orderId = [];
        trObj = [];
        i = 0;
    
        if ($(".bill-printed.session").val() == 1) {
            swal("Error", "Detail order tidak bisa diubah karena tagihan sudah dicetak.", "error");
            return false;
        }
        
        if ($("#order-menu").children("tr#menu-row").hasClass("highlight")) { 
        
            var jmlHarga = 0;

            $("#order-menu").children("tr#menu-row.highlight").each(function() {

                var qty = 0;
                var addedQty = 0;
                var hargaTemp = 0;            

                var discountType = $(this).find(".discount-type.order");
                var discount = $(this).find(".discount.order");                            
                var jumlah = $(this).find(".jumlah.order");
                var harga = $(this).find(".harga-satuan.order");

                if (thisObj.attr("id") == "qty-plus") {
                    qty = parseFloat($(this).find(".jumlah.order").val()) + 1;
                } else if (thisObj.attr("id") == "qty-minus") {
                    qty = parseFloat($(this).find(".jumlah.order").val()) - 1;
                }
                
                if ($(this).find(".is-free-menu.order").val() == 0) {

                    addedQty = qty - parseFloat(jumlah.val());

                    if (discountType.val() == "Percent") {
                        hargaTemp = parseFloat(harga.val()) - Math.round(parseFloat(discount.val()) * 0.01 * parseFloat(harga.val()));
                    } else if (discountType.val() == "Value") {
                        hargaTemp = parseFloat(harga.val()) - parseFloat(discount.val());
                    }                                        

                    jmlHarga += hargaTemp * addedQty;
                }

                var item = {};
                item["order_id"] = $(this).find(".order-id.order").val();
                item["jumlah"] = qty;                      

                setOrderId(item, $(this));
            });

            $.ajax({
                cache: false,
                dataType: "json",
                type: "POST",
                url: thisObj.attr("href"),
                data: {
                    "sess_id": $(".sess-id.session").val(),
                    "jumlah_harga": parseFloat($(".jumlah-harga.session").val()) + jmlHarga,
                    "order_id": orderId
                },
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {

                    if (response.success) {
                    
                        $.each(trObj, function(i, val1) {
                        
                            $.each(response.order, function(j, val2) {
                            
                                if (val1.find(".order-id.order").val() == val2.id) {
                                
                                    val1.find(".jumlah.order").val(val2.jumlah);
                                    val1.find("#qty").children("span").first().html(val2.jumlah);
                                    
                                    val1.find("#span-subtotal").html(val2.jumlah_harga);
                                    val1.find("#span-subtotal").currency({' . Yii::$app->params['currencyOptions'] . '});                                                                            
                                }
                            });
                        });
                        
                        hitungTotal(parseFloat(response.jumlah_harga));

                    } else {
                        swal("Error", "Perubahan jumlah order tidak bisa dilakukan. Harap cek order." + response.message, "error");
                    }

                    $(".overlay").hide();
                    $(".loading-img").hide();   
                },
                error: function (xhr, ajaxOptions, thrownError) {                     

                    $(".overlay").hide();
                    $(".loading-img").hide();

                    swal("Error", xhr.responseText, "error");
                }
            });
        }
        
        return false;
    });
    
    $("#select-all").on("click", function(event) {
    
        $("#order-menu").children("tr#menu-row").each(function() {
        
            if ($(this).hasClass("free-menu")) {
            
                $(this).removeClass("free-menu");
                $(this).addClass("highlight");            
            } else if (!$(this).hasClass("voided")) {
            
                $(this).addClass("highlight");
            }
        });
    });
    
    $("#unselect-all").on("click", function(event) {
    
        $("#order-menu").children("tr#menu-row").each(function() {
        
            if ($(this).hasClass("highlight")) {
            
                $(this).removeClass("highlight");

                if (parseFloat($(this).find(".is-free-menu.order").val()) == 1) {
                
                    $(this).addClass("free-menu");
                }
            }
        });
    });
    
    loadMenuCategory();
    
    $("#search-menu").on("change", function(event) {
        searchMenu($(this).val());
    });
    
    $("#cancel-search-menu").on("click", function(event) {
        loadMenuCategory();
        $("#search-menu").val("");
    });
';

$this->registerJs($jscript . $jscriptInit . $jscriptAction . $jscriptExe); ?>