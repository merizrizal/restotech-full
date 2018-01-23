<?php

use yii\helpers\Html;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SaleInvoice */

$this->title = 'Laporan Stok';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="stock-report">

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="stock-form">    
                        
                        <?= Html::beginForm() ?>
                                                    
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-6">
                                        <?= Html::submitButton('<i class="fa fa-file-pdf-o"></i> PDF', ['name' => 'print', 'value' => 'pdf', 'class' => 'btn btn-primary']) ?>
                                        &nbsp;&nbsp;
                                        <?= Html::submitButton('<i class="fa fa-file-excel-o"></i> Excel', ['name' => 'print', 'value' => 'excel', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </div>
                            </div>
                        
                        <?= Html::endForm() ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->
</div>