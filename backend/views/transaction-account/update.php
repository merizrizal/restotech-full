<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\TransactionAccount */

$this->title = 'Update Account Transaksi: ' . ' ' . $model->nama_account;
$this->params['breadcrumbs'][] = ['label' => 'Account Transaksi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transaction-account-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
