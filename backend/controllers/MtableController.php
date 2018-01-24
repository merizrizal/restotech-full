<?php

namespace restotech\full\backend\controllers;

use Yii;
use restotech\standard\backend\models\Mtable;
use restotech\standard\backend\models\search\MtableSearch;
use restotech\standard\backend\models\MtableCategory;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use restotech\standard\backend\components\Tools;


/**
 * MtableController implements the CRUD actions for Mtable model.
 */
class MtableController extends \restotech\standard\backend\controllers\MtableController
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
     * Lists all Mtable models.
     * @return mixed
     */
    public function actionIndex($cid)
    {
        $searchModel = new MtableSearch();
        $searchModel->mtable_category_id = $cid;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere(['mtable.mtable_category_id' => $searchModel->mtable_category_id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mtable model.
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
     * Creates a new Mtable model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate($cid)
    {
        $model = new Mtable();
        
        $model->mtable_category_id = $cid;
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (($flag = $model->id = Settings::getTransNumber('id_mtable', 'ym', $model->nama_meja))) {                                
                
                if (($model->image = Tools::uploadFile('/img/mtable/', $model, 'image', 'id'))) {
                    $flag = true;        
                }
                
                $flag = $model->save();
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
        ]);
    }

    /**
     * Updates an existing Mtable model.
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
            
            $flag = false;
            
            if (($model->image = Tools::uploadFile('/img/mtable/', $model, 'image', 'id'))) {
                $flag = true;        
            } else {
                $flag = true;
                $model->image = $model->oldAttributes['image'];
            }
            
            if ($model->save() && $flag) {
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
     * Deletes an existing Mtable model.
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

        return $this->redirect(['index', 'cid' => $model->mtable_category_id]);
    }
    
    public function actionTableLayout($catid = null)
    {
        if (($post = Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;            
            
            foreach ($post['mtable'] as $mtable) {
                $modelMtable = $this->findModel($mtable['id']);
                $modelMtable->layout_x = $mtable['layout_x'];
                $modelMtable->layout_y = $mtable['layout_y'];
                $modelMtable->shape = $mtable['shape'];
                
                if (!($flag = $modelMtable->save()))
                    break;
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }
            
            return $this->redirect(['table-layout', 'catid' => $catid]); 
        }
        
        $model = MtableCategory::find()
                ->joinWith([
                    'mtables' => function($query) {
                        $query->andOnCondition(['mtable.not_active' => 0])
                                ->andOnCondition(['mtable.is_deleted' => 0]);
                    }
                ])
                ->andWhere(['mtable_category.not_active' => 0])
                ->andWhere(['mtable_category.is_deleted' => 0])
                ->orderBy('mtable_category.nama_category')
                ->all();
        
        if (empty($catid)) {
            
            if (empty($model))
                throw new NotFoundHttpException('The requested page does not exist.');
            
            $catid = $model[0]->id;
        }
        
        return $this->render('table_layout', [
            'model' => $model,
            'catid' => $catid,
        ]);
    }
}
