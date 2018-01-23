<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\SupplierDeliveryInvoice;
use restotech\standard\backend\models\search\SupplierDeliveryInvoiceSearch;
use restotech\standard\backend\models\SupplierDeliveryInvoiceTrx;
use restotech\standard\backend\models\SupplierDeliveryTrx;
use restotech\standard\backend\models\ReturPurchaseTrx;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

/**
 * SupplierDeliveryInvoiceController implements the CRUD actions for SupplierDeliveryInvoice model.
 */
class SupplierDeliveryInvoiceController extends BackendController
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
     * Lists all SupplierDeliveryInvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SupplierDeliveryInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SupplierDeliveryInvoice model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $dataProviderSDTrx = new ActiveDataProvider([
            'query' => SupplierDeliveryTrx::find()->joinWith(['item', 'itemSku'])->andWhere(['supplier_delivery_id' => $model->supplier_delivery_id]),  
            'sort' => false
        ]);
        
        $dataProviderRPTrx = new ActiveDataProvider([
            'query' => ReturPurchaseTrx::find()->joinWith(['item', 'itemSku'])->andWhere(['supplier_delivery_id' => $model->supplier_delivery_id]),  
            'sort' => false
        ]);
        
        return $this->render('view', [
            'model' => $model,
            'modelSDTrx' => new SupplierDeliveryTrx(),
            'dataProviderSDTrx' => $dataProviderSDTrx,
            'modelRPTrx' => new SupplierDeliveryTrx(),
            'dataProviderRPTrx' => $dataProviderRPTrx,
        ]);
    }

    /**
     * Creates a new SupplierDeliveryInvoice model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new SupplierDeliveryInvoice();
        $model->date = Yii::$app->formatter->asDate(time());
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(($post = Yii::$app->request->post()))) {
            
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($model->id = Settings::getTransNumber('no_sdinv')) !== false) {                
                
                if (($flag = $model->save()) && ($flag = !empty($post['SupplierDeliveryInvoiceTrx']))) {
                
                    foreach ($post['SupplierDeliveryInvoiceTrx'] as $i => $supplierDeliveryInvoiceTrx) {
                        
                        $temp['SupplierDeliveryInvoiceTrx'] = $supplierDeliveryInvoiceTrx;

                        $modelSupplierDeliveryInvoiceTrx = new SupplierDeliveryInvoiceTrx();
                        $modelSupplierDeliveryInvoiceTrx->load($temp);
                        $modelSupplierDeliveryInvoiceTrx->supplier_delivery_invoice_id = $model->id;
                        
                        if (!($flag = $modelSupplierDeliveryInvoiceTrx->save())) {
                            break;
                        }
                    }
                }
            }
            
            if ($flag) {
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses tambah data gagal. Data gagal disimpan.');    
                
                $transaction->rollBack();
            }                        
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SupplierDeliveryInvoice model.
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
     * Deletes an existing SupplierDeliveryInvoice model.
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
     * Finds the SupplierDeliveryInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SupplierDeliveryInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SupplierDeliveryInvoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionReportHutang() {
        
        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {
            
            $modelSupplierDeliveryInvoice = SupplierDeliveryInvoice::find()
                    ->joinWith([
                        'supplierDelivery',
                        'supplierDelivery.kdSupplier',
                        'paymentMethod',
                    ])
                    ->andWhere('supplier_delivery_invoice.date BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->andWhere('payment_method.type="Purchase" AND payment_method.method="Account-Payable"')
                    ->asArray()->all();
            
            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);
            
            $title = ' - Report Hutang / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/hutang_print', [
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
        
        return $this->render('report/hutang', [
        
        ]);
    }        
}
