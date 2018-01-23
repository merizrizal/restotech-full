<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SupplierDeliveryInvoicePayment;
use restotech\standard\backend\models\SupplierDeliveryInvoice;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

/**
 * SupplierDeliveryInvoicePaymentController implements the CRUD actions for SupplierDeliveryInvoicePayment model.
 */
class SupplierDeliveryInvoicePaymentController extends BackendController
{
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [                
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
            ]);
    }

    /**
     * Creates a new SupplierDeliveryInvoicePayment model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $modelSupplierDeliveryInvoice = SupplierDeliveryInvoice::findOne($id);
        
        if (empty($modelSupplierDeliveryInvoice)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $model = new SupplierDeliveryInvoicePayment();
        $model->supplier_delivery_invoice_id = $id;
        $model->date = Yii::$app->formatter->asDate(time());  
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($flag = $model->save())) {
                                
                $modelSupplierDeliveryInvoice->jumlah_bayar = $modelSupplierDeliveryInvoice->jumlah_bayar + $model->jumlah_bayar;
                
                $flag = $modelSupplierDeliveryInvoice->save();
            }            
            
            if ($flag) {
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['supplier-delivery-invoice/view', 'id' => $model->supplier_delivery_invoice_id]);
            } else {
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses tambah data gagal. Data gagal disimpan.');   
                
                $transaction->rollback();
            }                        
        }
        
        $dataProviderSDInvoicePayment = new ActiveDataProvider([
            'query' => SupplierDeliveryInvoicePayment::find()->andWhere(['supplier_delivery_invoice_id' => $id]),  
            'sort' => false
        ]);
        
        return $this->render('create', [
            'model' => $model,
            'modelSupplierDeliveryInvoice' => $modelSupplierDeliveryInvoice,
            'modelSDInvoicePayment' => new SupplierDeliveryInvoicePayment(),
            'dataProviderSDInvoicePayment' => $dataProviderSDInvoicePayment,
        ]);
    }

    /**
     * Updates an existing SupplierDeliveryInvoicePayment model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
            }                        
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SupplierDeliveryInvoicePayment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (($model = $this->findModel($id)) !== false) {
                        
            $flag = false;
            $error = '';
            
            try {
                $flag = $model->delete();
            } catch (yii\db\Exception $exc) {
                $error = Yii::$app->params['errMysql'][$exc->errorInfo[1]];
            }
        }
        
        if ($flag) {
            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', 'Delete Sukses');
            Yii::$app->session->setFlash('message2', 'Proses delete sukses. Data telah berhasil dihapus.');            
        } else {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Delete Gagal');
            Yii::$app->session->setFlash('message2', 'Proses delete gagal. Data gagal dihapus.' . $error);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the SupplierDeliveryInvoicePayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SupplierDeliveryInvoicePayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SupplierDeliveryInvoicePayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionReportPembayaranHutang() {
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            $modelSupplierDeliveryInvoice = SupplierDeliveryInvoice::find()
                    ->joinWith([
                        'supplierDeliveryInvoicePayments',
                        'supplierDelivery',
                        'supplierDelivery.kdSupplier',
                        'paymentMethod',
                    ])
                    ->andWhere('supplier_delivery_invoice_payment.date BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->andWhere('payment_method.type="Purchase" AND payment_method.method="Account-Payable"')
                    ->asArray()->all();
            
            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);
            
            $title = ' - Report Pembayaran Hutang / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/pembayaran_hutang_print', [
                'modelSupplierDeliveryInvoice' => $modelSupplierDeliveryInvoice,
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
        
        return $this->render('report/pembayaran_hutang', [
        
        ]);
    }
}
