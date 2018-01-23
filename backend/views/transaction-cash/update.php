<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\TransactionCash */

$this->title = 'Update ' . $title[$model->account->account_type] . ': ' . ' ' . $model->account->nama_account;
$this->params['breadcrumbs'][] = ['label' => $title[$model->account->account_type], 'url' => ['index', 'type' => $model->account->account_type]];
$this->params['breadcrumbs'][] = ['label' => $model->account->nama_account, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="transaction-cash-update">

    <?= $this->render('_form', [
        'model' => $model,
        'type' => $model->account->account_type,
    ]) ?>

</div>
