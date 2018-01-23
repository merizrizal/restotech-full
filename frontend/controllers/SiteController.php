<?php
namespace restotech\standard\frontend\controllers;

use Yii;

use yii\filters\VerbFilter;
use yii\web\Response;
use backend\components\Tools;

/**
 * Site controller
 */
class SiteController extends FrontendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $this->layout = 'zero';

        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin() {

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->redirect(Yii::getAlias('@rootUrl/') . Yii::$app->params['subprogram']['administrator']);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionPrint() {
        $data = !empty(Yii::$app->request->post()) ? Yii::$app->request->post() : Yii::$app->request->queryParams;

        $return = [];

        $return['flag'] = Tools::printToServer(\yii\helpers\Json::encode($data));

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $return;
    }


    public function actionGetDatetime() {

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        $datetime = [];
        $datetime['date'] = Yii::$app->formatter->asDatetime(time(), 'EEEE, d LLLL yyyy');
        $datetime['time'] = Yii::$app->formatter->asDatetime(time(), 'HH:mm');

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $datetime;
    }
}