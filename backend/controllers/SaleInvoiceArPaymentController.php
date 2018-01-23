<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SaleInvoiceArPayment;

use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * SaleInvoiceArPaymentController implements the CRUD actions for SaleInvoiceArPayment model.
 */
class SaleInvoiceArPaymentController extends BackendController
{
    public function behaviors()
    {
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

    public function actionReportPembayaranPiutang() {
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            $modelSaleInvoiceArPayment = SaleInvoiceArPayment::find()
                    ->joinWith([
                        'saleInvoicePayment',
                        'saleInvoicePayment.saleInvoice',
                        'saleInvoicePayment.saleInvoice.mtableSession',
                        'saleInvoicePayment.paymentMethod',
                    ])
                    ->andWhere('sale_invoice_ar_payment.date BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->andWhere('payment_method.type="Sale" AND payment_method.method="Account-Receiveable"')
                    ->asArray()->all();
            
            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);
            
            $title = ' - Report Pembayaran Piutang / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/pembayaran_piutang_print', [
                'modelSaleInvoiceArPayment' => $modelSaleInvoiceArPayment,
                'print' => $post['print'],
            ]);                    

            if ($post['print'] == 'pdf') {
                $footer = '
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">' . Yii::$app->formatter->asDatetime(time()) . ' - ' . Yii::$app->session->get('user_data')['employee']['nama'] . '</td>
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
                        'SetHeader'=>[Yii::$app->name . $title], 
                        'SetFooter'=>[$footer],
                    ]
                ]);

                return $pdf->render(); 
            } else if ($post['print'] == 'excel') {
                header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . Yii::$app->name . $title .'.xls"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private',false);
                echo $content;
                exit;
            }
        }
        
        return $this->render('report/pembayaran_piutang', [
        
        ]);
    }
}
