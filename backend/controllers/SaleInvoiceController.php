<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\search\SaleInvoiceSearch;
use restotech\standard\backend\models\SaleInvoiceRetur;
use restotech\standard\backend\models\Menu;
use restotech\standard\backend\models\SaldoKasir;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * SaleInvoiceController implements the CRUD actions for SaleInvoice model.
 */
class SaleInvoiceController extends BackendController
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

    /**
     * Lists all SaleInvoice models.
     * @return mixed
     */
    public function actionRefund()
    {
        $searchModel = new SaleInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('refund', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SaleInvoice model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = SaleInvoice::find()
                ->joinWith([
                    'mtableSession',
                    'mtableSession.mtable',
                    'userOperator.kdKaryawan',
                    'saleInvoiceTrxes.menu',
                    'saleInvoiceTrxes.saleInvoiceReturs.menu' => function($query) {
                        $query->from('menu as menu_retur');
                    },
                ])
                ->andWhere(['sale_invoice.id' => $id])
                ->one();
        
        if (empty($model)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }       
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (($flag = !empty($post['SaleInvoiceRetur']))) {
            
                foreach ($post['SaleInvoiceRetur'] as $i => $saleInvoiceRetur) {

                    $temp['SaleInvoiceRetur'] = $saleInvoiceRetur;

                    $modelSaleInvoiceRetur = new SaleInvoiceRetur();
                    $modelSaleInvoiceRetur->load($temp);
                    $modelSaleInvoiceRetur->date = Yii::$app->formatter->asDatetime(time());
                    
                    if (!($flag = $modelSaleInvoiceRetur->save())) {
                        break;
                    }
                }
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['view', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }
        }
        
        return $this->render('view', [
            'model' => $model,
            'modelSaleInvoiceRetur' => new SaleInvoiceRetur(),
            'modelMenu' => new Menu(),
        ]);
    }
    
    public function actionReportPenjualan()
    {
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            $modelSaleInvoice = null;
                    
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
            
            $title = '';
            $content = '';
            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);
            
            Yii::$app->formatter->timeZone = 'Asia/Jakarta';
            
            if ($post['jenis'] == 'Detail') {
                
                $title = ' - Laporan Penjualan Detail / Tanggal ' .  $tanggal;
                $content = $this->renderPartial('report/penjualan_detail_print', [
                    'modelSaleInvoice' => $modelSaleInvoice,
                    'print' => $post['print'],
                ]);
            } else if ($post['jenis'] == 'Summary') {
                
                $title = ' - Laporan Penjualan Summary / Tanggal ' .  $tanggal;
                $content = $this->renderPartial('report/penjualan_summary_print', [
                    'modelSaleInvoice' => $modelSaleInvoice,
                    'print' => $post['print'],
                ]);
            } else if ($post['jenis'] == 'Terlaris') {                                     
                
                $title = ' - Laporan Penjualan Terlaris / Tanggal ' .  $tanggal;
                $content = $this->renderPartial('report/penjualan_terlaris_print', [
                    'modelSaleInvoice' => $modelSaleInvoice,
                    'print' => $post['print'],
                ]);
            }           
            
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
        
        return $this->render('report/penjualan', [
        
        ]);
    }
    
    public function actionReportPenjualanHpp()
    {
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal'])) {

            $modelSaleInvoice = null;
                    
            $modelSaleInvoice = SaleInvoice::find()
                    ->joinWith([
                        'saleInvoiceTrxes' => function($query) {
                            $query->andWhere(['sale_invoice_trx.is_free_menu' => false]);
                        },
                        'saleInvoiceTrxes.menu',
                        'saleInvoiceTrxes.menu.menuHpps' => function($query) {
                            $query->andOnCondition('DATE_FORMAT(CONVERT_TZ(menu_hpp.date, "+00:00", "+07:00"), "%Y-%m-%d") <= "' . Yii::$app->request->post()['tanggal'] . '"')
                                    ->orderBy('menu_hpp.date DESC')
                                    ->limit(1);
                        },
                        'saleInvoicePayments',
                        'saleInvoicePayments.paymentMethod',
                    ])
                    ->andWhere('DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d") = "' . $post['tanggal'] . '"')
                    ->asArray()->all();                        
            
            $title = '';
            $content = '';
            $tanggal = Yii::$app->formatter->asDate($post['tanggal']);
            
            Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                
            $title = ' - Laporan Penjualan Dan HPP / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/penjualan_hpp_print', [
                'modelSaleInvoice' => $modelSaleInvoice,
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
        
        return $this->render('report/penjualan_hpp', [
        
        ]);
    }
    
    public function actionReportKasKasir()
    {
        $modelSaleInvoice = null;
        $modelSaldoKasir = null;
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal'])) {                        

            $modelSaleInvoice = SaleInvoice::find()
                ->joinWith([
                    'saleInvoiceTrxes.menu',
                    'saleInvoiceTrxes.menu.menuCategory',
                    'saleInvoiceTrxes.saleInvoiceReturs',
                    'saleInvoicePayments',
                    'saleInvoicePayments.paymentMethod',
                ])
                ->andWhere('DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d") = "' . $post['tanggal'] . '"')
                ->orderBy('menu.nama_menu')
                ->asArray()->all();           
            
            $modelSaldoKasir = SaldoKasir::find()
                    ->joinWith([
                        'shift'
                    ])
                    ->andWhere(['saldo_kasir.date' => $post['tanggal']])
                    ->andWhere('"' . date('H:i:s') . '" BETWEEN shift.start_time AND shift.end_time')
                    ->asArray()->one();   
            
            if ($post['print'] != 'print') {
                
                $title = '';
                $content = '';
                $tanggal = Yii::$app->formatter->asDate($post['tanggal']);
                
                Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                
                if ($post['jenis'] == 'Kategori-Menu') {

                    $title = ' - Report Kas Kasir By Kategori Menu / Tanggal ' .  $tanggal;                                

                    $content = $this->renderPartial('report/kas_kasir_kategori_menu_print', [
                        'modelSaleInvoice' => $modelSaleInvoice,
                        'modelSaldoKasir' => $modelSaldoKasir,
                        'print' => $post['print'],
                    ]);    
                } else if ($post['jenis'] == 'Faktur') {

                    $title = ' - Report Kas Kasir By Faktur / Tanggal ' .  $tanggal;                                

                    $content = $this->renderPartial('report/kas_kasir_faktur_print', [
                        'modelSaleInvoice' => $modelSaleInvoice,
                        'modelSaldoKasir' => $modelSaldoKasir,
                        'print' => $post['print'],
                    ]);    
                }
                
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
        }        
        
        return $this->render('report/kas_kasir', [
            'modelSaleInvoice' => $modelSaleInvoice,
            'modelSaldoKasir' => $modelSaldoKasir,
            'tanggal' => !empty($post['tanggal']) ? $post['tanggal'] : '',
            'jenis' => !empty($post['jenis']) ? $post['jenis'] : '',
            'print' => !empty($post['print']) ? $post['print'] : '',
        ]);
    }
    
    public function actionReportRekapPenjualan() {
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            $modelSaleInvoice = null;
                    
            $modelSaleInvoice = SaleInvoice::find()
                    ->joinWith([
                        'saleInvoiceTrxes' => function($query) {
                            $query->andWhere(['sale_invoice_trx.is_free_menu' => false]);
                        },
                        'saleInvoiceTrxes.menu',
                        'saleInvoiceTrxes.menu.menuCategory',
                        'saleInvoiceTrxes.menu.menuCategory.parentCategory' => function($query) {
                            $query->from('menu_category parent_menu_category');
                        },
                        'saleInvoiceTrxes.saleInvoiceReturs',
                        'saleInvoicePayments',
                        'saleInvoicePayments.paymentMethod',
                    ])
                    ->andWhere('DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->orderBy('menu.nama_menu')
                    ->asArray()->all();
            
            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);            
            
            Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                
            $title = ' - Laporan Rekap Penjualan / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/rekap_penjualan_print', [
                'modelSaleInvoice' => $modelSaleInvoice,
                'kategoriParent' => ($post['jenis'] == 'Parent-Kategori') ? true : (($post['jenis'] == 'Kategori') ? false : null),
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
        
        return $this->render('report/rekap_penjualan', [
        
        ]);
    }
    
    /**
     * Finds the SaleInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SaleInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleInvoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
