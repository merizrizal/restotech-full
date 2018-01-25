<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\TransactionCash */

$this->title = 'Create ' . $title[$type];
$this->params['breadcrumbs'][] = ['label' => $title[$type], 'url' => ['index', 'type' => $type]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-cash-create">

    <?= $this->render('_form', [
        'model' => $model,
        'type' => $type,
    ]) ?>

</div>
