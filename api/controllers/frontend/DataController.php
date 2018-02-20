<?php

namespace restotech\full\api\controllers\frontend;

use Yii;
use restotech\standard\backend\models\MtableCategory;
use restotech\standard\backend\models\Mtable;
use restotech\standard\backend\models\Employee;
use restotech\standard\backend\models\Voucher;
use yii\filters\VerbFilter;

/**
 * Data controller
 */
class DataController extends \restotech\standard\api\controllers\frontend\DataController {

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return array_merge(
            [],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'table-layout' => ['post'],
                        'info-table' => ['post'],
                        'table-category' => ['post'],
                        'table' => ['post'],
                    ],
                ],
            ]);
    }

    public function actionTableLayout($id) {

        $modelMtable = Mtable::find()
                ->joinWith([
                    'mtableCategory'
                ])
                ->andWhere(['mtable.mtable_category_id' => $id])
                ->andWhere(['mtable.not_active' => 0])
                ->orderBy('mtable.nama_meja')
                ->asArray()->all();

        $return = [];

        $return['table'] = $modelMtable;

        return $return;
    }

    public function actionInfoTable() {

        $post = Yii::$app->request->post();

        $modelMtable = Mtable::find()
                ->joinWith([
                    'mtableCategory',
                    'mtableSessions' => function($query) {
                        $query->onCondition('mtable_session.is_closed = FALSE');
                    },
                    'mtableSessions.mtableSessionJoin.mtableJoin',
                    'mtableSessions.mtableSessionJoin.mtableJoin.activeMtableSession' => function($query) {
                        $query->from('mtable_session active_mtable_session');
                    },
                    'mtableSessions.mtableSessionJoin.mtableJoin.activeMtableSession.mtable' => function($query) {
                        $query->from('mtable mtable_j');
                    },
                ])
                ->andWhere(['mtable.id' => $post['id']])
                ->asArray()->one();

        if (empty($modelMtable)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }

        return [
            'mtable' => $modelMtable,
        ];
    }

    public function actionTableCategory($isOpened = false) {

        $post = Yii::$app->request->post();

        $modelMtableCategory = MtableCategory::find()
                ->andWhere(['!=', 'mtable_category.not_active', 1])
                ->orderBy('mtable_category.nama_category')
                ->asArray()->all();

        return [
            'modelMtableCategory' => $modelMtableCategory,
            'isOpened' => $isOpened,
        ];
    }

    public function actionTable($id, $isOpened = false) {

        $post = Yii::$app->request->post();

        $modelMtable = Mtable::find()
                ->joinWith([
                    'mtableSessions' => function($query) {
                        $query->andOnCondition('mtable_session.is_closed = FALSE');
                    },
                    'mtableCategory',
                ])
                ->andWhere(['!=', 'mtable_category.not_active', 1])
                ->andWhere(['mtable_category.id' => $id])
                ->orderBy('mtable.nama_meja')
                ->asArray()->all();

        return [
            'modelMtable' => $modelMtable,
            'isOpened' => $isOpened,
        ];
    }

    public function actionLimitKaryawan() {

        $post = Yii::$app->request->post();

        $flag = false;

        $return = [];

        if (($flag = !empty(($model = Employee::findOne($post['kode_karyawan']))))) {

            if ($post['jml_limit'] > $model->sisa) {

                $return['message'] = 'Sisa limit karyawan tidak mencukupi.';
                $flag = false;

            } else {
                $flag = true;
            }
        } else {
            $return['message'] = 'Data karyawan tidak bisa ditemukan.';
            $flag = false;
        }

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }
        
        return $return;
    }

    public function actionVoucher() {

        $post = Yii::$app->request->post();

        $flag = false;

        $return = [];

        if (($flag = !empty(($model = Voucher::findOne($post['kode_voucher']))))) {

            Yii::$app->formatter->timeZone = 'Asia/Jakarta';

            $date = strtotime(Yii::$app->formatter->asDate(time()));
            $from = strtotime($model->start_date);
            $to = strtotime($model->end_date);

            if ($model->not_active) {

                $return['message'] = 'Voucher sudah pernah dipakai atau sudah tidak berlaku.';
                $flag = false;
            } else if (!($date >= $from && $date <= $to)) {

                $return['message'] = 'Masa voucher sudah tidak berlaku.';
                $flag = false;
            } else {

                if ($model->voucher_type == 'Percent') {
                    $return['jumlah_voucher'] = round($model->jumlah_voucher * 0.01 * $post['tagihan']);
                } else if ($model->voucher_type == 'Value') {
                    $return['jumlah_voucher'] = $model->jumlah_voucher;
                }

                $flag = true;
            }
        } else {
            $return['message'] = 'Data voucher tidak bisa ditemukan.';
            $flag = false;
        }

        if ($flag) {

            $return['success'] = true;
        } else {

            $return['success'] = false;
        }

        return $return;
    }

    public function actionGetMtable($id) {

        $data = Mtable::find()->where(['mtable_category_id' => $id])->orderBy('nama_meja')->asArray()->all();
        $row = [];

        foreach ($data as $key => $value) {
            $row[$key]['id'] = $value['id'];
            $row[$key]['text'] = $value['nama_meja'] . ' (' . $value['id'] . ')';
        }

        return $row;
    }
}