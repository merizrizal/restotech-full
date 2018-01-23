<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\UserLevel;
use restotech\standard\backend\models\search\UserLevelSearch;
use restotech\standard\backend\models\UserAppModule;
use restotech\standard\backend\models\UserAkses;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * UserLevelController implements the CRUD actions for UserLevel model.
 */
class UserLevelController extends BackendController
{
    private $params = [];
    
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
     * Lists all UserLevel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserLevelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserLevel model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->params['id'] = $id;
        
        $modelUserAppModule = UserAppModule::find()
                ->joinWith([                    
                    'userAkses' => function($query) {
                        $query->onCondition('user_akses.user_level_id = ' . $this->params['id']);
                    },
                ])->asArray()->all();
                    
        $dataUserAppModule = [];
        foreach ($modelUserAppModule as $value) {
            $dataUserAppModule[$value['sub_program']][$value['nama_module']][] = $value;
        }
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelUserAppModule' => $dataUserAppModule,
        ]);
    }

    /**
     * Creates a new UserLevel model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserLevel();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }
        
        
        if ($model->load(Yii::$app->request->post()) && (($post = Yii::$app->request->post()))) {
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($flag = $model->save())) {
                
                foreach ($post['roles'] as $value) {
                    $modelUserAkses = new UserAkses();
                    $modelUserAkses->user_level_id = $model->id;
                    $modelUserAkses->user_app_module_id = $value['appModuleId'];
                    $modelUserAkses->is_active = !empty($value['action']) ? 1 : 0;

                    if (!($flag = $modelUserAkses->save())) {
                        break;
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
        
        $modelUserAppModule = UserAppModule::find()                
                ->joinWith([                    
                    'userAkses' => function($query) {
                        $query->onCondition('user_akses.user_level_id = -90909');
                    },
                ])->asArray()->all();
        
        $dataUserAppModule = [];
        foreach ($modelUserAppModule as $value) {
            $dataUserAppModule[$value['sub_program']][$value['nama_module']][] = $value;
        }                
         
        return $this->render('create', [
            'model' => $model,
            'modelUserAppModule' => $dataUserAppModule,
        ]);
    }

    /**
     * Updates an existing UserLevel model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->params['id'] = $id;
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && (($post = Yii::$app->request->post()))) {
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($flag = $model->save())) {
                
                foreach ($post['roles'] as $value) {
                    if ($value['userAksesId'] > 0) {
                        $modelUserAkses = UserAkses::findOne($value['userAksesId']);
                    } else {
                        $modelUserAkses = new UserAkses();
                        $modelUserAkses->user_level_id = $model->id;
                        $modelUserAkses->user_app_module_id = $value['appModuleId'];
                    }                    
                    
                    $modelUserAkses->is_active = !empty($value['action']) ? 1 : 0;

                    if (!($flag = $modelUserAkses->save())) {
                        break;
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
        
        $modelUserAppModule = UserAppModule::find()
                ->joinWith([                    
                    'userAkses' => function($query) {
                        $query->onCondition('user_akses.user_level_id = ' . $this->params['id']);
                    },
                ])->asArray()->all();
                    
        $dataUserAppModule = [];
        foreach ($modelUserAppModule as $value) {
            $dataUserAppModule[$value['sub_program']][$value['nama_module']][] = $value;
        }
         
        return $this->render('update', [
            'model' => $model,
            'modelUserAppModule' => $dataUserAppModule,
        ]);
    }

    /**
     * Deletes an existing UserLevel model.
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
                
                if ($exc->errorInfo[1] == 1451) {
                    
                    $model->is_deleted = 1;
                    $flag = $model->save();
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

    /**
     * Finds the UserLevel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserLevel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLevel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
