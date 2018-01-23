<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\Menu;
use restotech\standard\backend\models\search\MenuSearch;
use restotech\standard\backend\models\MenuCondiment;
use restotech\standard\backend\models\MenuHpp;
use restotech\standard\backend\models\MenuRecipe;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use restotech\standard\backend\components\Tools;


/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends BackendController
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
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $dataProviderMenuRecipe = new ActiveDataProvider([
            'query' => MenuRecipe::find()->joinWith(['item', 'itemSku'])->andWhere(['menu_recipe.menu_id' => $id]),  
            'sort' => false
        ]);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelMenuRecipe' => new MenuRecipe(),
            'dataProviderMenuRecipe' => $dataProviderMenuRecipe,
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        
        $model = new Menu();
        $modelMenuRecipe = new MenuRecipe();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load($post)) {
    
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (($flag = $model->id = Settings::getTransNumber('id_menu', 'ym', $model->nama_menu))) {
                        
                $model->harga_pokok = !empty($model->harga_pokok) ? $model->harga_pokok : 0;
                $model->harga_jual = !empty($model->harga_jual) ? $model->harga_jual : 0;

                if (($model->image = Tools::uploadFile('/img/menu/', $model, 'image', 'id'))) {
                    $flag = true;        
                }
                
                if (($flag = $model->save() && $flag)) {
                    
                    $menuHppId = Yii::$app->formatter->asDate(time(), 'yyyyMMdd') . $model->id;
                    
                    $modelMenuHpp = MenuHpp::findOne($menuHppId);
                    
                    if (!empty($modelMenuHpp)) {
                        
                        $modelMenuHpp->harga_pokok = $model->harga_pokok;
                        
                        $flag = $modelMenuHpp->save();
                    } else {
                     
                        $modelMenuHpp = new MenuHpp();
                        $modelMenuHpp->id = $menuHppId;
                        $modelMenuHpp->date = Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd HH:mm');
                        $modelMenuHpp->menu_id = $model->id;
                        $modelMenuHpp->harga_pokok = $model->harga_pokok;
                    }
                    
                    if (($flag = $modelMenuHpp->save())) {
                        foreach ($post['MenuRecipe'] as $i => $menuRecipe) {

                            if ($i !== 'index') {

                                $newModelMenuRecipe = new MenuRecipe();
                                $newModelMenuRecipe->menu_id = $model->id;
                                $newModelMenuRecipe->item_id = $menuRecipe['item_id'];
                                $newModelMenuRecipe->item_sku_id = $menuRecipe['item_sku_id'];
                                $newModelMenuRecipe->jumlah = $menuRecipe['jumlah'];

                                if (!($flag = $newModelMenuRecipe->save())) {
                                    break;
                                }
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
            'modelMenuRecipe' => $modelMenuRecipe,
        ]);
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        
        $model = $this->findModel($id);
        
        $modelMenuRecipe = !empty($model->menuRecipes) ? $model->menuRecipes : new MenuRecipe();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }        
        
        if ($model->load($post)) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (($model->image = Tools::uploadFile('/img/menu/', $model, 'image', 'id'))) {
                $flag = true;        
            } else {
                $flag = true;
                $model->image = $model->oldAttributes['image'];
            }
                        
            if ($flag) {
                
                $model->harga_pokok = !empty($model->harga_pokok) ? $model->harga_pokok : 0;
                $model->harga_jual = !empty($model->harga_jual) ? $model->harga_jual : 0;

                if (($flag = $model->save())) {
                    
                    $menuHppId = Yii::$app->formatter->asDate(time(), 'yyyyMMdd') . $model->id;
                    
                    $modelMenuHpp = MenuHpp::findOne($menuHppId);
                    
                    if (!empty($modelMenuHpp)) {
                        
                        $modelMenuHpp->harga_pokok = $model->harga_pokok;
                        
                        $flag = $modelMenuHpp->save();
                    } else {
                     
                        $modelMenuHpp = new MenuHpp();
                        $modelMenuHpp->id = $menuHppId;
                        $modelMenuHpp->date = Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd');
                        $modelMenuHpp->menu_id = $model->id;
                        $modelMenuHpp->harga_pokok = $model->harga_pokok;
                    }
                    
                    if (($flag = $modelMenuHpp->save())) {
                    
                        foreach ($post['MenuRecipe'] as $i => $menuRecipe) {

                            if ($i !== 'index') {

                                if (empty($menuRecipe['id'])) {

                                    $newModelMenuRecipe = new MenuRecipe();
                                    $newModelMenuRecipe->menu_id = $model->id;
                                    $newModelMenuRecipe->item_id = $menuRecipe['item_id'];
                                    $newModelMenuRecipe->item_sku_id = $menuRecipe['item_sku_id'];
                                    $newModelMenuRecipe->jumlah = $menuRecipe['jumlah'];

                                    if (!($flag = $newModelMenuRecipe->save())) {
                                        break;
                                    }
                                } else {

                                    foreach ($model->menuRecipes as $dataModelMenuRecipe) {

                                        if ($menuRecipe['id'] == $dataModelMenuRecipe->id) {

                                            if (empty($menuRecipe['delete']['id'])) {

                                                $dataModelMenuRecipe->item_id = $menuRecipe['item_id'];
                                                $dataModelMenuRecipe->item_sku_id = $menuRecipe['item_sku_id'];
                                                $dataModelMenuRecipe->jumlah = $menuRecipe['jumlah'];

                                                if (!($flag = $dataModelMenuRecipe->save())) {
                                                    break 2;
                                                }
                                            } else {

                                                if (!($flag = $dataModelMenuRecipe->delete())) {
                                                    break 2;
                                                }
                                            }

                                            break;
                                        }
                                    }
                                }
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
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses tambah data gagal. Data gagal disimpan.');                
                
                $transaction->rollBack();
            }                                    
        }                
        
        return $this->render('update', [
            'model' => $model,
            'modelMenuRecipe' => $modelMenuRecipe,
        ]);
    }

    /**
     * Deletes an existing Menu model.
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
    
    public function actionCondiment($id) {
        
        $post = Yii::$app->request->post();
        
        $model = $this->findModel($id);
        
        $modelMenuCondiment = !empty($model->menuCondiments) ? $model->menuCondiments : new MenuCondiment();
        
        if (Yii::$app->request->isPost) {   
    
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;                            
            
            foreach ($post['MenuCondiment'] as $i => $menuCondiment) {
                
                if ($i !== 'index') {
                
                    if (empty($menuCondiment['id'])) {

                        $newModelMenuCondiment = new MenuCondiment();
                        $newModelMenuCondiment->parent_menu_id = $model->id;
                        $newModelMenuCondiment->menu_id = $menuCondiment['menu_id'];

                        if (!($flag = $newModelMenuCondiment->save())) {
                            break;
                        }
                    } else {

                        foreach ($model->menuCondiments as $dataModelMenuCondiment) {

                            if ($menuCondiment['id'] == $dataModelMenuCondiment->id) {

                                if (empty($menuCondiment['delete']['id'])) {

                                    $dataModelMenuCondiment->menu_id = $menuCondiment['menu_id'];

                                    if (!($flag = $dataModelMenuCondiment->save())) {
                                        break 2;
                                    }
                                } else {

                                    if (!($flag = $dataModelMenuCondiment->delete())) {
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
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['condiment', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }
        }
        
        return $this->render('condiment', [
            'model' => $model,       
            'modelMenuCondiment' => $modelMenuCondiment,
        ]);
    }
    
    public function actionHppHistory($id) {
        
        $dataProviderMenuHpp = new ActiveDataProvider([
            'query' => MenuHpp::find()->andWhere(['menu_hpp.menu_id' => $id]),  
            'sort' => false
        ]);
        
        return $this->render('hpp_history', [
            'model' => $this->findModel($id),
            'modelMenuHpp' => new MenuHpp(),
            'dataProviderMenuHpp' => $dataProviderMenuHpp,
        ]);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
