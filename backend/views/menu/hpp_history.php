<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\DynamicTable;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Menu */

$dynamicTableMenuHpp = new DynamicTable([
    'model' => $modelMenuHpp,
    'tableFields' => [
        'date',
        'harga_pokok:currency',
    ],
    'dataProvider' => $dataProviderMenuHpp,
    'title' => 'History HPP',
    'columnClass' => 'col-sm-8 col-sm-offset-2'
]);

$this->title = 'History HPP';
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="menu">
    
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">  
                
                <div class="box-header">
                    <h3 class="box-title">

                        <?= Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;' . 'Cancel', 
                            ['update', 'id' => $model->id], 
                            [
                                'class' => 'btn btn-default',
                            ]) ?>
                    </h3>
                </div>

                <?= DetailView::widget([
                    'model' => $model,
                    'options' => [
                        'class' => 'table'
                    ],
                    'attributes' => [
                        'id',
                        'nama_menu',
                        'harga_pokok:currency',
                        'harga_jual:currency',
                    ],
                ]) ?>

            </div>
        </div>
    </div>

    <?= $dynamicTableMenuHpp->tableData() ?>

</div>