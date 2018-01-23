<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\UserLevel */

$this->title = 'Create User Level';
$this->params['breadcrumbs'][] = ['label' => 'User Level', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-level-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelUserAppModule' => $modelUserAppModule,
    ]) ?>

</div>
