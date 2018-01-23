<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\ItemSku;
use restotech\standard\backend\models\search\ItemSkuSearch;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * ItemSkuController implements the CRUD actions for ItemSku model.
 */
class ItemSkuController extends BackendController
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
     * Lists all ItemSku models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemSkuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ItemSku model.
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
     * Creates a new ItemSku model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ItemSku();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update', 'id' => $model->id]);
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
     * Updates an existing ItemSku model.
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
     * Deletes an existing ItemSku model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete() !== false) {
            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', 'Delete Sukses');
            Yii::$app->session->setFlash('message2', 'Proses delete sukses. Data telah berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Delete Gagal');
            Yii::$app->session->setFlash('message2', 'Proses delete gagal. Data gagal dihapus.');
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Get SKU from sku
     * If get is successful, return the data.
     * @param string $id
     * @return mixed
     */
    public function actionGetSkuItem($id) 
    {
        $data = ItemSku::find()->andWhere(['item_id' => $id])->orderBy('no_urut')->asArray()->all();
        
        $row = [];
        
        foreach ($data as $key => $value) {
            $row[$key]['id'] = $value['id']; 
            $row[$key]['text'] = $value['nama_sku'] . ' (' . $value['id'] . ')';
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
    
    public function actionGetJumlahConvert($iid, $isidfrom, $isidto) 
    {
        $data = ItemSku::find()->andWhere(['item_id' => $iid])->orderBy('no_urut')->asArray()->all();
        
        $noUrutTo = null;
        $noUrutFrom = null;
        
        foreach ($data as $value) {
            
            if ($value['id'] == $isidto) {
                
                $noUrutTo = $value['no_urut'];
            } else if ($value['id'] == $isidfrom) {
                
                $noUrutFrom = $value['no_urut'];
            }
        }
        
        $response = [];
        
        foreach ($data as $value) {
            
            if ($value['no_urut'] > $noUrutTo && $value['no_urut'] <= $noUrutFrom) {
                
                $response['jumlah'] = !empty($response['jumlah']) ? ($response['jumlah'] * $value['per_stok']) : $value['per_stok'];
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * Finds the ItemSku model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ItemSku the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemSku::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
