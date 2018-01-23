<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Voucher */

$this->title = 'Create Voucher';
$this->params['breadcrumbs'][] = ['label' => 'Voucher', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voucher-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
