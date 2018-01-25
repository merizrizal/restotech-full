<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\TransactionAccount */

$this->title = 'Create Account Transaksi';
$this->params['breadcrumbs'][] = ['label' => 'Account Transaksi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-account-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
