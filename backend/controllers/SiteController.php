<?php

namespace restotech\standard\backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;
use restotech\standard\common\models\LoginForm;


/**
 * Site controller
 */
class SiteController extends BackendController {

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
                        'logout' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        $this->layout = 'zero';

        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error',
            ],
        ];
    }

    public function actionIndex() {
        
        $this->actionDefault();
    }

    public function actionLogin() {
        
        $this->layout = 'zero';
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $post = Yii::$app->request->post();
        $model = new LoginForm();
        if (!empty($post['loginButton']) && $model->load($post) && $model->login()) {

            return $this->redirect(Yii::$app->session->get('user_data')['user_level']['default_action']);
        } else {
            if (!empty($post['setVirtualKeyboard'])) {
                Yii::$app->session->set('showVirtualKeyboard', empty($post['showVirtualKeyboard']) ? false : true);
            }
            
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }        

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }
  
    public function actionDefault() {
        
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }
        
        return $this->redirect(Yii::$app->session->get('user_data')['user_level']['default_action']);
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
