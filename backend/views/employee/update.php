<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Employee */

$this->title = 'Update Karyawan: ' . ' (' . $model->kd_karyawan . ') ' . $model->nama;
$this->params['breadcrumbs'][] = ['label' => 'Karyawan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => ' (' . $model->kd_karyawan . ') ' . $model->nama, 'url' => ['view', 'id' => $model->kd_karyawan]];
$this->params['breadcrumbs'][] = 'Update'; ?>

<div class="employee-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
