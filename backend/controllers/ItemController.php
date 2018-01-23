<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\Item;
use restotech\standard\backend\models\search\ItemSearch;
use restotech\standard\backend\models\ItemSku;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends BackendController
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
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Item model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $modelSkusTemp = $model->itemSkus;
        $modelSkus = [];
        
        for ($i = 0; $i < 4; $i++) {
            if (!empty($modelSkusTemp[$i]->no_urut))
                $modelSkus[$modelSkusTemp[$i]->no_urut] = $modelSkusTemp[$i];
            else
                $modelSkus[$i + 1] = new ItemSku();
            
        }
        
        return $this->render('view', [
            'model' => $model,
            'modelSkus' => $modelSkus,
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Item();
        $modelSkus = [];
        
        for ($i = 1; $i <= 4; $i++) {
            $modelSkus[$i] = new ItemSku();
        }
        
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && ItemSku::loadMultiple($modelSkus, Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return array_merge(ActiveForm::validateMultiple($modelSkus), ActiveForm::validate($model));
        }

        if ($model->load(Yii::$app->request->post()) && ItemSku::loadMultiple($modelSkus, Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (($flag = $model->id = Settings::getTransNumber('id_item', 'ym', $model->nama_item))) {
            
                $model->item_category_id = !empty($model->item_category_id) ? $model->item_category_id : null;

                if (($flag = $model->save())) {
                    
                    foreach ($modelSkus as $key => $modelSku) {
                        
                        $modelSku->item_id = $model->id;
                        
                        if (!empty($modelSku->id)) {

                            $modelSku->storage_id = !empty($modelSku->storage_id) ? $modelSku->storage_id : null;

                            if (!($flag = $modelSku->save())) {
                                break;
                            }
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
            'modelSkus' => $modelSkus,
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); 
        
        $modelSkusTemp = $model->itemSkus;
        $modelSkus = [];
        for ($i = 0; $i < 4; $i++) {
            if (!empty($modelSkusTemp[$i]->no_urut))
                $modelSkus[$modelSkusTemp[$i]->no_urut] = $modelSkusTemp[$i];
            else
                $modelSkus[$i + 1] = new ItemSku();
            
        }        
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && ItemSku::loadMultiple($modelSkus, Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return array_merge(ActiveForm::validateMultiple($modelSkus), ActiveForm::validate($model));
        }        

        if ($model->load(Yii::$app->request->post()) && ItemSku::loadMultiple($modelSkus, Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            $model->item_category_id = !empty($model->item_category_id) ? $model->item_category_id : null;
            
            if (($flag = $model->save())) {       
                
                foreach ($modelSkus as $key => $modelSku) {
                    
                    $modelSku->item_id = $model->id;
                    $modelSku->storage_id = $modelSku->storage_id == '' ? NULL : $modelSku->storage_id;
                    
                    if (!empty($modelSku->id)) {
                        if (!($flag = $modelSku->save())) {             
                            break;
                        }
                    }
                }
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
            'modelSkus' => $modelSkus,
        ]);
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (($model = $this->findModel($id)) !== false) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            $error = '';                                                            
            
            try {
                ItemSku::deleteAll(['item_id' => $model->id]);                
                $flag = $model->delete();
            } catch (yii\db\Exception $exc) {
                $error = Yii::$app->params['errMysql'][$exc->errorInfo[1]];
            }
        }
        
        if ($flag) {
            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', 'Delete Sukses');
            Yii::$app->session->setFlash('message2', 'Proses delete sukses. Data telah berhasil dihapus.');
            
            $transaction->commit();
        } else {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Delete Gagal');
            Yii::$app->session->setFlash('message2', 'Proses delete gagal. Data gagal dihapus.' . $error);
            
            $transaction->rollBack();
        }
        
        return $this->redirect(['index']);
    }        

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
