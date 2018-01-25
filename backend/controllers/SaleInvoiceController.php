<?php

namespace restotech\full\backend\controllers;

use Yii;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\search\SaleInvoiceSearch;
use restotech\standard\backend\models\SaleInvoiceRetur;
use restotech\standard\backend\models\Menu;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SaleInvoiceController implements the CRUD actions for SaleInvoice model.
 */
class SaleInvoiceController extends \restotech\standard\backend\controllers\SaleInvoiceController
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
                        
                    ],
                ],
            ]);
    }

    /**
     * Lists all SaleInvoice models.
     * @return mixed
     */
    public function actionRefund()
    {
        $searchModel = new SaleInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('refund', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SaleInvoice model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = SaleInvoice::find()
                ->joinWith([
                    'mtableSession',
                    'mtableSession.mtable',
                    'userOperator.kdKaryawan',
                    'saleInvoiceTrxes.menu',
                    'saleInvoiceTrxes.saleInvoiceReturs.menu' => function($query) {
                        $query->from('menu as menu_retur');
                    },
                ])
                ->andWhere(['sale_invoice.id' => $id])
                ->one();
        
        if (empty($model)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }       
        
        if (!empty(($post = Yii::$app->request->post()))) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (($flag = !empty($post['SaleInvoiceRetur']))) {
            
                foreach ($post['SaleInvoiceRetur'] as $i => $saleInvoiceRetur) {

                    $temp['SaleInvoiceRetur'] = $saleInvoiceRetur;

                    $modelSaleInvoiceRetur = new SaleInvoiceRetur();
                    $modelSaleInvoiceRetur->load($temp);
                    $modelSaleInvoiceRetur->date = Yii::$app->formatter->asDatetime(time());
                    
                    if (!($flag = $modelSaleInvoiceRetur->save())) {
                        break;
                    }
                }
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['view', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }
        }
        
        return $this->render('view', [
            'model' => $model,
            'modelSaleInvoiceRetur' => new SaleInvoiceRetur(),
            'modelMenu' => new Menu(),
        ]);
    }        
}
