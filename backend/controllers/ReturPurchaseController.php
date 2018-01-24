<?php

namespace restotech\full\backend\controllers;

use Yii;
use restotech\standard\backend\models\ReturPurchase;
use restotech\standard\backend\models\search\ReturPurchaseSearch;
use restotech\standard\backend\models\ReturPurchaseTrx;
use restotech\standard\backend\models\Item;
use restotech\standard\backend\models\ItemSku;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\Settings;

use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;


/**
 * ReturPurchaseController implements the CRUD actions for ReturPurchase model.
 */
class ReturPurchaseController extends \restotech\standard\backend\controllers\ReturPurchaseController
{
    public function beforeAction($action) {

        if (parent::beforeAction($action)) {

            $this->setViewPath('@restotech/full/backend/views/' . $action->controller->id);

            return true;
        } else {
            return false;
        }
    }
    
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
     * Lists all ReturPurchase models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReturPurchaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReturPurchase model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $dataProviderRPTrx = new ActiveDataProvider([
            'query' => ReturPurchaseTrx::find()->joinWith(['item', 'itemSku', 'storage', 'storageRack'])->andWhere(['retur_purchase_id' => $id]),  
            'sort' => false
        ]);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelRPTrx' => new ReturPurchaseTrx(),
            'dataProviderRPTrx' => $dataProviderRPTrx,
        ]);
    }

    /**
     * Creates a new ReturPurchase model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new ReturPurchase();
        $model->date = Yii::$app->formatter->asDate(time());
        
        $modelReturPurchaseTrx = new ReturPurchaseTrx();                
        
        $modelItem = new Item();
        
        $modelItemSku = new ItemSku();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($model->id = Settings::getTransNumber('no_rp')) !== false) {                
                
                if (($flag = $model->save()) && ($flag = !empty($post['ReturPurchaseTrx']))) {
                        
                    foreach ($post['ReturPurchaseTrx'] as $i => $returPurchaseTrx) {

                        $temp['ReturPurchaseTrx'] = $returPurchaseTrx;

                        $modelReturPurchaseTrx = new ReturPurchaseTrx();
                        $modelReturPurchaseTrx->load($temp);
                        $modelReturPurchaseTrx->retur_purchase_id = $model->id;
                        $modelReturPurchaseTrx->jumlah_harga = $modelReturPurchaseTrx->jumlah_item * $modelReturPurchaseTrx->harga_satuan;

                        if (($flag = $modelReturPurchaseTrx->save())) {

                            $flag = Stock::setStock(
                                    $modelReturPurchaseTrx->item_id, 
                                    $modelReturPurchaseTrx->item_sku_id, 
                                    $modelReturPurchaseTrx->storage_id, 
                                    $modelReturPurchaseTrx->storage_rack_id, 
                                    -1 * $modelReturPurchaseTrx->jumlah_item
                            );

                            if ($flag) {
                                $flag = StockMovement::setOutflow(
                                        'Outflow-RP', 
                                        $modelReturPurchaseTrx->item_id, 
                                        $modelReturPurchaseTrx->item_sku_id, 
                                        $modelReturPurchaseTrx->storage_id, 
                                        $modelReturPurchaseTrx->storage_rack_id, 
                                        $modelReturPurchaseTrx->jumlah_item,
                                        Yii::$app->formatter->asDate(time()), 
                                        $modelReturPurchaseTrx->retur_purchase_id
                                );                                    
                            }
                        }                                                
                        
                        if (!$flag) {
                            break;
                        }
                        
                        $model->jumlah_item += $modelReturPurchaseTrx->jumlah_item;
                        $model->jumlah_harga += $modelReturPurchaseTrx->jumlah_harga;
                    }
                }
                
                if ($flag) {
                    $flag = $model->save();
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
            'modelReturPurchaseTrx' => $modelReturPurchaseTrx,
            'modelItem' => $modelItem,
            'modelItemSku' => $modelItemSku,
        ]);
    }

    /**
     * Updates an existing ReturPurchase model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);
        
        $modelReturPurchaseTrx = new ReturPurchaseTrx();       
        
        $modelItem = new Item();
        
        $modelItemSku = new ItemSku();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (!empty($post['ReturPurchaseTrx'])) {
                        
                foreach ($post['ReturPurchaseTrx'] as $i => $returPurchaseTrx) {

                    $temp['ReturPurchaseTrx'] = $returPurchaseTrx;

                    $modelReturPurchaseTrx = new ReturPurchaseTrx();
                    $modelReturPurchaseTrx->load($temp);
                    $modelReturPurchaseTrx->retur_purchase_id = $model->id;
                    $modelReturPurchaseTrx->jumlah_harga = $modelReturPurchaseTrx->jumlah_item * $modelReturPurchaseTrx->harga_satuan;

                    if (($flag = $modelReturPurchaseTrx->save())) {

                        $flag = Stock::setStock(
                                $modelReturPurchaseTrx->item_id, 
                                $modelReturPurchaseTrx->item_sku_id, 
                                $modelReturPurchaseTrx->storage_id, 
                                $modelReturPurchaseTrx->storage_rack_id, 
                                -1 * $modelReturPurchaseTrx->jumlah_item
                        );

                        if ($flag) {
                            $flag = StockMovement::setOutflow(
                                    'Inflow-PO', 
                                    $modelReturPurchaseTrx->item_id, 
                                    $modelReturPurchaseTrx->item_sku_id, 
                                    $modelReturPurchaseTrx->storage_id, 
                                    $modelReturPurchaseTrx->storage_rack_id, 
                                    $modelReturPurchaseTrx->jumlah_item,
                                    Yii::$app->formatter->asDate(time()), 
                                    $modelReturPurchaseTrx->retur_purchase_id
                            );                                    
                        }
                    }                                                

                    if (!$flag) {
                        break;
                    }

                    $model->jumlah_item += $modelReturPurchaseTrx->jumlah_item;
                    $model->jumlah_harga += $modelReturPurchaseTrx->jumlah_harga;
                }
            }

            if ($flag) {
                $flag = $model->save();
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }                        
        }
        
        return $this->render('update', [
            'model' => $model,
            'modelReturPurchaseTrx' => $modelReturPurchaseTrx,
            'modelItem' => $modelItem,
            'modelItemSku' => $modelItemSku,
        ]);
    }

    /**
     * Deletes an existing ReturPurchase model.
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
    
    public function actionGetRpById($id) {
        
        $this->layout = '@restotech/standard/backend/views/layouts/ajax';
        
        $data = ReturPurchaseTrx::find()
                ->joinWith([
                    'supplierDelivery',
                    'item', 
                    'itemSku'
                ])
                ->andWhere(['supplier_delivery.id' => $id])
                ->asArray()->all();
        
        return $this->render('_get_rp_by_id', [
            'data' => $data,
        ]);
    }
}
