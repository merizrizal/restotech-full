<?php

namespace restotech\full\backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\db\Query;
use kartik\mpdf\Pdf;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\components\Tools;


/**
 * Page controller
 */
class PageController extends \restotech\standard\backend\controllers\PageController {

    public function beforeAction($action) {

        if (parent::beforeAction($action)) {

            $this->setViewPath('@restotech/full/backend/views/' . $action->controller->id);

            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return array_merge(
            $this->getAccess(),
            [                
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                                            
                    ],
                ],
            ]);        
    }
    
    public function actionReportAktifitasKeuangan() {
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            Tools::loadIsIncludeScp();
            
            $modelSaleInvoice = SaleInvoice::find()
                    ->joinWith([
                        'saleInvoiceTrxes' => function($query) {
                            $query->andWhere(['sale_invoice_trx.is_free_menu' => false]);
                        },
                        'saleInvoiceTrxes.menu',
                        'saleInvoicePayments',
                        'saleInvoicePayments.paymentMethod',
                    ])
                    ->andWhere('DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->asArray()->all();
                        
            $penjualan = 0;
            
            foreach ($modelSaleInvoice as $dataSaleInvoice) {

                $discBill = empty($dataSaleInvoice['discount']) ? 0 : $dataSaleInvoice['discount'];
                $discBillType = $dataSaleInvoice['discount_type'];
                $discBillValue = 0;

                if ($discBillType == 'Percent') {                                                    
                    $discBillValue = $discBill * 0.01 * $dataSaleInvoice['jumlah_harga']; 
                } else if ($discBillType == 'Value') {
                    $discBillValue = $discBill;        
                }    

                $jumlahSubtotal = 0;

                foreach ($dataSaleInvoice['saleInvoiceTrxes'] as $dataSaleInvoiceTrx) {

                    $subtotal = $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];
                    $discount = 0;

                    if ($dataSaleInvoiceTrx['discount_type'] == 'Percent') {

                        $discount = ($dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['discount'] / 100) * $dataSaleInvoiceTrx['jumlah'];
                        $subtotal = $subtotal - $discount;
                    } else if ($dataSaleInvoiceTrx['discount_type'] == 'Value') {

                        $discount = $dataSaleInvoiceTrx['discount'] * $dataSaleInvoiceTrx['jumlah']; 
                        $subtotal = $subtotal - $discount;
                    }

                    $jumlahSubtotal += $subtotal;
                }    

                $scp = Tools::hitungServiceChargePajak($jumlahSubtotal, $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);                                        
                $serviceCharge = $scp['serviceCharge'];
                $pajak = $scp['pajak']; 
                $grandTotal = $jumlahSubtotal + $serviceCharge + $pajak - $discBillValue;

                $penjualan += $grandTotal;
            }
            
            $query = new Query();
            $result = $query->select('SUM(jumlah) as total_cash_in')                    
                    ->from('transaction_cash')
                    ->join('LEFT JOIN', 'transaction_account', 'transaction_cash.account_id=transaction_account.id')
                    ->andWhere('DATE_FORMAT(CONVERT_TZ(transaction_cash.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->andWhere('transaction_account.account_type="Cash-In"')
                    ->one();
            
            $cashIn = $result['total_cash_in'];
            
            $query = new Query();
            $result = $query->select('SUM(jumlah_bayar) as total_pembelian')
                ->from('supplier_delivery_invoice_payment')
                ->andWhere('DATE_FORMAT(CONVERT_TZ(supplier_delivery_invoice_payment.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                ->one();
            
            $pembelianPO = $result['total_pembelian'];
            
            $query = new Query();
            $result = $query->select('SUM(jumlah_harga) as total_pembelian')
                ->from('direct_purchase')
                ->andWhere('DATE_FORMAT(CONVERT_TZ(direct_purchase.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                ->one();
            
            $pembelian = $result['total_pembelian'];
            
            $query = new Query();
            $result = $query->select('SUM(jumlah) as total_cash_out')                    
                    ->from('transaction_cash')
                    ->join('LEFT JOIN', 'transaction_account', 'transaction_cash.account_id=transaction_account.id')
                    ->andWhere('DATE_FORMAT(CONVERT_TZ(transaction_cash.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->andWhere('transaction_account.account_type="Cash-Out"')
                    ->one();
            
            $cashOut = $result['total_cash_out'];

            $content = $this->renderPartial('report/aktifitas_keuangan_print', [
                'penjualan' => $penjualan,                
                'cashIn' => $cashIn,
                'pembelianPO' => $pembelianPO,
                'pembelian' => $pembelian,
                'cashOut' => $cashOut,
                'print' => $post['print'],
            ]);                    

            if ($post['print'] == 'pdf') {
                $footer = '
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">' . date('d-m-Y H:m:s') . ' - ' . Yii::$app->session->get('user_data')['employee']['nama'] . '</td>
                            <td style="width:50%; text-align:right">{PAGENO}</td>
                        </tr>
                    </table>
                ';
                
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_BLANK, 
                    'format' => Pdf::FORMAT_A4, 
                    'orientation' => Pdf::ORIENT_PORTRAIT, 
                    'destination' => Pdf::DEST_DOWNLOAD, 
                    'content' => $content,  
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                    'cssInline' => file_get_contents(Yii::getAlias('@restotech/standard/backend/media/css/report.css')), 
                    'options' => ['title' => Yii::$app->name],
                    'methods' => [ 
                        'SetHeader'=>[Yii::$app->name . ' - Report Aktivitas Keuangan / Tanggal ' .  Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to'])], 
                        'SetFooter'=>[$footer],
                    ]
                ]);

                return $pdf->render(); 
            } else if ($post['print'] == 'excel') {
                header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . Yii::$app->name . ' - Report Aktivitas Keuangan / Tanggal ' .  Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']) .'.xls"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private',false);
                echo $content;
                exit;
            } 
        }
        
        return $this->render('report/aktifitas_keuangan', [
            
        ]);
    }
}
