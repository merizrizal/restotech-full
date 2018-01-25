<?php

namespace restotech\full\frontend\controllers;

use Yii;
use restotech\standard\backend\models\Mtable;
use restotech\standard\backend\models\MtableCategory;
use restotech\standard\backend\models\MtableSession;
use restotech\standard\backend\models\MtableOrderQueue;
use restotech\standard\backend\models\MtableBooking;
use restotech\standard\backend\models\SaleInvoice;
use restotech\standard\backend\models\Settings;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * Home controller
 */
class HomeController extends \restotech\standard\frontend\controllers\HomeController {
    
    public function beforeAction($action) {

        if (parent::beforeAction($action)) {

            $this->setViewPath('@restotech/full/frontend/views/' . $action->controller->id);

            return true;
        } else {
            return false;
        }
    }

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
                        'room' =>  ['post'],
                        'table' =>  ['post'],
                        'room-layout' =>  ['post'],
                        'open-table' =>  ['post'],
                        'view-session' =>  ['post'],
                        'open-table' =>  ['post'],
                        'opened-table' =>  ['post'],
                        'menu-queue' =>  ['post'],
                        'menu-queue-finished' =>  ['post'],
                        'correction-invoice' =>  ['post'],
                        'correction-invoice-submit' =>  ['post'],
                        'booking' =>  ['post'],
                        'create-booking' =>  ['post'],
                    ],
                ],
            ]);
    }

    public function actionRoom() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $modelMtableCategory = MtableCategory::find()
                ->andWhere(['mtable_category.not_active' => 0])
                ->andWhere(['mtable_category.is_deleted' => 0])
                ->orderBy('nama_category')
                ->asArray()->all();

        return $this->render('_room', [
            'modelMtableCategory' => $modelMtableCategory,
        ]);
    }

    public function actionTable($id) {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $modelMtableCategory = MtableCategory::find()
                    ->joinWith([
                        'mtables' => function($query) {
                            $query->andWhere(['mtable.not_active' => '0'])
                                    ->andWhere(['mtable.is_deleted' => 0]);

                        },
                        'mtables.mtableSessions' => function($query) {
                            $query->onCondition('mtable_session.is_closed = 0');
                        },
                        'mtables.mtableSessions.mtableSessionJoin.mtableJoin',
                        'mtables.mtableSessions.mtableSessionJoin.mtableJoin.activeMtableSession' => function($query) {
                            $query->from('mtable_session active_mtable_session');
                        },
                        'mtables.mtableSessions.mtableSessionJoin.mtableJoin.activeMtableSession.mtable' => function($query) {
                            $query->from('mtable mtable_j');
                        },
                    ])
                    ->andWhere(['mtable_category.id' => $id])
                    ->andWhere(['mtable_category.not_active' => 0])
                    ->asArray()->one();

        return $this->render('_table', [
            'modelMtableCategory' => $modelMtableCategory,
        ]);
    }

    public function actionRoomLayout() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $modelMtableCategory = MtableCategory::find()
                ->andWhere(['mtable_category.not_active' => 0])
                ->andWhere(['mtable_category.is_deleted' => 0])
                ->orderBy('nama_category')
                ->asArray()->all();

        return $this->render('_room_layout', [
            'modelMtableCategory' => $modelMtableCategory,
        ]);
    }

    public function actionViewSession($id, $cid, $sessId = null) {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $modelMtableSession = null;

        if (!empty($sessId)) {

            $modelMtableSession = MtableSession::find()
                    ->andWhere([
                        'mtable_id' => $id,
                        'is_closed' => 0
                    ])->asArray()->all();
        }

        if (count($modelMtableSession) == 1 || empty($sessId)) {
            return $this->runAction('open-table', ['id' => $id, 'cid' => $cid, 'sessId' => $sessId]);
        }

        return $this->render('_view_session', [
            'modelMtable' => Mtable::find()->andWhere(['id' => $id])->asArray()->one(),
            'modelMtableSession' => $modelMtableSession,
        ]);
    }

    public function actionOpenTable($id, $cid, $sessId = null, $isCorrection = false) {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $modelSettings = Settings::getSettingsByName(['tax_amount', 'service_charge_amount']);

        $modelMtableSession = null;

        if (empty($sessId)) {

            $transaction = Yii::$app->db->beginTransaction();

            $modelMtableSession = MtableSession::find()
                    ->andWhere([
                        'mtable_id' => $id,
                        'is_closed' => 0
                    ])->asArray()->one();

            if (empty($modelMtableSession)) {

                $modelMtableSession = new MtableSession();
                $modelMtableSession->mtable_id = $id;
                $modelMtableSession->nama_tamu = '';
                $modelMtableSession->catatan = '';
                $modelMtableSession->pajak = $modelMtableSession->mtable->not_ppn ? 0 :$modelSettings['tax_amount'] ;
                $modelMtableSession->service_charge = $modelMtableSession->mtable->not_service_charge ? 0 : $modelSettings['service_charge_amount'];
                $modelMtableSession->opened_at = Yii::$app->formatter->asDatetime(time());
                $modelMtableSession->user_opened = Yii::$app->user->identity->id;

                if ($modelMtableSession->save()) {

                    $transaction->commit();
                } else {

                    return $this->render('_error', [
                        'tableCategoryId' => $cid,
                        'title' => 'Error open table',
                        'message' => 'Telah terjadi kesalahan saat proses open table.'
                    ]);

                    $transaction->rollBack();
                }
            } else {
                $sessId = $modelMtableSession['id'];

                $transaction->rollBack();
            }
        }

        $modelMtableSession = MtableSession::find()
                ->joinWith([
                    'mtable',
                    'mtableOrders' => function($query) {
                        $query->andOnCondition(['mtable_order.parent_id' => null]);
                    },
                    'mtableOrders.menu',
                    'mtableOrders.menu.menuCategory',
                    'mtableOrders.menu.menuCategory.menuCategoryPrinters',
                    'mtableOrders.menu.menuCategory.menuCategoryPrinters.printer0',
                    'mtableOrders.mtableOrders' => function($query) {
                        $query->from('mtable_order a');
                    },
                    'mtableOrders.mtableOrderQueue',
                ])
                ->andWhere(['mtable_session.id' => !empty($sessId) ? $sessId : $modelMtableSession->id])
                ->orderBy('mtable_order.id ASC')
                ->one();

        return $this->render('_open_table', [
            'modelMtableSession' => $modelMtableSession,
            'settingsArray' => Settings::getSettingsByName('struk_', true),
            'isCorrection' => $isCorrection,
        ]);
    }

    public function actionOpenedTable() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $post = Yii::$app->request->post();

        $namaTamu = !empty($post['nama_tamu']) ? $post['nama_tamu'] : '';

        $query = Mtable::find()
                ->joinWith([
                    'mtableCategory',
                    'mtableSessions' => function($query) {
                        $query->andWhere('mtable_session.is_closed = 0');
                    },
                    'mtableSessions.mtableSessionJoin.mtableJoin',
                    'mtableSessions.mtableSessionJoin.mtableJoin.mtableSessionJoins' => function($query) {
                        $query->from('mtable_session_join mtable_session_join_table');
                    },
                    'mtableSessions.mtableSessionJoin.mtableJoin.activeMtableSession' => function($query) {
                        $query->from('mtable_session active_mtable_session');
                    },
                    'mtableSessions.userOpened.kdKaryawan',
                ])
                ->andWhere(['mtable.not_active' => 0])
                ->andWhere(['like', 'mtable_session.nama_tamu', $namaTamu])
                ->andWhere('CASE WHEN active_mtable_session.id != mtable_session.id THEN FALSE ELSE TRUE END')
                ->orderBy('mtable.nama_meja');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        return $this->render('_opened_table', [
            'dataProvider' => $dataProvider,
            'namaTamu' => $namaTamu,
        ]);
    }

    public function actionMenuQueue() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $query = MtableOrderQueue::find()
                ->joinWith([
                    'menu',
                    'mtableOrder',
                    'mtableOrder.mtableSession',
                ])
                ->andWhere(['mtable_order_queue.is_finish' => 0])
                ->andWhere(['mtable_order_queue.is_send' => 0])
                ->andWhere(['mtable_session.is_closed' => 0])
                ->andWhere(['>', 'mtable_order_queue.jumlah', 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        return $this->render('_menu_queue', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMenuQueueFinished() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $query = MtableOrderQueue::find()
                ->joinWith([
                    'menu',
                    'mtableOrder',
                    'mtableOrder.mtableSession',
                ])
                ->andWhere(['mtable_order_queue.is_finish' => 1])
                ->andWhere(['mtable_order_queue.is_send' => 0])
                ->andWhere(['mtable_session.is_closed' => 0])
                ->andWhere(['>', 'mtable_order_queue.jumlah', 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        return $this->render('_menu_queue_finished', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCorrectionInvoice() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        return $this->render('_input_invoice', [
            'type' => 'correction',
        ]);
    }

    public function actionCorrectionInvoiceSubmit() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $post = Yii::$app->request->post();

        $modelSaleInvoice = SaleInvoice::find()
                ->joinWith([
                    'mtableSession',
                    'mtableSession.mtable',
                    'mtableSession.mtableOrders',
                    'mtableSession.mtableOrders.menu',
                    'saleInvoicePayments',
                    'saleInvoicePayments.paymentMethod',
                ])
                ->andWhere(['sale_invoice.id' => $post['id']])->one();

        if (empty($modelSaleInvoice)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        } else {
            return $this->actionOpenTable($modelSaleInvoice->mtableSession->mtable->id, $modelSaleInvoice->mtableSession->mtable->mtable_category_id, $modelSaleInvoice->mtableSession->id, true);
        }
    }

    public function actionBooking() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        $query = MtableBooking::find()
                ->joinWith([
                    'mtable',
                ])
                ->andWhere(['mtable_booking.is_closed' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        return $this->render('_booking', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateBooking() {

        $this->layout = '@restotech/standard/backend/views/layouts/ajax';

        return $this->render('_create_booking', [
            'model' => new MtableBooking(),
            'modelMtable' => new Mtable(),
        ]);
    }
}