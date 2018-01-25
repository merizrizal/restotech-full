<?php

namespace restotech\full\frontend\controllers;

use Yii;
use restotech\standard\backend\models\MtableSession;
use restotech\standard\backend\models\MtableJoin;
use restotech\standard\backend\models\MtableSessionJoin;
use restotech\standard\backend\models\MtableOrder;
use restotech\standard\backend\models\MtableOrderQueue;
use restotech\standard\backend\models\MtableBooking;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\SaleInvoiceTrx;
use restotech\standard\backend\models\SaleInvoicePayment;
use restotech\standard\backend\models\SaleInvoiceCorrection;
use restotech\standard\backend\models\SaleInvoiceTrxCorrection;
use restotech\standard\backend\models\SaleInvoicePaymentCorrection;
use restotech\standard\backend\models\MenuRecipe;
use restotech\standard\backend\models\Stock;
use restotech\standard\backend\models\StockMovement;
use restotech\standard\backend\models\Employee;
use restotech\standard\backend\models\Voucher;
use restotech\standard\backend\models\Settings;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Action controller
 */
class ActionController extends \restotech\standard\frontend\controllers\ActionController {

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'catatan' => ['post'],
                        'split' => ['post'],
                        'queue-menu' => ['post'],
                        'cashdrawer' => ['post'],
                        'transfer-table' => ['post'],
                        'transfer-menu' => ['post'],
                        'join-table' => ['post'],
                        'payment-correction' => ['post'],
                        'queue-finish' => ['post'],
                        'queue-send' => ['post'],
                        'create-booking' => ['post'],
                        'booking-open' => ['post'],
                    ],
                ],
            ]);
    }

    public function actionCatatan() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableOrder = MtableOrder::findOne($post['order_id']))))) {

            $modelMtableOrder->catatan = $post['catatan'];

            $flag = $modelMtableOrder->save();
        }

        $return = [];

        if ($flag) {

            $return['success'] = true;
            $return['catatan'] = $modelMtableOrder->catatan;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionSplit() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $modelMtableSession = new MtableSession();
        $modelMtableSession->mtable_id = $post['mtable_id'];
        $modelMtableSession->nama_tamu = $post['nama_tamu'];
        $modelMtableSession->jumlah_tamu = $post['jumlah_tamu'];
        $modelMtableSession->jumlah_harga = 0;
        $modelMtableSession->is_closed = 0;
        $modelMtableSession->opened_at = Yii::$app->formatter->asDatetime(time());
        $modelMtableSession->user_opened = Yii::$app->user->identity->id;

        if (($flag = $modelMtableSession->save())) {

            $jumlahHarga = 0;

            foreach ($post['order_id'] as $orderId) {

                $modelMtableOrder = MtableOrder::findOne($orderId);
                $modelMtableOrder->mtable_session_id = $modelMtableSession->id;

                $subtotal = $modelMtableOrder->harga_satuan * $modelMtableOrder->jumlah;
                $disc = 0;

                if ($modelMtableOrder->discount_type == 'Percent') {

                    $disc = $modelMtableOrder->discount * 0.01 * $subtotal;
                } else if ($modelMtableOrder->discount_type == 'Value') {

                    $disc = $modelMtableOrder->jumlah * $modelMtableOrder->discount;
                }

                $jumlahHarga += $subtotal - $disc;

                if (!($flag = $modelMtableOrder->save())) {
                    break;
                }
            }

            if ($flag) {

                $modelMtableSession->jumlah_harga = $jumlahHarga;

                if (($flag = $modelMtableSession->save())) {

                    $modelMtableSession = MtableSession::findOne($post['sess_id']);
                    $modelMtableSession->jumlah_harga = $modelMtableSession->jumlah_harga - $jumlahHarga;

                    $flag = $modelMtableSession->save();
                }
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionQueueMenu() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        foreach ($post['order_id'] as $dataOrder) {

            $modelMtableOrderQueue = new MtableOrderQueue();
            $modelMtableOrderQueue->mtable_order_id = $dataOrder['order_id'];
            $modelMtableOrderQueue->menu_id = $dataOrder['menu_id'];
            $modelMtableOrderQueue->jumlah = $dataOrder['jumlah'];
            $modelMtableOrderQueue->keterangan = $dataOrder['catatan'];

            if (!($flag = $modelMtableOrderQueue->save())) {
                break;
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionCashdrawer() {

        $post = Yii::$app->request->post();

        $flag = true;

        $return = [];

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionTransferTable() {

        $post = Yii::$app->request->post();

        $flag = true;

        if (($flag = !empty(($modelMtableSession = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSession->mtable_id = $post['mtable_id'];

            $flag = $modelMtableSession->save();
        }

        $return = [];

        if ($flag) {

            $return['open_table'] = Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/view-session', 'id' => $modelMtableSession->mtable_id, 'cid' => $modelMtableSession->mtable->mtable_category_id, 'sessId' => $modelMtableSession->id]);
            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionTransferMenu() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $modelMtableSession = MtableSession::find()
                ->andWhere(['mtable_session.mtable_id' => $post['mtable_id']])
                ->andWhere(['mtable_session.is_closed' => 0])
                ->one();

        if (($flag = !empty($modelMtableSession))) {

            $jumlahHarga = 0;

            foreach ($post['order_id'] as $orderId) {

                $modelMtableOrder = MtableOrder::findOne($orderId);
                $modelMtableOrder->mtable_session_id = $modelMtableSession->id;

                $subtotal = $modelMtableOrder->harga_satuan * $modelMtableOrder->jumlah;
                $disc = 0;

                if ($modelMtableOrder->discount_type == 'Percent') {

                    $disc = $modelMtableOrder->discount * 0.01 * $subtotal;
                } else if ($modelMtableOrder->discount_type == 'Value') {

                    $disc = $modelMtableOrder->jumlah * $modelMtableOrder->discount;
                }

                $jumlahHarga += $subtotal - $disc;

                if (!($flag = $modelMtableOrder->save())) {
                    break;
                }
            }

            if ($flag) {

                $modelMtableSession->jumlah_harga = $modelMtableSession->jumlah_harga + $jumlahHarga;

                if (($flag = $modelMtableSession->save())) {

                    $modelMtableSession = MtableSession::findOne($post['sess_id']);
                    $modelMtableSession->jumlah_harga = $modelMtableSession->jumlah_harga - $jumlahHarga;

                    $flag = $modelMtableSession->save();
                }
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['open_table'] = Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/view-session', 'id' => $modelMtableSession->mtable_id, 'cid' => $modelMtableSession->mtable->mtable_category_id, 'sessId' => $modelMtableSession->id]);
            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionJoinTable() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $message = '';

        if (($flag = !empty(($modelMtableSessionFrom = MtableSession::findOne($post['sess_id']))))) {

            $modelMtableSessionTo = MtableSession::find()
                    ->andWhere(['mtable_session.mtable_id' => $post['mtable_id']])
                    ->andWhere(['mtable_session.is_closed' => 0])
                    ->one();

            if (($flag = !empty($modelMtableSessionTo))) {

                if (!$modelMtableSessionTo->is_join_mtable) {

                    $modelMtableJoin = new MtableJoin();
                    $modelMtableJoin->active_mtable_session_id = $modelMtableSessionTo->id;
                } else {

                    $modelMtableSessionJoinTo = MtableSessionJoin::findOne(['mtable_session_id' => $modelMtableSessionTo->id]);

                    $modelMtableSessionTo = $modelMtableSessionJoinTo->mtableJoin->activeMtableSession;

                    $modelMtableJoin = $modelMtableSessionJoinTo->mtableJoin;
                    $modelMtableJoin->active_mtable_session_id = $modelMtableSessionTo->id;
                }

                $jumlahHarga = 0;

                if (!empty($post['order_id'])) {

                    foreach ($post['order_id'] as $orderId) {

                        $modelMtableOrder = MtableOrder::findOne($orderId);
                        $modelMtableOrder->mtable_session_id = $modelMtableJoin->active_mtable_session_id;

                        $subtotal = $modelMtableOrder->harga_satuan * $modelMtableOrder->jumlah;
                        $disc = 0;

                        if ($modelMtableOrder->discount_type == 'Percent') {

                            $disc = $modelMtableOrder->discount * 0.01 * $subtotal;
                        } else if ($modelMtableOrder->discount_type == 'Value') {

                            $disc = $modelMtableOrder->jumlah * $modelMtableOrder->discount;
                        }

                        $jumlahHarga += $subtotal - $disc;

                        if (!($flag = $modelMtableOrder->save())) {
                            break;
                        }
                    }
                }

                if (($flag = ($flag && $modelMtableJoin->save()))) {

                    if (!$modelMtableSessionTo->is_join_mtable) {

                        $modelMtableSessionJoinTo = new MtableSessionJoin();
                        $modelMtableSessionJoinTo->mtable_session_id = $modelMtableSessionTo->id;
                        $modelMtableSessionJoinTo->mtable_join_id = $modelMtableJoin->id;
                    } else {
                        $modelMtableSessionJoinTo->mtable_join_id = $modelMtableJoin->id;
                    }

                    if (!$modelMtableSessionFrom->is_join_mtable) {

                        $modelMtableSessionJoinFrom = new MtableSessionJoin();
                        $modelMtableSessionJoinFrom->mtable_session_id = $modelMtableSessionFrom->id;
                        $modelMtableSessionJoinFrom->mtable_join_id = $modelMtableJoin->id;
                    } else {

                        $modelMtableSessionJoinFrom = MtableSessionJoin::findOne(['mtable_session_id' => $modelMtableSessionFrom->id]);

                        foreach ($modelMtableSessionJoinFrom->mtableJoin->mtableSessionJoins as $mtableSessionJoin) {

                            $mtableSessionJoin->mtable_join_id = $modelMtableJoin->id;

                            if (!($flag = $mtableSessionJoin->save())) {
                                break;
                            }
                        }
                    }

                    if (($flag = ($modelMtableSessionJoinTo->save() && $modelMtableSessionJoinFrom->save()))) {

                        $modelMtableSessionTo->is_join_mtable = 1;
                        $modelMtableSessionTo->jumlah_harga = $modelMtableSessionTo->jumlah_harga + $jumlahHarga;

                        $modelMtableSessionFrom->is_join_mtable = 1;
                        $modelMtableSessionFrom->jumlah_harga = 0;
                        $modelMtableSessionFrom->discount = 0;
                        $modelMtableSessionFrom->pajak = 0;
                        $modelMtableSessionFrom->service_charge = 0;

                        $flag = ($modelMtableSessionTo->save() && $modelMtableSessionFrom->save());
                    }
                }
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['open_table'] = Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/view-session', 'id' => $modelMtableJoin->activeMtableSession->mtable_id, 'cid' => $modelMtableJoin->activeMtableSession->mtable->mtable_category_id, 'sessId' => $modelMtableJoin->activeMtableSession->id]);
            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['message'] = $message;
            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
    
    public function actionPaymentCorrection() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        if (($flag = !empty(($modelSaleInvoice = SaleInvoice::findOne(['mtable_session_id' => $post['sess_id']]))))) {

            $modelSaleInvoiceCorrection = new SaleInvoiceCorrection();

            $modelSaleInvoiceCorrection->sale_invoice_id = $modelSaleInvoice->id;
            $modelSaleInvoiceCorrection->date = $modelSaleInvoice->date;
            $modelSaleInvoiceCorrection->mtable_session_id = $modelSaleInvoice->mtable_session_id;
            $modelSaleInvoiceCorrection->user_operator = $modelSaleInvoice->user_operator;
            $modelSaleInvoiceCorrection->jumlah_harga = $modelSaleInvoice->jumlah_harga;
            $modelSaleInvoiceCorrection->discount_type = $modelSaleInvoice->discount_type;
            $modelSaleInvoiceCorrection->discount = $modelSaleInvoice->discount;
            $modelSaleInvoiceCorrection->pajak = $modelSaleInvoice->pajak;
            $modelSaleInvoiceCorrection->service_charge = $modelSaleInvoice->service_charge;
            $modelSaleInvoiceCorrection->jumlah_bayar = $modelSaleInvoice->jumlah_bayar;
            $modelSaleInvoiceCorrection->jumlah_kembali = $modelSaleInvoice->jumlah_kembali;

            if (($flag = $modelSaleInvoiceCorrection->save())) {

                foreach ($modelSaleInvoice->saleInvoiceTrxes as $modelSaleInvoiceTrx) {

                    $modelSaleInvoiceTrxCorrection = new SaleInvoiceTrxCorrection();
                    $modelSaleInvoiceTrxCorrection->sale_invoice_correction_id = $modelSaleInvoiceCorrection->id;
                    $modelSaleInvoiceTrxCorrection->menu_id = $modelSaleInvoiceTrx->menu_id;
                    $modelSaleInvoiceTrxCorrection->catatan = $modelSaleInvoiceTrx->catatan;
                    $modelSaleInvoiceTrxCorrection->jumlah = $modelSaleInvoiceTrx->jumlah;
                    $modelSaleInvoiceTrxCorrection->discount_type = $modelSaleInvoiceTrx->discount_type;
                    $modelSaleInvoiceTrxCorrection->discount = $modelSaleInvoiceTrx->discount;
                    $modelSaleInvoiceTrxCorrection->harga_satuan = $modelSaleInvoiceTrx->harga_satuan;
                    $modelSaleInvoiceTrxCorrection->is_free_menu = $modelSaleInvoiceTrx->is_free_menu;

                    if (($flag = ($modelSaleInvoiceTrxCorrection->save() && $modelSaleInvoiceTrx->delete()))) {

                        $modelMenuRecipe = MenuRecipe::find()
                                ->joinWith([
                                    'menu',
                                    'item',
                                    'itemSku',
                                ])
                                ->andWhere(['menu_recipe.menu_id' => $modelSaleInvoiceTrxCorrection->menu_id])
                                ->asArray()->all();


                        if (count($modelMenuRecipe) > 0) {

                            foreach ($modelMenuRecipe as $dataMenuRecipe) {

                                if (empty($dataMenuRecipe['itemSku']['storage_id'])) {

                                    $return['message'] = 'Item pada resep salah satu menu, belum disetting storagenya untuk pengurangan item.';
                                    $flag = false;
                                    break;
                                }

                                $flag = Stock::setStock(
                                        $dataMenuRecipe['item_id'],
                                        $dataMenuRecipe['item_sku_id'],
                                        $dataMenuRecipe['itemSku']['storage_id'],
                                        $dataMenuRecipe['itemSku']['storage_rack_id'],
                                        $dataMenuRecipe['jumlah'] * $modelSaleInvoiceTrxCorrection->jumlah
                                );

                                if ($flag) {

                                    $modelStockMovement = new StockMovement();
                                    $modelStockMovement->type = 'Inflow';
                                    $modelStockMovement->item_id = $dataMenuRecipe['item_id'];
                                    $modelStockMovement->item_sku_id = $dataMenuRecipe['item_sku_id'];
                                    $modelStockMovement->storage_to = $dataMenuRecipe['itemSku']['storage_id'];
                                    $modelStockMovement->storage_rack_to = $dataMenuRecipe['itemSku']['storage_rack_id'];
                                    $modelStockMovement->jumlah = $dataMenuRecipe['jumlah'] * $modelSaleInvoiceTrxCorrection->jumlah;

                                    Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                                    $modelStockMovement->tanggal = Yii::$app->formatter->asDate(time());
                                    Yii::$app->formatter->timeZone = 'UTC';

                                    if (!($flag = $modelStockMovement->save())) {
                                        break;
                                    }
                                } else {
                                    break;
                                }
                            }
                        }
                    }

                    if (!$flag) {
                        break;
                    }
                }

                if ($flag) {

                    foreach ($modelSaleInvoice->saleInvoicePayments as $modelSaleInvoicePayment) {

                        $modelSaleInvoicePaymentCorrection = new SaleInvoicePaymentCorrection();
                        $modelSaleInvoicePaymentCorrection->sale_invoice_correction_id = $modelSaleInvoiceCorrection->id;
                        $modelSaleInvoicePaymentCorrection->payment_method_id = $modelSaleInvoicePayment->payment_method_id;
                        $modelSaleInvoicePaymentCorrection->jumlah_bayar = $modelSaleInvoicePayment->jumlah_bayar;
                        $modelSaleInvoicePaymentCorrection->keterangan = $modelSaleInvoicePayment->keterangan;

                        if (!($flag = ($modelSaleInvoicePaymentCorrection->save() && $modelSaleInvoicePayment->delete()))) {
                            break;
                        }
                    }
                }
            }

            if ($flag) {

                $modelSaleInvoice->date = Yii::$app->formatter->asDatetime(time());
                $modelSaleInvoice->mtable_session_id = $post['sess_id'];
                $modelSaleInvoice->user_operator = Yii::$app->user->identity->id;
                $modelSaleInvoice->jumlah_harga = $post['jumlah_harga'];
                $modelSaleInvoice->discount_type = $post['discount_type'];
                $modelSaleInvoice->discount = $post['discount'];
                $modelSaleInvoice->pajak = $post['pajak'];
                $modelSaleInvoice->service_charge = $post['service_charge'];
                $modelSaleInvoice->jumlah_bayar = $post['jumlah_bayar'];
                $modelSaleInvoice->jumlah_kembali = $post['jumlah_kembali'];

                if (($flag = $modelSaleInvoice->save())) {

                    foreach ($post['order_id'] as $order) {

                        $modelSaleInvoiceTrx = new SaleInvoiceTrx();
                        $modelSaleInvoiceTrx->sale_invoice_id = $modelSaleInvoice->id;
                        $modelSaleInvoiceTrx->menu_id = $order['menu_id'];
                        $modelSaleInvoiceTrx->catatan = $order['catatan'];
                        $modelSaleInvoiceTrx->jumlah = $order['jumlah'];
                        $modelSaleInvoiceTrx->discount_type = $order['discount_type'];
                        $modelSaleInvoiceTrx->discount = $order['discount'];
                        $modelSaleInvoiceTrx->harga_satuan = $order['harga_satuan'];
                        $modelSaleInvoiceTrx->is_free_menu = $order['is_free_menu'];

                        if (($flag = $modelSaleInvoiceTrx->save())) {

                            $modelMenuRecipe = MenuRecipe::find()
                                    ->joinWith([
                                        'menu',
                                        'item',
                                        'itemSku',
                                    ])
                                    ->andWhere(['menu_recipe.menu_id' => $order['menu_id']])
                                    ->asArray()->all();


                            if (count($modelMenuRecipe) > 0) {

                                foreach ($modelMenuRecipe as $dataMenuRecipe) {

                                    if (empty($dataMenuRecipe['itemSku']['storage_id'])) {

                                        $return['message'] = 'Item pada resep salah satu menu, belum disetting storagenya untuk pengurangan item.';
                                        $flag = false;
                                        break;
                                    }

                                    $flag = Stock::setStock(
                                            $dataMenuRecipe['item_id'],
                                            $dataMenuRecipe['item_sku_id'],
                                            $dataMenuRecipe['itemSku']['storage_id'],
                                            $dataMenuRecipe['itemSku']['storage_rack_id'],
                                            -1 * $dataMenuRecipe['jumlah'] * $order['jumlah']
                                    );

                                    if ($flag) {

                                        $modelStockMovement = new StockMovement();
                                        $modelStockMovement->type = 'Outflow-Menu';
                                        $modelStockMovement->item_id = $dataMenuRecipe['item_id'];
                                        $modelStockMovement->item_sku_id = $dataMenuRecipe['item_sku_id'];
                                        $modelStockMovement->storage_from = $dataMenuRecipe['itemSku']['storage_id'];
                                        $modelStockMovement->storage_rack_from = $dataMenuRecipe['itemSku']['storage_rack_id'];
                                        $modelStockMovement->jumlah = $dataMenuRecipe['jumlah'] * $order['jumlah'];

                                        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
                                        $modelStockMovement->tanggal = Yii::$app->formatter->asDate(time());
                                        Yii::$app->formatter->timeZone = 'UTC';

                                        if (!($flag = $modelStockMovement->save())) {
                                            break;
                                        }
                                    } else {
                                        break;
                                    }
                                }
                            }
                        }

                        if (!$flag) {
                            break;
                        }
                    }

                    if ($flag && !empty($post['payment'])) {

                        foreach ($post['payment'] as $payment) {

                            $modelSaleInvoicePayment = new SaleInvoicePayment();
                            $modelSaleInvoicePayment->sale_invoice_id = $modelSaleInvoice->id;
                            $modelSaleInvoicePayment->payment_method_id = $payment['payment_method_id'];
                            $modelSaleInvoicePayment->jumlah_bayar = $payment['jumlah_bayar'];
                            $modelSaleInvoicePayment->keterangan = $payment['keterangan'];

                            if ($payment['payment_method_id'] == 'XLIMIT') {
                                $modelEmployee = Employee::findOne($payment['kode']);
                                $modelEmployee->sisa -= $payment['jumlah_bayar'];

                                if (!($flag = $modelEmployee->save())) {
                                    break;
                                }
                            } else if ($payment['payment_method_id'] == 'XVCHR') {
                                $modelVoucher = Voucher::findOne($payment['kode']);
                                $modelVoucher->not_active = true;

                                if (!($flag = $modelVoucher->save())) {
                                    break;
                                }
                            }

                            if (!($flag = $modelSaleInvoicePayment->save())) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['id'] = $modelSaleInvoice->id;
            $return['table'] = Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/correction-invoice']);
            $return['is_correction'] = true;
            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionQueueFinish($id) {

        $flag = true;

        $modelMtableOrderQueue = MtableOrderQueue::findOne($id);
        $modelMtableOrderQueue->is_finish = 1;

        $flag = $modelMtableOrderQueue->save();

        $return = [];

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionQueueSend($id) {

        $flag = true;

        $modelMtableOrderQueue = MtableOrderQueue::findOne($id);
        $modelMtableOrderQueue->is_send = 1;

        $flag = $modelMtableOrderQueue->save();

        $return = [];

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionCreateBooking() {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $modelMtableBooking = new MtableBooking();

        if (($flag = $modelMtableBooking->load($post))) {

            if (($modelMtableBooking->id = Settings::getTransNumber('no_booking')) !== false) {

                $flag = $modelMtableBooking->save();
            }
        }

        $return = [];

        if ($flag) {

            $transaction->commit();

            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionBookingOpen($id, $tid) {

        $post = Yii::$app->request->post();

        $transaction = Yii::$app->db->beginTransaction();
        $flag = true;

        $modelMtableSession = MtableSession::find()
                ->andWhere(['mtable_id' => $tid])
                ->andWhere(['is_closed' => 0])
                ->asArray()->all();

        $return = [];

        if (empty($modelMtableSession)) {

            $modelMtableBooking = MtableBooking::findOne($id);

            $modelMtableBooking->is_closed = 1;

            if (($flag = $modelMtableBooking->save())) {

                $modelSettings = Settings::getSettingsByName(['tax_amount', 'service_charge_amount']);

                $modelMtableSession = new MtableSession();
                $modelMtableSession->mtable_id = $modelMtableBooking->mtable_id;
                $modelMtableSession->nama_tamu = $modelMtableBooking->nama_pelanggan;
                $modelMtableSession->pajak = $modelMtableBooking->mtable->not_ppn ? 0 :$modelSettings['tax_amount'] ;
                $modelMtableSession->service_charge = $modelMtableBooking->mtable->not_service_charge ? 0 : $modelSettings['service_charge_amount'];
                $modelMtableSession->opened_at = Yii::$app->formatter->asDatetime(time());
                $modelMtableSession->user_opened = Yii::$app->user->identity->id;

                $flag = $modelMtableSession->save();
            }
        } else {

            $flag = false;
            $return['message'] = 'Tidak bisa melakukan open table karena meja sudah diisi.';
        }

        if ($flag) {

            $transaction->commit();

            $return['open_table'] = Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['full'] . 'home/view-session', 'id' => $modelMtableBooking->mtable_id, 'cid' => $modelMtableBooking->mtable->mtable_category_id, 'sessId' => $modelMtableSession->id]);
            $return['success'] = true;
        } else {

            $transaction->rollBack();

            $return['success'] = false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }
}
