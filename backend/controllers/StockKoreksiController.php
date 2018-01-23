<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\StockKoreksi;
use restotech\standard\backend\models\search\StockKoreksiSearch;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * StockKoreksiController implements the CRUD actions for StockKoreksi model.
 */
class StockKoreksiController extends BackendController
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
     * Lists all StockKoreksi models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!empty(($post = Yii::$app->request->post())) && !empty($post['selectedRows'])) {            
                
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $opnameVerify = '';            
            $messages = '';

            $selectedRows = explode(',', $post['selectedRows']);
            
            foreach ($selectedRows as $value) {
                $post['pk'] = $value;
                $post['value'] = $post['action'];
                $opnameVerify = $this->opnameVerify($post);

                if (!($flag = $opnameVerify['flag'])) {                 
                    break;
                }
            }

            $model = $opnameVerify['model'];
            $modelStock = $opnameVerify['modelStock'];
            $flag = $opnameVerify['flag'];
            $messages = $opnameVerify['messages']; 

            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Verifikasi Sukses');
                Yii::$app->session->setFlash('message2', 'Proses verifikasi sukses. Data telah berhasil disimpan.');

                $transaction->commit();
            } else {           
                $model->setIsNewRecord(true);

                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Verifikasi Gagal');
                Yii::$app->session->setFlash('message2', $messages . '\nData gagal disimpan.');

                $transaction->rollBack();
            }                                
            
            return $this->redirect(['index']);
        }
        
        $searchModel = new StockKoreksiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query
                ->andWhere(['stock_koreksi.action' => 'Waiting']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StockKoreksi model.
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
     * Creates a new StockKoreksi model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new StockKoreksi();
        
        $modelStock = Stock::find()
                ->andWhere(['id' => $id])
                ->asArray()->one();
        
        $model->item_id = $modelStock['item_id'];
        $model->item_sku_id = $modelStock['item_sku_id'];
        $model->storage_id = $modelStock['storage_id'];
        $model->storage_rack_id = $modelStock['storage_rack_id'];
        $model->jumlah_awal = $modelStock['jumlah_stok'];
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            $model->date_action = Yii::$app->formatter->asDatetime(time());
            $model->user_action = Yii::$app->user->identity->id;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['stock/index']);
            } else {
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses tambah data gagal. Data gagal disimpan.');                
            }                        
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StockKoreksi model.
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
     * Deletes an existing StockKoreksi model.
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
     * Finds the StockKoreksi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return StockKoreksi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockKoreksi::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    private function opnameVerify($postParams) {
        $flag = false;
        $messages = '';
        
        if (($model = StockKoreksi::findOne($postParams['pk'])) !== null) {
                
            $model->action = $postParams['value'];

            if (($flag = $model->save()) && ($model->action == 'approved')) {
                if (($modelStock = Stock::findOne($model->item_id . $model->item_sku_id . $model->storage_id . $model->storage_rack_id)) !== null) {

                    $modelStock->jumlah_stok = $model->jumlah;

                    if (($flag = $modelStock->save())) {

                        $modelStockMovement = new StockMovement();
                        $modelStockMovement->tanggal = Yii::$app->formatter->asDate(time());
                        $modelStockMovement->type = 'Koreksi';
                        $modelStockMovement->item_id = $model->item_id;
                        $modelStockMovement->item_sku_id = $model->item_sku_id;

                        if ($model->jumlah_adjustment < 0) {
                            $modelStockMovement->jumlah = -1 * $model->jumlah_adjustment;
                            $modelStockMovement->storage_from = $model->storage_id;
                            $modelStockMovement->storage_rack_from = $model->storage_rack_id;
                        } else {
                            $modelStockMovement->jumlah = $model->jumlah_adjustment;
                            $modelStockMovement->storage_to = $model->storage_id;
                            $modelStockMovement->storage_rack_to = $model->storage_rack_id;
                        }
                        
                        $modelStockMovement->reference = $model->id;

                        if (!($flag = $modelStockMovement->save())) {
                            $messages = 'Error update data!';
                        }
                    }                            
                } else {
                    $flag = false;
                    $messages = 'Unavailable stock opname data';
                }
            }                                
        }
        
        return [
            'flag' => $flag, 
            'messages' => $messages, 
            'model' => $model,
            'modelStock' => empty($modelStock) ? NULL : $modelStock
        ];
    }
}
