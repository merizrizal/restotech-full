<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\MenuCategory;
use restotech\standard\backend\models\search\MenuCategorySearch;
use restotech\standard\backend\models\MenuCategoryPrinter;
use restotech\standard\backend\models\search\MenuCategoryPrinterSearch;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * MenuCategoryController implements the CRUD actions for MenuCategory model.
 */
class MenuCategoryController extends BackendController
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
     * Lists all MenuCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MenuCategory model.
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
     * Creates a new MenuCategory model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MenuCategory();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $flag = $model->id = Settings::getTransNumber('id_menu_category', 'ym', $model->nama_category);
                            
            $model->parent_category_id =  $model->parent_category_id == '' ? NULL : $model->parent_category_id;
            
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
     * Updates an existing MenuCategory model.
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
            
            $model->parent_category_id =  $model->parent_category_id == '' ? NULL : $model->parent_category_id;

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
     * Deletes an existing MenuCategory model.
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
    
    public function actionPrinter($id, $pid = null)
    {
        if (empty($pid))
            $modelMenuCategoryPrinter = new MenuCategoryPrinter();
        else             
            $modelMenuCategoryPrinter = MenuCategoryPrinter::findOne($pid);
        
        if (Yii::$app->request->isAjax && $modelMenuCategoryPrinter->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($modelMenuCategoryPrinter);
        }

        if ($modelMenuCategoryPrinter->load(Yii::$app->request->post())) {                        
            
            if ($modelMenuCategoryPrinter->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update data sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['printer', 'id' => $id]);
            } else {
                $modelMenuCategoryPrinter->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update data gagal. Data gagal disimpan.');                
            }                        
        }
        
        $searchModelMenuCategoryPrinter = new MenuCategoryPrinterSearch();
        $dataProviderMenuCategoryPrinter = $searchModelMenuCategoryPrinter->search(Yii::$app->request->queryParams, $id);
        
        return $this->render('printer', [
            'model' => $this->findModel($id),
            'modelMenuCategoryPrinter' => $modelMenuCategoryPrinter,
            'searchModelMenuCategoryPrinter' => $searchModelMenuCategoryPrinter,
            'dataProviderMenuCategoryPrinter' => $dataProviderMenuCategoryPrinter,
            'id' => $id,
            'pid' => $pid,
        ]);
    }
    
    public function actionPrinterDelete($id, $pid)
    {
        if (($model = MenuCategoryPrinter::findOne($pid)) !== false) {
                        
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

        return $this->redirect(['printer', 'id' => $id]);
    }

    /**
     * Finds the MenuCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MenuCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MenuCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
