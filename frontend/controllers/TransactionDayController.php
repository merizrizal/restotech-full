<?php

namespace restotech\standard\frontend\controllers;

use Yii;
use restotech\standard\backend\models\TransactionDay;
use restotech\standard\backend\models\MtableSession;
use yii\filters\VerbFilter;

/**
 * Transaction Day controller
 */
class TransactionDayController extends FrontendController {
    
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
                        'start-day' => ['post'],
                        'end-day' => ['post'],
                    ],
                ],
            ]);        
    }
    
    public function actionStartDay() {
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';                
        
        $modelTransactionDay = new TransactionDay();
        $modelTransactionDay->start = Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd HH:mm:ss');                       
            
        $checkTransactionDay = TransactionDay::find()
                ->andWhere(['end' => NULL])
                ->asArray()->all();

        if (!empty($checkTransactionDay) && count($checkTransactionDay) > 0) {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Save Start Day Gagal');
            Yii::$app->session->setFlash('message2', 'Proses update gagal. Harap lakukan proses end day terlebih dahulu untuk transaction day sebelumnya. Data gagal disimpan.');
        } else {
            if ($modelTransactionDay->save()) {                
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Save Start Day Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');                    
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Save Start Day Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data telah berhasil disimpan.');
            }       
        }         
        
        return $this->redirect(['home/index']);
    }
    
    public function actionEndDay() {
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';                
        
        $modelTransactionDay = TransactionDay::find()
                ->andWhere(['IS', 'end', null])
                ->andWhere(['IS NOT', 'start', null])
                ->one();                
        
        $modelTransactionDay = !empty($modelTransactionDay) ? $modelTransactionDay : new TransactionDay();         

        if (!empty($modelTransactionDay->start) && empty($modelTransactionDay->end)) {

            $modelMtableSession = MtableSession::find()
                    ->andWhere(['is_closed' => 0])
                    ->asArray()->all();                

            if (count($modelMtableSession) > 0) {
                Yii::$app->session->setFlash('fail-end-day', true);
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Save End Day Gagal');
                Yii::$app->session->setFlash('message2', 'Proses end day gagal. Data gagal disimpan.<br>Masih ada meja yang dalam keadaan open, silakan selesaikan pembayaran atau close meja terlebih dahulu.');
            } else {
                $modelTransactionDay->end = Yii::$app->formatter->asTime(time(), 'yyyy-MM-dd HH:mm:ss');
                
                if ($modelTransactionDay->save()) {                                    
                    Yii::$app->session->setFlash('status', 'success');
                    Yii::$app->session->setFlash('message1', 'Save Start Day Sukses');
                    Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');                        
                } else {
                    Yii::$app->session->setFlash('fail-end-day', true);
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Save End Day Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                }       
            }
        } else {
            Yii::$app->session->setFlash('fail-end-day', true);
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Save End Day Gagal');
            Yii::$app->session->setFlash('message2', 'Proses update gagal. Start day belum dilakukan. Data gagal disimpan.');
        }
        
        return $this->redirect(['home/index']);
    }
}