<?php

namespace restotech\full\backend\controllers;

use Yii;
use restotech\standard\backend\models\SupplierDelivery;
use restotech\standard\backend\models\search\SupplierDeliverySearch;
use restotech\standard\backend\models\SupplierDeliveryTrx;
use restotech\standard\backend\models\PurchaseOrderTrx;
use restotech\standard\backend\models\Item;
use restotech\standard\backend\models\ItemSku;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\Settings;

use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;


/**
 * SupplierDeliveryController implements the CRUD actions for SupplierDelivery model.
 */
class SupplierDeliveryController extends \restotech\standard\backend\controllers\SupplierDeliveryController
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
     * Lists all SupplierDelivery models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SupplierDeliverySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SupplierDelivery model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $dataProviderSDTrx = new ActiveDataProvider([
            'query' => SupplierDeliveryTrx::find()->joinWith(['item', 'itemSku', 'storage', 'storageRack'])->andWhere(['supplier_delivery_id' => $id]),
            'sort' => false
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelSDTrx' => new SupplierDeliveryTrx(),
            'dataProviderSDTrx' => $dataProviderSDTrx,
        ]);
    }

    /**
     * Creates a new SupplierDelivery model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $model = new SupplierDelivery();
        $model->date = Yii::$app->formatter->asDate(time());

        $modelSupplierDeliveryTrx = new SupplierDeliveryTrx();

        $modelPurchaseOrderTrx = new PurchaseOrderTrx();

        $modelItem = new Item();

        $modelItemSku = new ItemSku();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            Yii::$app->formatter->timeZone = 'UTC';

            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;

            if (($model->id = Settings::getTransNumber('no_sd')) !== false) {

                if (($flag = $model->save()) && ($flag = !empty($post['SupplierDeliveryTrx']))) {

                    foreach ($post['SupplierDeliveryTrx'] as $i => $supplierDeliveryTrx) {

                        $temp['SupplierDeliveryTrx'] = $supplierDeliveryTrx;

                        $modelSupplierDeliveryTrx = new SupplierDeliveryTrx();
                        $modelSupplierDeliveryTrx->load($temp);
                        $modelSupplierDeliveryTrx->supplier_delivery_id = $model->id;
                        $modelSupplierDeliveryTrx->jumlah_harga = $modelSupplierDeliveryTrx->jumlah_terima * $modelSupplierDeliveryTrx->harga_satuan;

                        if (($flag = $modelSupplierDeliveryTrx->save())) {

                            $modelPurchaseOrderTrx = $modelSupplierDeliveryTrx->purchaseOrderTrx;
                            $modelPurchaseOrderTrx->jumlah_terima += $modelSupplierDeliveryTrx->jumlah_terima;

                            if (!empty($post['PurchaseOrderTrx'][$i]['is_closed']))
                                $modelPurchaseOrderTrx->is_closed = $post['PurchaseOrderTrx'][$i]['is_closed'];

                            if (($flag = $modelPurchaseOrderTrx->save())) {

                                $flag = Stock::setStock(
                                        $modelSupplierDeliveryTrx->item_id,
                                        $modelSupplierDeliveryTrx->item_sku_id,
                                        $modelSupplierDeliveryTrx->storage_id,
                                        $modelSupplierDeliveryTrx->storage_rack_id,
                                        $modelSupplierDeliveryTrx->jumlah_terima
                                );

                                if ($flag) {
                                    $flag = StockMovement::setInflow(
                                            'Inflow-PO',
                                            $modelSupplierDeliveryTrx->item_id,
                                            $modelSupplierDeliveryTrx->item_sku_id,
                                            $modelSupplierDeliveryTrx->storage_id,
                                            $modelSupplierDeliveryTrx->storage_rack_id,
                                            $modelSupplierDeliveryTrx->jumlah_terima,
                                            Yii::$app->formatter->asDate(time()),
                                            $modelSupplierDeliveryTrx->supplier_delivery_id
                                    );
                                }
                            }
                        }

                        if (!$flag) {
                            break;
                        }

                        $model->jumlah_item += $modelSupplierDeliveryTrx->jumlah_terima;
                        $model->jumlah_harga += $modelSupplierDeliveryTrx->jumlah_harga;
                    }
                }

                if ($flag) {
                    $flag = $model->save();
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
            'modelSupplierDeliveryTrx' => $modelSupplierDeliveryTrx,
            'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
            'modelItem' => $modelItem,
            'modelItemSku' => $modelItemSku,
        ]);
    }

    /**
     * Updates an existing SupplierDelivery model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);

        $modelSupplierDeliveryTrx = new SupplierDeliveryTrx();

        $modelPurchaseOrderTrx = new PurchaseOrderTrx();

        $modelItem = new Item();

        $modelItemSku = new ItemSku();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post)) {

            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;

            if (!empty($post['SupplierDeliveryTrx'])) {

                foreach ($post['SupplierDeliveryTrx'] as $i => $supplierDeliveryTrx) {

                    $temp['SupplierDeliveryTrx'] = $supplierDeliveryTrx;

                    $modelSupplierDeliveryTrx = new SupplierDeliveryTrx();
                    $modelSupplierDeliveryTrx->load($temp);
                    $modelSupplierDeliveryTrx->supplier_delivery_id = $model->id;
                    $modelSupplierDeliveryTrx->jumlah_harga = $modelSupplierDeliveryTrx->jumlah_terima * $modelSupplierDeliveryTrx->harga_satuan;

                    if (($flag = $modelSupplierDeliveryTrx->save())) {

                        $modelPurchaseOrderTrx = $modelSupplierDeliveryTrx->purchaseOrderTrx;
                        $modelPurchaseOrderTrx->jumlah_terima += $modelSupplierDeliveryTrx->jumlah_terima;

                        if (!empty($post['PurchaseOrderTrx'][$i]['is_closed']))
                            $modelPurchaseOrderTrx->is_closed = $post['PurchaseOrderTrx'][$i]['is_closed'];

                        if (($flag = $modelPurchaseOrderTrx->save())) {

                            $flag = Stock::setStock(
                                    $modelSupplierDeliveryTrx->item_id,
                                    $modelSupplierDeliveryTrx->item_sku_id,
                                    $modelSupplierDeliveryTrx->storage_id,
                                    $modelSupplierDeliveryTrx->storage_rack_id,
                                    $modelSupplierDeliveryTrx->jumlah_terima
                            );

                            if ($flag) {
                                $flag = StockMovement::setInflow(
                                        'Inflow-PO',
                                        $modelSupplierDeliveryTrx->item_id,
                                        $modelSupplierDeliveryTrx->item_sku_id,
                                        $modelSupplierDeliveryTrx->storage_id,
                                        $modelSupplierDeliveryTrx->storage_rack_id,
                                        $modelSupplierDeliveryTrx->jumlah_terima,
                                        Yii::$app->formatter->asDate(time()),
                                        $modelSupplierDeliveryTrx->supplier_delivery_id
                                );
                            }
                        }
                    }

                    if (!$flag) {
                        break;
                    }

                    $model->jumlah_item += $modelSupplierDeliveryTrx->jumlah_terima;
                    $model->jumlah_harga += $modelSupplierDeliveryTrx->jumlah_harga;
                }
            }

            if ($flag) {
                $flag = $model->save();
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
            'modelSupplierDeliveryTrx' => $modelSupplierDeliveryTrx,
            'modelPurchaseOrderTrx' => $modelPurchaseOrderTrx,
            'modelItem' => $modelItem,
            'modelItemSku' => $modelItemSku,
        ]);
    }

    /**
     * Deletes an existing SupplierDelivery model.
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

    public function actionGetSd($id) {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $data = SupplierDeliveryTrx::find()
                ->joinWith([
                    'supplierDelivery',
                    'item',
                    'itemSku'
                ])
                ->andWhere(['supplier_delivery.kd_supplier' => $id])
                ->asArray()->all();

        return $this->render('_get_sd', [
            'data' => $data,
        ]);
    }

    public function actionGetSdById($id) {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $data = SupplierDeliveryTrx::find()
                ->joinWith([
                    'supplierDelivery',
                    'item',
                    'itemSku'
                ])
                ->andWhere(['supplier_delivery.id' => $id])
                ->asArray()->all();

        return $this->render('_get_sd_by_id', [
            'data' => $data,
        ]);
    }

    public function actionPrint($id) {
        $model = $this->findModel($id);
        $modelSupplierDeliveryTrxs = SupplierDeliveryTrx::find()->joinWith(['item', 'itemSku'])->where(['supplier_delivery_id' => $id])->all();

        $content = $this->renderPartial('report/print', [
            'model' => $model,
            'modelSupplierDeliveryTrxs' => $modelSupplierDeliveryTrxs
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
                'SetHeader'=>[Yii::$app->name . ' - Penerimaan Item'],
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionReportPenerimaan() {

        if (!empty($post = Yii::$app->request->post()) && !empty($post['tanggal_from']) && !empty($post['tanggal_to'])) {

            $modelSupplierDelivery = SupplierDeliveryTrx::find()
                    ->joinWith([
                        'supplierDelivery',
                        'supplierDelivery.kdSupplier',
                        'item',
                        'itemSku',
                        'storage',
                        'storageRack',
                    ])
                    ->andWhere('supplier_delivery.date BETWEEN "' . $post['tanggal_from'] . '" AND "' . $post['tanggal_to'] . '"')
                    ->asArray()->all();

            $tanggal = Yii::$app->formatter->asDate($post['tanggal_from']) . ' - ' . Yii::$app->formatter->asDate($post['tanggal_to']);

            $title = ' - Report Penerimaan Barang / Tanggal ' .  $tanggal;
            $content = $this->renderPartial('report/penerimaan_print', [
                'modelSupplierDelivery' => $modelSupplierDelivery,
                'print' => $post['print'],
            ]);

            if ($post['print'] == 'pdf') {
                $footer = '
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">' . Yii::$app->formatter->asDatetime(time()) . ' - ' . Yii::$app->session->get('user_data')['employee']['nama'] . '</td>
                            <td style="width:50%; text-align:right">{PAGENO}</td>
                        </tr>
                    </table>
                ';

                $pdf = new Pdf([
                    'mode' => Pdf::MODE_BLANK,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'destination' => Pdf::DEST_DOWNLOAD,
                    'content' => $content,
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                    'cssInline' => file_get_contents(Yii::getAlias('@restotech/standard/backend/media/css/report.css')),
                    'options' => ['title' => Yii::$app->name],
                    'methods' => [
                        'SetHeader'=>[Yii::$app->name . $title],
                        'SetFooter'=>[$footer],
                    ]
                ]);

                return $pdf->render();
            } else if ($post['print'] == 'excel') {
                header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . Yii::$app->name . $title .'.xls"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private',false);
                echo $content;
                exit;
            }
        }

        return $this->render('report/penerimaan', [

        ]);
    }
}
