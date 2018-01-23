<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\Siswa;
use restotech\standard\backend\models\search\SiswaSearch;
use common\models\User;
use restotech\standard\backend\models\SaldoDeposit;
use restotech\standard\backend\models\SaldoDepositHistori;
use restotech\standard\backend\models\Pendaftaran;
use restotech\standard\backend\models\PendaftaranWorkInfo;
use restotech\standard\backend\models\LogPendaftaranAction;
use restotech\standard\backend\models\KelaskbmMurid;
use restotech\standard\backend\models\Settings;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use restotech\standard\backend\components\Tools;


/**
 * SiswaController implements the CRUD actions for Siswa model.
 */
class SiswaController extends BackendController
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
                        'create-user' => ['post'],
                        'create-siswa' => ['post'],
                    ],
                ],
            ]);
    }

    /**
     * Lists all Siswa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SiswaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Siswa model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Siswa model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Siswa();
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = true;
            
            if (($flag = $model->nis = Settings::getNoInduk('nis', $model->tanggal_lahir)) !== false) {
                if (($model->foto_file = Tools::uploadFile('/img/siswa/', $model, 'foto_file', 'nis')))
                    $flag = true;
            }
            
            if ($model->save() && $flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Tambah Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses tambah data sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['update', 'id' => $model->nis]);
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
        ]);
    }

    /**
     * Updates an existing Siswa model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;            
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $flag = false;
            
            if (($model->foto_file = Tools::uploadFile('/img/siswa/', $model, 'foto_file', 'nis'))) {                                      
                $flag = true;        
            } else {
                $flag = true;
                $model->foto_file = $model->oldAttributes['foto_file'];
            }
            
            if ($model->save() && $flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                return $this->redirect(['update', 'id' => $model->nis]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
            }                        
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Siswa model.
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
    
    public function actionCreateSiswa($id, $actid, $spid) {
                
        $post = Yii::$app->request->post();
        
        $modelPendaftaranWorkInfo = new PendaftaranWorkInfo();
        if ($modelPendaftaranWorkInfo->load($post)) {
            $modelPendaftaranWorkInfo->status_pendaftaran_id = $spid;
            $modelPendaftaranWorkInfo->waktu = date('Y-m-d H:i:s');        
            
            if (!$modelPendaftaranWorkInfo->save()) {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Work Info Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                return $this->redirect(['update-status', 'id' => $id]);
            }
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        $modelPendaftaran = Pendaftaran::findOne($id);
          
        $modelTemp['Siswa'] = $modelPendaftaran->getAttributes();
        
        $modelSiswa = new Siswa();
        $modelSiswa->load($modelTemp);   
        $modelSiswa->pendaftaran_id = $modelPendaftaran->id;

        if (($flag = $modelSiswa->nis = Settings::getNoInduk('nis', $modelSiswa->tanggal_lahir)) !== false) {
            if (($flag = $modelSiswa->save())) {                                                         
                $flag = KelaskbmMurid::updateAll(['siswa_nis' => $modelSiswa->nis], ['pendaftaran_id' => $modelPendaftaran->id]) > 0;
            }
        }
        
        if ($flag && !empty($modelPendaftaran->pendaftarans)) {
            foreach ($modelPendaftaran->pendaftarans as $valuePendaftarans) {
                $modelTemp['Siswa'] = $valuePendaftarans->getAttributes();

                $modelSiswa = new Siswa();
                $modelSiswa->load($modelTemp);   
                $modelSiswa->pendaftaran_id = $valuePendaftarans->id;

                if (($flag = $modelSiswa->nis = Settings::getNoInduk('nis', $modelSiswa->tanggal_lahir)) !== false) {
                    if (($flag = $modelSiswa->save())) {
                        $flag = KelaskbmMurid::updateAll(['siswa_nis' => $modelSiswa->nis], ['pendaftaran_id' => $modelPendaftaran->id]) > 0;
                    }
                }
            }
        }
        
        if ($flag) {
            $modelLogPendaftaranAction = new LogPendaftaranAction();
            $modelLogPendaftaranAction->pendaftaran_id = $id;
            $modelLogPendaftaranAction->action_id = $actid;

            $flag = $modelLogPendaftaranAction->save();
        }
        
        if ($flag) {
            Yii::$app->session->setFlash('status', 'success');
            Yii::$app->session->setFlash('message1', 'Create Siswa Sukses');
            Yii::$app->session->setFlash('message2', 'Proses create siswa sukses. Data berhasil disimpan');

            $transaction->commit();           
        } else {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Create Siswa Gagal');
            Yii::$app->session->setFlash('message2', 'Proses create siswa gagal. Data gagal disimpan');

            $transaction->rollBack();
        }
        
        return $this->redirect(['pendaftaran/update-status', 'id' => $id]);
    }
    
    public function actionCreateUser($id) 
    {
        $modelSiswa = $this->findModel($id);
        
        $transaction = Yii::$app->db->beginTransaction();
        $flag = false;
        
        if (!$modelSiswa->is_user_created) {

            $modelSiswa->is_user_created = 1;

            if (($flag = $modelSiswa->save())) {
                $modelUser = new User();
                $modelUser->id = $modelSiswa->nis;
                $modelUser->nama = $modelSiswa->nama_lengkap;

                $password = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789', 5)), 0, 7);
                $modelUser->setPassword($password); 
                
                $modelUser->user_level_id = 4;
                $modelUser->siswa_nis = $modelSiswa->nis;
                $modelUser->image = 'ppdefault.png';

                $flag = $modelUser->save();
            }

            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Create User Sukses');

                $message2 = 'Proses create user sukses. Data telah berhasil disimpan.<br><br>';
                $message2 .= 'User ID: <strong>' . $modelUser->id . '</strong><br>';
                $message2 .= 'Password: <strong>' . $password . '</strong><br>';
                Yii::$app->session->setFlash('message2', $message2);    

                $transaction->commit();
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Create User Gagal');
                Yii::$app->session->setFlash('message2', 'Proses create user gagal. Data gagal disimpan.');

                $transaction->rollBack();
            }
        } else {
            Yii::$app->session->setFlash('status', 'danger');
            Yii::$app->session->setFlash('message1', 'Create User Gagal');
            
            $message2 = 'Proses create user gagal. Data gagal disimpan.<br>';
            $message2 .= 'Siswa bersangkutan sudah dibuatkan user<br>';
            Yii::$app->session->setFlash('message2', $message2);
        }
        
        return $this->redirect(['index']);
    }
    
    public function actionDeposit($id) {
        
        $modelSiswa = Siswa::find()
                ->joinWith([
                    'pendaftaran',
                    'saldoDeposit',
                ])
                ->andWhere(['siswa.nis' => $id])
                ->one();
        
        if (Yii::$app->request->isPost) {
            
            $post = Yii::$app->request->post();                        
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            if (!empty($post)) {

                $modelDeposit = SaldoDeposit::findOne($id);
                $jumlahSaldo = 0;

                if ($modelDeposit !== null) {
                    $modelDeposit->load($post);

                    $jumlahSaldo = $modelDeposit->jumlah_saldo;

                    $modelDeposit->jumlah_saldo = $modelDeposit->jumlah_saldo + $modelDeposit->getOldAttribute('jumlah_saldo');                           
                } else {
                    $modelDeposit = new SaldoDeposit();
                    $modelDeposit->load($post);

                    $jumlahSaldo = $modelDeposit->jumlah_saldo;

                    $modelDeposit->siswa_nis = $id;
                }
                
                if (($flag = $modelDeposit->save())) {

                    $modelSaldoDepositHistori = new SaldoDepositHistori();
                    $modelSaldoDepositHistori->siswa_nis = $modelDeposit->siswa_nis;
                    $modelSaldoDepositHistori->saldo_masuk = $jumlahSaldo;
                    $modelSaldoDepositHistori->tanggal = date('Y-m-d H:i:s');

                    $flag = $modelSaldoDepositHistori->save();
                }
            }
            
            if ($flag) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Update Sukses');
                Yii::$app->session->setFlash('message2', 'Proses update sukses. Data telah berhasil disimpan.');
                
                $transaction->commit();
                
                return $this->redirect(['deposit', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Update Gagal');
                Yii::$app->session->setFlash('message2', 'Proses update gagal. Data gagal disimpan.');
                
                $transaction->rollBack();
            }       
        }
        
        return $this->render('deposit', [
            'modelSiswa' => $modelSiswa,
            'newSaldoDeposit' => new SaldoDeposit(),
        ]);
    }
    
    public function actionHistoriDeposit($id) {
        
        $model = Siswa::find()
                ->joinWith([
                    'pendaftaran',
                    'saldoDeposit',
                    'saldoDeposit.saldoDepositHistoris'
                ])
                ->andWhere(['siswa.nis' => $id])
                ->one();
        
        if ($model === null)            
            throw new NotFoundHttpException('The requested page does not exist.');
        
        return $this->render('histori_deposit', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Siswa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Siswa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Siswa::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
