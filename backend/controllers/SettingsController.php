<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\Settings;
use restotech\standard\backend\models\search\SettingsSearch;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use restotech\standard\backend\components\Tools;

/**
 * SettingsController implements the CRUD actions for Settings model.
 */
class SettingsController extends BackendController
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
     * Lists all Settings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Settings model.
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
     * Creates a new Settings model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Settings();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update', 'id' => $model->setting_id]);
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
     * Updates an existing Settings model.
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
                
                return $this->redirect(['update', 'id' => $model->setting_id]);
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
     * Deletes an existing Settings model.
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
     * Action for update-setting page
     * If update is successful, the browser will be redirected to the 'form_settings_company' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdateSetting($id)
    {
        if ($id == 'company') {            
            return $this->updateSetting([['like', 'setting_name', 'company_']], $id, 'Profile Perusahaan');
        } else if ($id == 'tax-sc') {
            return $this->updateSetting([['like', 'setting_name', 'tax_amount'], ['like', 'setting_name', 'service_charge_amount']], $id, 'Nilai Pajak dan Service Charge');
        } else if ($id == 'include-tax-sc') {
            return $this->updateSetting([['like', 'setting_name', 'tax_include_service_charge']], $id, 'Pajak Include Service Charge');
        } else if ($id == 'struk') {
            return $this->updateSetting([['like', 'setting_name', 'struk_'], ['setting_name' => 'print_paper_width']], $id, 'Setting Struk');
        } else if ($id == 'printserver') {
            return $this->updateSetting([['like', 'setting_name', 'print_server_']], $id, 'Print Server');
        } else if ($id == 'transaction-day') {
            return $this->updateSetting([['like', 'setting_name', 'transaction_day_']], $id, 'Transaction Day');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Updates an existing Settings model.
     * If update is successful, the browser will be redirected to the 'form_settings_company' page.
     * @param string $params
     * @return mixed
     */
    
    protected function updateSetting($params, $id, $judul) {
        $models = Settings::find();
        
        foreach ($params as $param) {
            $models->orFilterWhere($param);
        }

        $models = $models->all();        
        
        if (Settings::loadMultiple($models, Yii::$app->request->post())) { 
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            foreach ($models as $key => $model) {
                
                if ($model->type == 'file') {
                    
                    $model->setting_value = Tools::uploadFile('/img/company/', $model, '[' . $key . ']setting_value', 'setting_id');
                    
                    if (empty($model->setting_value)) {
                        
                        $model->setting_value = $model->oldAttributes['setting_value'];
                    }                            
                }
                
                if ($flag) $flag = $model->save();
                
                if (!$flag) {
                    $transaction->rollback();
                    break;
                }         
            }
            
            if ($flag) {
                $transaction->commit();
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update-setting', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
            }                        
        }
        
        return $this->render('form_settings', [
            'models' => $models,
            'judul' => $judul,
        ]);
    }
    
    public function actionShowVirtualKeyboard() {
        
        $model = new Settings();
        $model->setting_name = 'virtual_keyboard';
        $model->setting_value = Yii::$app->session->get('showVirtualKeyboard', false);
        
        if (!empty(($post = Yii::$app->request->post()))) {                        

            
            if ($model->load($post)) {                  

                Yii::$app->session->set('showVirtualKeyboard', $model->setting_value);
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');

                return $this->redirect(['show-virtual-keyboard']);                      
            }
        }        
        
        return $this->render('form_show_virtual_keyboard', [
            'model' => $model,
        ]);
    }    

    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModels($params)
    {
        if (($model = Settings::find($params)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
