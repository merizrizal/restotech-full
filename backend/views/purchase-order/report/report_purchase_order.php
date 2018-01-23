<?php

use yii\helpers\Html;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\SaleInvoice */

$this->title = 'Laporan Pembelian (PO)';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="sale-invoice-report">

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="sale-invoice-form">    
                        
                        <?= Html::beginForm() ?>
                        
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="control-label">Report</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="col-lg-7">
                                            <?= Html::radioList('reportType', 'detail', 
                                                [
                                                    'detail' => 'Detail',
                                                ],
                                                [
                                                    'separator' => '<br>'
                                                ]) ?>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <label class="control-label">Tanggal</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="col-lg-8">
                                            <?= DatePicker::widget([
                                                    'name' => 'tanggalFrom',
                                                    'options' => ['id' => 'tanggalFrom'],
                                                    'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                                ]); ?>
                                            
                                            &nbsp; &nbsp; s/d &nbsp; &nbsp;
                                            
                                            <?= DatePicker::widget([
                                                    'name' => 'tanggalTo',
                                                    'options' => ['id' => 'tanggalTo'],
                                                    'pluginOptions' => Yii::$app->params['datepickerOptions'],
                                                ]); ?>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-6">
                                        <?php
                                        $icon = '<i class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;';
                                        echo Html::submitButton($icon . 'PDF', ['class' => 'btn btn-success', 'name' => 'print', 'value' => 'pdf']); 
                                        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                                        echo Html::submitButton($icon . 'Excel', ['class' => 'btn btn-primary', 'name' => 'print', 'value' => 'excel']); ?>

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

<?php 

$this->params['regJsFile'][] = function() {
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/iCheck/icheck.min.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.date.extensions.js');
    $this->registerJsFile(Yii::getAlias('@common-web') . '/js/plugins/input-mask/jquery.inputmask.extensions.js');
};

$jscript = '
    $("#tanggalFrom, #tanggalTo").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>