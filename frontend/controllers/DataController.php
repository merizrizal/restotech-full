<?php

namespace restotech\standard\frontend\controllers;

use Yii;
use restotech\standard\backend\models\MenuCategory;
use restotech\standard\backend\models\Menu;
use restotech\standard\backend\models\MenuCondiment;
use restotech\standard\backend\models\MtableCategory;
use restotech\standard\backend\models\Mtable;
use restotech\standard\backend\models\Employee;
use restotech\standard\backend\models\Voucher;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Data controller
 */
class DataController extends FrontendController {

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
                        'table-layout' => ['post'],
                        'info-table' => ['post'],
                        'search-menu' => ['post'],
                        'menu-category' => ['post'],
                        'menu' => ['post'],
                        'table-category' => ['post'],
                        'table' => ['post'],
                    ],
                ],
            ]);
    }

    public function actionDatetime() {

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $datetime = [];

        $datetime['datetime'] = Yii::$app->formatter->asDatetime(time(), 'dd-MM-yyyy HH:mm');

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $datetime;
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

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionInfoTable() {

        $this->layout = 'ajax';

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

        return $this->render('_info_table', [
            'mtable' => $modelMtable,
        ]);
    }

    public function actionSearchMenu() {

        $this->layout = 'ajax';

        $post = Yii::$app->request->post();

        $modelMenu = Menu::find()
                ->joinWith([
                    'menuRecipes',
                    'menuRecipes.itemSku',
                    'menuRecipes.itemSku.stocks',
                    'menuCategory',
                    'menuCategory.menuCategoryPrinters',
                    'menuCategory.menuCategoryPrinters.printer0',
                    'menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                ])
                ->andFilterWhere(['LIKE', 'menu.nama_menu', $post['namaMenu']])
                ->andWhere(['menu.not_active' => 0])
                ->andWhere(['menu.is_deleted' => 0])
                ->asArray()->all();

        return $this->render('_menu', [
            'modelMenu' => $modelMenu,
            'search' => true,
        ]);
    }

    public function actionMenuCategory() {

        $this->layout = 'ajax';

        $post = Yii::$app->request->post();

        $modelMenuCategory = MenuCategory::find();

        if (!empty($post['id'])) {
            $modelMenuCategory = $modelMenuCategory->andWhere(['menu_category.parent_category_id' => $post['id']]);
        } else {
            $modelMenuCategory = $modelMenuCategory->andWhere(['IS', 'menu_category.parent_category_id', NULL]);
        }

        $modelMenuCategory = $modelMenuCategory
                ->andWhere(['menu_category.not_active' => 0])
                ->asArray()->all();

        return $this->render('_menu_category', [
            'modelMenuCategory' => $modelMenuCategory,
            'pid' => !empty($post['id']) ? $post['id'] : null,
        ]);
    }

    public function actionMenu() {

        $this->layout = 'ajax';

        $post = Yii::$app->request->post();

        $modelMenu = Menu::find()
                ->joinWith([
                    'menuRecipes',
                    'menuRecipes.itemSku',
                    'menuRecipes.itemSku.stocks',
                    'menuCategory',
                    'menuCategory.menuCategoryPrinters',
                    'menuCategory.menuCategoryPrinters.printer0',
                    'menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                ])
                ->andWhere(['menu.menu_category_id' => $post['id']])
                ->andWhere(['menu.not_active' => 0])
                ->andWhere(['menu.is_deleted' => 0])
                ->asArray()->all();

        return $this->render('_menu', [
            'modelMenu' => $modelMenu,
            'cid' => !empty($modelMenu[0]['menuCategory']['parent_category_id']) ? $modelMenu[0]['menuCategory']['parent_category_id'] : MenuCategory::find()->andWhere(['id' => $post['id']])->asArray()->one()['parent_category_id'],
            'search' => false,
        ]);
    }

    public function actionCondiment() {

        $this->layout = 'ajax';

        $post = Yii::$app->request->post();

        $modelMenu = MenuCondiment::find()
                ->joinWith([
                    'menu',
                    'menu.menuRecipes',
                    'menu.menuRecipes.itemSku',
                    'menu.menuRecipes.itemSku.stocks',
                    'menu.menuCategory',
                    'menu.menuCategory.menuCategoryPrinters',
                    'menu.menuCategory.menuCategoryPrinters.printer0',
                    'menu.menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                ])
                ->andWhere(['menu_condiment.parent_menu_id' => $post['parent_id']])
                ->andWhere(['menu.not_active' => 0])
                ->andWhere(['menu.is_deleted' => 0])
                ->asArray()->all();

        return $this->render('_condiment', [
            'modelMenu' => $modelMenu,
            'orderParentId' => $post['order_parent_id'],
        ]);
    }

    public function actionTableCategory($isOpened = false) {

        $this->layout = 'ajax';

        $post = Yii::$app->request->post();

        $modelMtableCategory = MtableCategory::find()
                ->andWhere(['!=', 'mtable_category.not_active', 1])
                ->orderBy('mtable_category.nama_category')
                ->asArray()->all();

        return $this->render('_table_category', [
            'modelMtableCategory' => $modelMtableCategory,
            'isOpened' => $isOpened,
        ]);
    }

    public function actionTable($id, $isOpened = false) {

        $this->layout = 'ajax';

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

        return $this->render('_table', [
            'modelMtable' => $modelMtable,
            'isOpened' => $isOpened,
        ]);
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

        Yii::$app->response->format = Response::FORMAT_JSON;
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

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
    }

    public function actionGetMtable($id) {

        $data = Mtable::find()->where(['mtable_category_id' => $id])->orderBy('nama_meja')->asArray()->all();
        $row = [];

        foreach ($data as $key => $value) {
            $row[$key]['id'] = $value['id'];
            $row[$key]['text'] = $value['nama_meja'] . ' (' . $value['id'] . ')';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
}