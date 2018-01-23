<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SaleInvoicePayment;
use restotech\standard\backend\models\search\SaleInvoicePaymentSearch;
use restotech\standard\backend\models\SaleInvoiceArPayment;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

/**
 * SaleInvoicePaymentController implements the CRUD actions for SaleInvoicePayment model.
 */
class SaleInvoicePaymentController extends BackendController
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
     * Lists all SaleInvoicePayment models.
     * @return mixed
     */
    public function actionAr()
    {
        $searchModel = new SaleInvoicePaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query
                ->andWhere(['payment_method.method' => 'Account-Receiveable']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionPayment($id)
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $modelSaleInvoicePayment = SaleInvoicePayment::findOne($id);
        
        if (empty($modelSaleInvoicePayment)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $model = new SaleInvoiceArPayment();
        $model->sale_invoice_payment_id = $id;
        $model->date = Yii::$app->formatter->asDate(time());  
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            Yii::$app->formatter->timeZone = 'UTC';           
            
            if ($model->save()) {
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');                
                
                return $this->redirect(['payment', 'id' => $modelSaleInvoicePayment->id]);
            } else {
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses tambah data gagal. Data gagal disimpan.');                   
            }                        
        }
        
        $dataProviderSaleInvoiceArPayment = new ActiveDataProvider([
            'query' => SaleInvoiceArPayment::find()->andWhere(['sale_invoice_payment_id' => $id]),  
            'sort' => false
        ]);

        return $this->render('payment', [
            'model' => $model,
            'modelSaleInvoicePayment' => $modelSaleInvoicePayment,
            'modelSaleInvoiceArPayment' => new SaleInvoiceArPayment(),
            'dataProviderSaleInvoiceArPayment' => $dataProviderSaleInvoiceArPayment,
        ]);
    }
    

    /**
     * Finds the SaleInvoicePayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SaleInvoicePayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleInvoicePayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionReportPiutang() {
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            $modelSaleInvoicePayment = SaleInvoicePayment::find()
                    ->joinWith([
                        'saleInvoice',
                        'saleInvoice.mtableSession',
                        'saleInvoiceArPayments',
                        'paymentMethod',
                    ])
                    ->andWhere('DATE_FORMAT(CONVERT_TZ(sale_invoice.date, "+00:00", "+07:00"), "%Y-%m-%d") BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->andWhere('payment_method.type="Sale" AND payment_method.method="Account-Receiveable"')
                    ->asArray()->all();
            
            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);
            
            $title = ' - Report Piutang / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/piutang_print', [
                'modelSaleInvoicePayment' => $modelSaleInvoicePayment,
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
        
        return $this->render('report/piutang', [
        
        ]);
    }
}
