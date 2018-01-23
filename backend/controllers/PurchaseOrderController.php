<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\PurchaseOrder;
use restotech\standard\backend\models\search\PurchaseOrderSearch;
use restotech\standard\backend\models\PurchaseOrderTrx;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;


/**
 * PurchaseOrderController implements the CRUD actions for PurchaseOrder model.
 */
class PurchaseOrderController extends BackendController
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
     * Lists all PurchaseOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PurchaseOrder model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $dataProviderPOTrx = new ActiveDataProvider([
            'query' => PurchaseOrderTrx::find()->joinWith(['item', 'itemSku'])->andWhere(['purchase_order_id' => $id]),
            'sort' => false
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelPOTrx' => new PurchaseOrderTrx(),
            'dataProviderPOTrx' => $dataProviderPOTrx,
        ]);
    }

    /**
     * Creates a new PurchaseOrder model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $model = new PurchaseOrder();
        $model->date = Yii::$app->formatter->asDate(time());

        $modelPurchaseOrderTrx = new PurchaseOrderTrx();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            Yii::$app->formatter->timeZone = 'UTC';

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            if (($flag = ($model->id = Settings::getTransNumber('no_po')) !== false)) {

                if (($flag = $model->save())) {

                    $model->jumlah_item = 0;
                    $model->jumlah_harga = 0;

                    foreach ($post['PurchaseOrderTrx'] as $i => $purchaseOrderTrx) {

                        if ($i !== 'index') {

                            $temp['PurchaseOrderTrx'] = $purchaseOrderTrx;

                            $newModelPurchaseOrderTrx = new PurchaseOrderTrx();
                            $newModelPurchaseOrderTrx->load($temp);
                            $newModelPurchaseOrderTrx->purchase_order_id = $model->id;
                            $newModelPurchaseOrderTrx->jumlah_harga = $newModelPurchaseOrderTrx->jumlah_order * $newModelPurchaseOrderTrx->harga_satuan;

                            $model->jumlah_item += $newModelPurchaseOrderTrx->jumlah_order;
                            $model->jumlah_harga += $newModelPurchaseOrderTrx->jumlah_harga;

                            if (!($flag = $newModelPurchaseOrderTrx->save())) {
                                break;
                            }
                        }
                    }

                    if ($flag) {
                        $flag = $model->save();
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
            'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
        ]);
    }

    /**
     * Updates an existing PurchaseOrder model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $modelPurchaseOrderTrx = !empty($model->purchaseOrderTrxes) ? $model->purchaseOrderTrxes : new PurchaseOrderTrx();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;

            if (($flag = $model->save())) {

                $model->jumlah_item = 0;
                $model->jumlah_harga = 0;

                foreach ($post['PurchaseOrderTrx'] as $i => $purchaseOrderTrx) {

                    if ($i !== 'index') {

                        if (empty($purchaseOrderTrx['id'])) {

                            $temp['PurchaseOrderTrx'] = $purchaseOrderTrx;

                            $newModelPurchaseOrderTrx = new PurchaseOrderTrx();
                            $newModelPurchaseOrderTrx->load($temp);
                            $newModelPurchaseOrderTrx->purchase_order_id = $model->id;
                            $newModelPurchaseOrderTrx->jumlah_harga = $newModelPurchaseOrderTrx->jumlah_order * $newModelPurchaseOrderTrx->harga_satuan;

                            $model->jumlah_item += $newModelPurchaseOrderTrx->jumlah_order;
                            $model->jumlah_harga += $newModelPurchaseOrderTrx->jumlah_harga;

                            if (!($flag = $newModelPurchaseOrderTrx->save())) {
                                break;
                            }
                        } else {

                            foreach ($model->purchaseOrderTrxes as $dataModelPurchaseOrderTrx) {

                                if ($purchaseOrderTrx['id'] == $dataModelPurchaseOrderTrx->id) {

                                    if (empty($purchaseOrderTrx['delete']['id'])) {

                                        $temp['PurchaseOrderTrx'] = $purchaseOrderTrx;

                                        $dataModelPurchaseOrderTrx->load($temp);
                                        $dataModelPurchaseOrderTrx->jumlah_harga = $dataModelPurchaseOrderTrx->jumlah_order * $dataModelPurchaseOrderTrx->harga_satuan;

                                        $model->jumlah_item += $dataModelPurchaseOrderTrx->jumlah_order;
                                        $model->jumlah_harga += $dataModelPurchaseOrderTrx->jumlah_harga;

                                        if (!($flag = $dataModelPurchaseOrderTrx->save())) {
                                            break 2;
                                        }
                                    } else {

                                        if (!($flag = $dataModelPurchaseOrderTrx->delete())) {
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
                    $flag = $model->save();
                }
            }

            if ($flag) {
                $transaction->commit();

                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update data sukses. Data telah berhasil disimpan.');

                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                $transaction->rollBack();

                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Tambah Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update data gagal. Data gagal disimpan.');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
        ]);
    }

    /**
     * Deletes an existing PurchaseOrder model.
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
     * Finds the PurchaseOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PurchaseOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchaseOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetPo($id) {

        $this->layout = 'ajax';

        $data = PurchaseOrderTrx::find()
                ->joinWith([
                    'purchaseOrder',
                    'item',
                    'itemSku'
                ])
                ->andWhere(['purchase_order.kd_supplier' => $id])
                ->andWhere(['purchase_order_trx.is_closed' => 0])
                ->asArray()->all();

        return $this->render('_get_po', [
            'data' => $data,
        ]);
    }

    public function actionPrint($id) {
        $model = $this->findModel($id);
        $modelPurchaseOrderTrxs = PurchaseOrderTrx::find()->joinWith(['item', 'itemSku'])->where(['purchase_order_id' => $id])->all();

        $content = $this->renderPartial('report/print', [
            'model' => $model,
            'modelPurchaseOrderTrxs' => $modelPurchaseOrderTrxs
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_DOWNLOAD,
            'content' => $content,
            'cssFile' => '@vendor/yii2-krajee-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => file_get_contents(Yii::getAlias('@restotech/standard/backend/media/css/report.css')),
            'options' => ['title' => Yii::$app->name],
            'methods' => [
                'SetHeader'=>[Yii::$app->name . ' - Purchase Order'],
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }
}
