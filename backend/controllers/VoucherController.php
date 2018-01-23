<?php

namespace restotech\full\backend\controllers;

use Yii;
use restotech\standard\backend\models\Voucher;
use restotech\standard\backend\models\search\VoucherSearch;
use restotech\standard\backend\models\Settings;

use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * VoucherController implements the CRUD actions for Voucher model.
 */
class VoucherController extends \restotech\standard\backend\controllers\VoucherController
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
     * Lists all Voucher models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VoucherSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Voucher model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Voucher model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Voucher();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $flag = $model->id = Settings::getTransNumber('id_voucher', 'ym');
                
            if ($flag && $model->save()) {

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
     * Updates an existing Voucher model.
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
            
            /*$modelSaleInvoicePayment = SaleInvoicePayment::find()
                    ->andWhere(['payment_method_id' => 'XVCHR'])
                    ->andWhere(['LIKE', 'keterangan', $id])
                    ->all();            
            
            if (count($modelSaleInvoicePayment) > 0) {
                $flag = false;
                $error = '<br>Voucher ini sudah pernah dipakai untuk pembayaran';
            } else {            
                $flag = $model->save();                
            }*/
            
            $flag = $model->save();
                    
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.' . $error);
            }                        
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Voucher model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (($model = $this->findModel($id)) !== false) {
                        
            $flag = false;
            $error = '';
            
            $modelSaleInvoicePayment = SaleInvoicePayment::find()
                    ->andWhere(['payment_method_id' => 'XVCHR'])
                    ->andWhere(['LIKE', 'keterangan', $id])
                    ->all();            
            
            if (count($modelSaleInvoicePayment) > 0) {
                $flag = false;
                $error = '<br>Voucher ini sudah pernah dipakai untuk pembayaran';
            } else {            
                try {
                    $flag = $model->delete();
                } catch (yii\db\Exception $exc) {
                    $error = Yii::$app->params['errMysql'][$exc->errorInfo[1]];
                }
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
}
