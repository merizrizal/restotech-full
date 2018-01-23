<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\DirectPurchase;
use restotech\standard\backend\models\search\DirectPurchaseSearch;
use restotech\standard\backend\models\DirectPurchaseTrx;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;


/**
 * DirectPurchaseController implements the CRUD actions for DirectPurchase model.
 */
class DirectPurchaseController extends BackendController
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
     * Lists all DirectPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DirectPurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DirectPurchase model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $dataProviderDPTrx = new ActiveDataProvider([
            'query' => DirectPurchaseTrx::find()->joinWith(['item', 'itemSku', 'storage', 'storageRack'])->andWhere(['direct_purchase_id' => $id]),  
            'sort' => false
        ]);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelDPTrx' => new DirectPurchaseTrx(),
            'dataProviderDPTrx' => $dataProviderDPTrx,
        ]);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DirectPurchase model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new DirectPurchase();
        $model->date = Yii::$app->formatter->asDate(time());  
        
        $modelDirectPurchaseTrx = new DirectPurchaseTrx();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }
        
        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {
 
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;            
            
            if (($flag = ($model->id = Settings::getTransNumber('no_dp')) !== false)) {
                
                if (($flag = $model->save())) {
                    
                    $model->jumlah_item = 0;
                    $model->jumlah_harga = 0;
                
                    foreach ($post['DirectPurchaseTrx'] as $i => $directPurchaseTrx) {
                        
                        if ($i !== 'index') {
                            
                            $temp['DirectPurchaseTrx'] = $directPurchaseTrx;                            
                            
                            $newModelDirectPurchaseTrx = new DirectPurchaseTrx();
                            $newModelDirectPurchaseTrx->load($temp);
                            $newModelDirectPurchaseTrx->direct_purchase_id = $model->id;
                            $newModelDirectPurchaseTrx->jumlah_harga = $newModelDirectPurchaseTrx->jumlah_item * $newModelDirectPurchaseTrx->harga_satuan;                                

                            if (($flag = $newModelDirectPurchaseTrx->save())) {                                

                                $flag = Stock::setStock(
                                        $newModelDirectPurchaseTrx->item_id, 
                                        $newModelDirectPurchaseTrx->item_sku_id, 
                                        $newModelDirectPurchaseTrx->storage_id, 
                                        $newModelDirectPurchaseTrx->storage_rack_id, 
                                        $newModelDirectPurchaseTrx->jumlah_item
                                );

                                if ($flag) {
                                    $flag = StockMovement::setInflow(
                                            'Inflow-DP', 
                                            $newModelDirectPurchaseTrx->item_id, 
                                            $newModelDirectPurchaseTrx->item_sku_id, 
                                            $newModelDirectPurchaseTrx->storage_id, 
                                            $newModelDirectPurchaseTrx->storage_rack_id, 
                                            $newModelDirectPurchaseTrx->jumlah_item,
                                            Yii::$app->formatter->asDate(time()), 
                                            $newModelDirectPurchaseTrx->direct_purchase_id
                                    );                                    
                                }
                            }
                            
                            if (!$flag) {
                                break;
                            }
                            
                            $model->jumlah_item += $newModelDirectPurchaseTrx->jumlah_item;
                            $model->jumlah_harga += $newModelDirectPurchaseTrx->jumlah_harga;
                        }                        
                    }
                    
                    if ($flag) {
                        $flag = $model->save();
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
            'modelDirectPurchaseTrx' => $modelDirectPurchaseTrx,
        ]);
    }

    /**
     * Updates an existing DirectPurchase model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);        
        
        $modelDirectPurchaseTrx = !empty($model->directPurchaseTrxes) ? $model->directPurchaseTrxes : new DirectPurchaseTrx();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }
        
        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            $transaction = Yii::$app->db->beginTransaction();      
            $flag = true;

            if (($flag = $model->save())) {
                
                $model->jumlah_item = 0;
                $model->jumlah_harga = 0;

                foreach ($post['DirectPurchaseTrx'] as $i => $directPurchaseTrx) {
                        
                    if ($i !== 'index') {

                        if (empty($directPurchaseTrx['id'])) {

                            $temp['DirectPurchaseTrx'] = $directPurchaseTrx;
                            
                            $newModelDirectPurchaseTrx = new DirectPurchaseTrx();
                            $newModelDirectPurchaseTrx->load($temp);
                            $newModelDirectPurchaseTrx->direct_purchase_id = $model->id;
                            $newModelDirectPurchaseTrx->jumlah_harga = $newModelDirectPurchaseTrx->jumlah_item * $newModelDirectPurchaseTrx->harga_satuan;
                            
                            $model->jumlah_item += $newModelDirectPurchaseTrx->jumlah_item;
                            $model->jumlah_harga += $newModelDirectPurchaseTrx->jumlah_harga;

                            if (($flag = $newModelDirectPurchaseTrx->save())) {                                

                                $flag = Stock::setStock(
                                        $newModelDirectPurchaseTrx->item_id, 
                                        $newModelDirectPurchaseTrx->item_sku_id, 
                                        $newModelDirectPurchaseTrx->storage_id, 
                                        $newModelDirectPurchaseTrx->storage_rack_id, 
                                        $newModelDirectPurchaseTrx->jumlah_item
                                );

                                if ($flag) {
                                    $flag = StockMovement::setInflow(
                                            'Inflow-DP', 
                                            $newModelDirectPurchaseTrx->item_id, 
                                            $newModelDirectPurchaseTrx->item_sku_id, 
                                            $newModelDirectPurchaseTrx->storage_id, 
                                            $newModelDirectPurchaseTrx->storage_rack_id, 
                                            $newModelDirectPurchaseTrx->jumlah_item,
                                            Yii::$app->formatter->asDate(time()), 
                                            $newModelDirectPurchaseTrx->direct_purchase_id
                                    );                                    
                                }
                            }
                        } else {

                            foreach ($model->directPurchaseTrxes as $dataModelDirectPurchaseTrx) {

                                if ($directPurchaseTrx['id'] == $dataModelDirectPurchaseTrx->id) {

                                    if (empty($directPurchaseTrx['delete']['id'])) {
                                        
                                        $model->jumlah_item += $dataModelDirectPurchaseTrx->jumlah_item;
                                        $model->jumlah_harga += $dataModelDirectPurchaseTrx->jumlah_harga;
                                    } else {

                                        if (($flag = $dataModelDirectPurchaseTrx->delete())) {
                                            
                                            $flag = Stock::setStock(
                                                    $dataModelDirectPurchaseTrx->item_id, 
                                                    $dataModelDirectPurchaseTrx->item_sku_id, 
                                                    $dataModelDirectPurchaseTrx->storage_id, 
                                                    $dataModelDirectPurchaseTrx->storage_rack_id, 
                                                    -1 * $dataModelDirectPurchaseTrx->jumlah_item
                                            );

                                            if ($flag) {
                                                $flag = StockMovement::setOutflow(
                                                        'Inflow-DP-Delete', 
                                                        $dataModelDirectPurchaseTrx->item_id, 
                                                        $dataModelDirectPurchaseTrx->item_sku_id, 
                                                        $dataModelDirectPurchaseTrx->storage_id, 
                                                        $dataModelDirectPurchaseTrx->storage_rack_id, 
                                                        $dataModelDirectPurchaseTrx->jumlah_item,
                                                        Yii::$app->formatter->asDate(time()), 
                                                        $dataModelDirectPurchaseTrx->direct_purchase_id
                                                );                                    
                                            }
                                        }
                                        
                                        if (!$flag) {
                                            break 2;
                                        }
                                    }

                                    break;
                                }
                            }                                                        
                        }
                    }                        
                }
                
                if ($flag) {
                    $flag = $model->save();
                }
            }
            
            if ($flag) {
                $transaction->commit();
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update data sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                $transaction->rollBack();
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update data gagal. Data gagal disimpan.');      
            }
        }
        
        return $this->render('update', [
            'model' => $model,
            'modelDirectPurchaseTrx' => $modelDirectPurchaseTrx,
        ]);
    }

    /**
     * Deletes an existing DirectPurchase model.
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
     * Finds the DirectPurchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return DirectPurchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DirectPurchase::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionPrint($id) {
        $model = $this->findModel($id);
        $modelDirectPurchaseTrxs = DirectPurchaseTrx::find()->joinWith(['item', 'itemSku'])->where(['direct_purchase_id' => $id])->all();
        
        $content = $this->renderPartial('report/print', [
            'model' => $model,
            'modelDirectPurchaseTrxs' => $modelDirectPurchaseTrxs
        ]);
        
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'destination' => Pdf::DEST_DOWNLOAD, 
            'content' => $content,  
            'cssFile' => '@vendor/yii2-krajee-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => file_get_contents(Yii::getAlias('@restotech/standard/backend/media/css/report.css')), 
            'options' => ['title' => Yii::$app->name],
            'methods' => [ 
                'SetHeader'=>[Yii::$app->name . ' - Pembelian Langsungs'], 
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }
}
