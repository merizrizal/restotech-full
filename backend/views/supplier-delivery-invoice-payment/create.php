<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use kartik\date\DatePicker;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\DynamicTable;
use restotech\standard\backend\components\NotificationDialog;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SupplierDeliveryInvoicePayment */

yii\widgets\MaskedInputAsset::register($this);

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) : 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

$dynamicTableSDInvoicePayment = new DynamicTable([
    'model' => $modelSDInvoicePayment,
    'tableFields' => [
        'date:date',
        'jumlah_bayar:currency',
    ],
    'dataProvider' => $dataProviderSDInvoicePayment,
    'title' => 'Histori Pembayaran',
    'columnClass' => 'col-sm-8 col-sm-offset-2'
]);

$this->title = 'Pembayaran Penerimaan PO';
$this->params['breadcrumbs'][] = ['label' => 'Invoice Penerimaan PO', 'url' => ['supplier-delivery-invoice/view', 'id' => $model->supplier_delivery_invoice_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-delivery-invoice-payment-create">

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="supplier-delivery-invoice-payment-form">

                        <?php $form = ActiveForm::begin([
                                'options' => [

                                ],
                                'fieldConfig' => [
                                    'parts' => [
                                        '{inputClass}' => 'col-lg-12'
                                    ],
                                    'template' => '<div class="row">'
                                                    . '<div class="col-lg-3">'
                                                        . '{label}'
                                                    . '</div>'
                                                    . '<div class="col-lg-6">'
                                                        . '<div class="{inputClass}">'
                                                            . '{input}'
                                                        . '</div>'
                                                    . '</div>'
                                                    . '<div class="col-lg-3">'
                                                        . '{error}'
                                                    . '</div>'
                                                . '</div>', 
                                ]
                        ]); ?>                    

                        <?= $form->field($model, 'date', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-8'
                            ],
                        ])->widget(DatePicker::className(), [
                            'pluginOptions' => Yii::$app->params['datepickerOptions'],
                        ]) ?>

                        <?= $form->field($model, 'jumlah_bayar', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->widget(MaskMoney::className()) ?>                    

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['supplier-delivery-invoice/index'], ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        Detail Invoice Penerimaan PO
                    </h3>
                </div>

                <?= DetailView::widget([
                    'model' => $modelSupplierDeliveryInvoice,
                    'options' => [
                        'class' => 'table'
                    ],
                    'attributes' => [
                        'jumlah_harga:currency',
                        'jumlah_bayar:currency',
                        [
                            'label' => 'Jumlah Sisa',
                            'format' => 'raw',
                            'value' => Yii::$app->formatter->asCurrency(-1 * ($modelSupplierDeliveryInvoice->jumlah_bayar - $modelSupplierDeliveryInvoice->jumlah_harga)),
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>
    
    <?= $dynamicTableSDInvoicePayment->tableData() ?>

</div>

<?php
$jscript = '
    $("#supplierdeliveryinvoicepayment-date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
';

$this->registerJs($jscript); ?>