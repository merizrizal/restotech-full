<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\TransactionCash;
use restotech\standard\backend\models\search\TransactionCashSearch;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * TransactionCashController implements the CRUD actions for TransactionCash model.
 */
class TransactionCashController extends BackendController
{
    private $title = [];  
    
    public function __construct($id, $module, $config = array()) {
        
        parent::__construct($id, $module, $config);
        
        $this->title['Cash-In'] = 'Kas Masuk';
        $this->title['Cash-Out'] = 'Kas Keluar';
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
     * Lists all TransactionCash models.
     * @return mixed
     */
    public function actionIndex($type)
    {
        if ($type != 'Cash-In' && $type != 'Cash-Out') {
            throw new \yii\web\BadRequestHttpException(null);
        }
        
        $searchModel = new TransactionCashSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query
                ->andWhere(['transaction_account.account_type' => $type]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type' => $type,
            'title' => $this->title,
        ]);
    }

    /**
     * Displays a single TransactionCash model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'title' => $this->title,
        ]);
    }

    /**
     * Creates a new TransactionCash model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        if ($type != 'Cash-In' && $type != 'Cash-Out') {
            throw new \yii\web\BadRequestHttpException(null);
        }
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new TransactionCash();
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
            'type' => $type,
            'title' => $this->title,
        ]);
    }

    /**
     * Updates an existing TransactionCash model.
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
            'title' => $this->title,
        ]);
    }

    /**
     * Deletes an existing TransactionCash model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $type = '';
        
        if (($model = $this->findModel($id)) !== false) {
            
            $type = $model->account->account_type;
                        
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

        return $this->redirect(['index', 'type' => $type]);
    }

    /**
     * Finds the TransactionCash model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TransactionCash the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransactionCash::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
