<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\TransactionDay;
use restotech\standard\backend\models\MtableSession;

use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * TransactionDayController implements the CRUD actions for TransactionDay model.
 */
class TransactionDayController extends BackendController
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

    public function actionStartDay() {
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $post = Yii::$app->request->post();          
        
        $modelTransactionDay = new TransactionDay();
        $modelTransactionDay->start = Yii::$app->formatter->asDate(time(), 'yyyy-MM-dd HH:mm:ss');
        
        if (Yii::$app->request->isAjax && $modelTransactionDay->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($modelTransactionDay);
        }                
        
        if (!empty($post)) {
            
            $checkTransactionDay = TransactionDay::find()
                    ->andWhere(['end' => NULL])
                    ->asArray()->all();
            
            if (!empty($checkTransactionDay) && count($checkTransactionDay) > 0) {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Save Start Day Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Harap lakukan proses end day terlebih dahulu untuk transaction day sebelumnya. Data gagal disimpan.');
            } else {
                if ($modelTransactionDay->load($post)) {
                    if ($modelTransactionDay->save()) {                
                        Yii::$app->session->setFlash('status', 'success');
                        Yii::$app->session->setFlash('message1', 'Save Start Day Sukses');
                        Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');

                        return $this->redirect(['transaction-day/start-day']);
                    } else {
                        Yii::$app->session->setFlash('status', 'danger');
                        Yii::$app->session->setFlash('message1', 'Save Start Day Gagal');
                        Yii::$app->session->setFlash('message2', 'Proses update gagal. Data telah berhasil disimpan.');
                    }       
                }
            }
        }                           
        
        return $this->render('start_day', [
            'modelTransactionDay' => $modelTransactionDay,
        ]);
    }
    
    public function actionEndDay() {
        
        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        
        $post = Yii::$app->request->post();          
        
        $modelTransactionDay = TransactionDay::find()
                ->andWhere(['IS', 'end', null])
                ->andWhere(['IS NOT', 'start', null])
                ->one();
        
        $modelTransactionDay = !empty($modelTransactionDay) ? $modelTransactionDay : new TransactionDay();
        
        if (Yii::$app->request->isAjax && $modelTransactionDay->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($modelTransactionDay);
        }                
        
        if (!empty($post)) {            
            
            if (!empty($modelTransactionDay->start) && empty($modelTransactionDay->end)) {
                
                $modelMtableSession = MtableSession::find()
                        ->andWhere(['is_closed' => 0])
                        ->asArray()->all();                
                
                if (count($modelMtableSession) > 0) {
                    Yii::$app->session->setFlash('status', 'danger');
                    Yii::$app->session->setFlash('message1', 'Save End Day Gagal');
                    Yii::$app->session->setFlash('message2', 'Proses update gagal. Masih ada meja yang dalam keadaan open, silakan selesaikan pembayaran atau close meja terlebih dahulu. Data gagal disimpan.');
                } else {
                    if ($modelTransactionDay->load($post)) {

                        if ($modelTransactionDay->save()) {                
                            Yii::$app->session->setFlash('status', 'success');
                            Yii::$app->session->setFlash('message1', 'Save Start Day Sukses');
                            Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');

                            return $this->redirect(['transaction-day/end-day']);
                        } else {
                            Yii::$app->session->setFlash('status', 'danger');
                            Yii::$app->session->setFlash('message1', 'Save End Day Gagal');
                            Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                        }       
                    }                
                }
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Save End Day Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Start day belum dilakukan. Data gagal disimpan.');
            }
        }            
                
        $modelTransactionDay->end = Yii::$app->formatter->asTime(time(), 'yyyy-MM-dd HH:mm:ss');
        
        return $this->render('end_day', [
            'modelTransactionDay' => $modelTransactionDay,
        ]);
    }
}
