<?php

namespace restotech\full\backend\controllers;

use Yii;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\search\StockSearch;
use restotech\standard\backend\models\ItemSku;

use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use kartik\mpdf\Pdf;


/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends \restotech\standard\backend\controllers\StockController
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
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }    
    
    /**
     * Updates an Stock Flow.
     *
     * @return mixed
     */
    public function actionInputStock($type)
    {        
        switch ($type) {
            case 'Inflow':
                return $this->stockInflow();
                break;
            
            case 'Outflow':
                return $this->stockOutflow();
                break;
            
            case 'Transfer':
                return $this->stockTransfer();
                break;

            default:
                break;
        }
    }
    
    /**
     * Updates an Stock model for Inflow.
     *
     * @return mixed
     */
    private function stockInflow()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new StockMovement();
        $model->tanggal = Yii::$app->formatter->asDate(time());
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;                                   
                
            $flag = Stock::setStock(
                    $model->item_id, 
                    $model->item_sku_id, 
                    $model->storage_to, 
                    $model->storage_rack_to, 
                    $model->jumlah
            );

            if ($flag) {                
                $model->type = 'Inflow';
                
                $flag = $model->save();
            }                       
            
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Data Stok Masuk Sukses');
                Yii::$app->session->setFlash('message2', 'Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['stock-movement/index', 'type' => 'Inflow', 'date' => 'selected', 'StockMovementSearch[tanggal]' => $model->tanggal]);
            } else {
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Data Stok Masuk Gagal');
                Yii::$app->session->setFlash('message2', 'Data gagal disimpan.');  

                $transaction->rollBack();
            }                        
        }
        
        return $this->render('stock_flow', [
            'model' => $model,
            'flow' => 'Inflow',
            'title' => 'Stok Masuk',
        ]);
    }
    
    /**
     * Updates an Stock model for Outflow.
     *
     * @return mixed
     */
    private function stockOutflow()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new StockMovement();
        $model->tanggal = Yii::$app->formatter->asDate(time());
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
        
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;            
            
            $flag = Stock::setStock(
                    $model->item_id, 
                    $model->item_sku_id, 
                    $model->storage_from, 
                    $model->storage_rack_from, 
                    -1 * $model->jumlah
            );
            
            if ($flag) {
                                        
                $model->type = 'Outflow';

                $flag = $model->save();
            }
                        
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Data Stok Keluar Sukses');
                Yii::$app->session->setFlash('message2', 'Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['stock-movement/index', 'type' => 'Outflow', 'date' => 'selected', 'StockMovementSearch[tanggal]' => $model->tanggal]);
            } else {
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Data Stok Keluar Gagal');
                Yii::$app->session->setFlash('message2', 'Data gagal disimpan.');                                                          
                
                $transaction->rollBack();
            }                        
        }
        
        return $this->render('stock_flow', [
            'model' => $model,
            'flow' => 'Outflow',
            'title' => 'Stok Keluar',
        ]);
    }
    
    /**
     * Updates an Stock model for Transfer.
     *
     * @return mixed
     */
    private function stockTransfer()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $model = new StockMovement();
        $model->tanggal = Yii::$app->formatter->asDate(time());
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $flag = Stock::setStock(
                $model->item_id, 
                $model->item_sku_id, 
                $model->storage_from, 
                $model->storage_rack_from, 
                -1 * $model->jumlah
            );
            
            if ($flag) {
                $flag = Stock::setStock(
                    $model->item_id, 
                    $model->item_sku_id, 
                    $model->storage_to, 
                    $model->storage_rack_to, 
                    $model->jumlah
                );
                
                if ($flag) {
                                        
                    $model->type = 'Transfer';

                    $flag = $model->save();
                }
            }
                        
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Data Stok Transfer Sukses');
                Yii::$app->session->setFlash('message2', 'Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['stock-movement/index', 'type' => 'Transfer', 'date' => 'selected', 'StockMovementSearch[tanggal]' => $model->tanggal]);
            } else {
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Data Stok Transfer Gagal');
                Yii::$app->session->setFlash('message2', 'Data gagal disimpan.');                                                          
                
                $transaction->rollBack();
            }
        }
        
        return $this->render('stock_flow', [
            'model' => $model,
            'flow' => 'Transfer',
            'title' => 'Stok Transfer',
        ]);
    }
    
    public function actionStockConvert()
    {
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $modelStock = new Stock();
        
        $model = new StockMovement();
        $model->tanggal = Yii::$app->formatter->asDate(time());
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if (!empty(($post = Yii::$app->request->post())) && $model->load($post) && $modelStock->load($post)) {
            
            Yii::$app->formatter->timeZone = 'UTC';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            $tanggal = $model->tanggal;
            
            $model->item_id = $modelStock->item_id;
            
            $flag = Stock::setStock(
                $model->item_id, 
                $model->item_sku_id, 
                $model->storage_to, 
                $model->storage_rack_to, 
                $model->jumlah
            );
            
            if ($flag) {
                
                $model->type = 'Inflow-Convert';

                $flag = $model->save();
            }                        
            
            if ($flag) {
                
                $flag = Stock::setStock(
                    $modelStock->item_id, 
                    $modelStock->item_sku_id, 
                    $modelStock->storage_id, 
                    $modelStock->storage_rack_id, 
                    -1 * $modelStock->jumlah_stok
                );
                
                if ($flag) {
                    
                    $model = new StockMovement();
                    $model->tanggal = $tanggal;
                    $model->item_id = $modelStock->item_id;
                    $model->item_sku_id = $modelStock->item_sku_id;
                    $model->storage_from = $modelStock->storage_id;
                    $model->storage_rack_from = $modelStock->storage_rack_id;
                    $model->jumlah = $modelStock->jumlah_stok;
                    $model->type = 'Outflow-Convert';
                    
                    $flag = $model->save();
                }
            }
            
            if ($flag) {
                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Stok Konversi Sukses');
                Yii::$app->session->setFlash('message2', 'Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['stock-movement/convert', 'date' => 'selected', 'StockMovementSearch[tanggal]' => $tanggal]);
            } else {
                
                $model->setIsNewRecord(true);
                
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Stok Konversi Gagal');
                Yii::$app->session->setFlash('message2', 'Data gagal disimpan.');                                                          
                
                $transaction->rollBack();
                
                return $this->redirect(['stock-convert']);
            }
        }
        
        return $this->render('stock_convert', [
            'modelStock' => $modelStock,
            'model' => $model,            
        ]);
    }
    
    public function actionGetSkuItem($id) 
    {
        $data = Stock::find()
                ->joinWith('itemSku')
                ->andWhere(['stock.item_id' => $id])
                ->andWhere(['>', 'item_sku.no_urut', 1])
                ->orderBy('item_sku.no_urut')
                ->asArray()->all();
        
        $tempRow = [];
        
        foreach ($data as $key => $value) {
            $tempRow[$value['itemSku']['id']]['id'] = $value['itemSku']['id']; 
            $tempRow[$value['itemSku']['id']]['text'] = $value['itemSku']['nama_sku'] . ' (' . $value['itemSku']['id'] . ')';
        }
        
        $row = [];
        $i = 0;
        
        foreach ($tempRow as $value) {
            $row[$i]['id'] = $value['id']; 
            $row[$i]['text'] = $value['text'];
            
            $i++;
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
    
    public function actionGetStorage($id) 
    {
        $data = Stock::find()
                ->joinWith('storage')
                ->andWhere(['stock.item_sku_id' => $id])
                ->asArray()->all();
        
        $tempRow = [];
        
        foreach ($data as $key => $value) {
            $tempRow[$value['storage']['id']]['id'] = $value['storage']['id']; 
            $tempRow[$value['storage']['id']]['text'] = $value['storage']['nama_storage'] . ' (' . $value['storage']['id'] . ')';
        }
        
        $row = [];
        $i = 0;
        
        foreach ($tempRow as $value) {
            $row[$i]['id'] = $value['id']; 
            $row[$i]['text'] = $value['text'];
            
            $i++;
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
    
    public function actionGetStorageRack($sid, $isid, $iid) 
    {
        $data = Stock::find()
                ->joinWith('storageRack')
                ->andWhere(['stock.storage_id' => $sid])
                ->andWhere(['stock.item_sku_id' => $isid])
                ->andWhere(['stock.item_id' => $iid])
                ->asArray()->all();
        
        $tempRow = [];
        
        foreach ($data as $key => $value) {
            $tempRow[$value['storageRack']['id']]['id'] = $value['storageRack']['id']; 
            $tempRow[$value['storageRack']['id']]['text'] = $value['storageRack']['nama_rak'];
        }
        
        $row = [];
        $i = 0;
        
        foreach ($tempRow as $value) {
            $row[$i]['id'] = $value['id']; 
            $row[$i]['text'] = $value['text'];
            
            $i++;
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
    
    public function actionGetSkuItemDescent($iid, $isid) 
    {
        $data = ItemSku::find()
                ->andWhere(['item_id' => $iid])
                ->orderBy('no_urut')
                ->asArray()->all();
        
        $noUrut = null;
        
        foreach ($data as $value) {
            
            if ($value['id'] == $isid) {
                
                $noUrut = $value['no_urut'];
                break;
            }
        }
                
        $row = [];                
        
        foreach ($data as $key => $value) {
            
            if ($value['no_urut'] < $noUrut) {
                
                $row[$key]['id'] = $value['id']; 
                $row[$key]['text'] = $value['nama_sku'] . ' (' . $value['id'] . ')';
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
    
    public function actionReportStock() {
        
        if (!empty($post = Yii::$app->request->post())) {
        
            $modelStock = Stock::find()
                ->joinWith([
                    'item',
                    'itemSku',
                    'storage',
                    'storageRack',
                ])
                ->asArray()->all();

            $content = $this->renderPartial('report/stock_print', [
                'modelStock' => $modelStock,
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
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                    'cssInline' => file_get_contents(Yii::getAlias('@restotech/standard/backend/media/css/report.css')), 
                    'options' => ['title' => Yii::$app->name],
                    'methods' => [ 
                        'SetHeader'=>[Yii::$app->name . ' - Laporan Stok'], 
                        'SetFooter'=>[$footer],
                    ]
                ]);

                return $pdf->render(); 
            } else if ($post['print'] == 'excel') {
                header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . Yii::$app->name . ' - Report Stok' .'.xls"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private',false);
                echo $content;
                exit;
            }  
        }
        
        return $this->render('report/stock', [
        
        ]);
    }
}
