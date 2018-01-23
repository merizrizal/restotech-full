<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\NotificationDialog;
use restotech\standard\backend\components\DynamicFormField;
use restotech\standard\backend\models\MenuCategory;
use restotech\standard\backend\models\MenuSatuan;
use restotech\standard\backend\models\Item;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Menu */
/* @var $form yii\widgets\ActiveForm */

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) :
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif; ?>

<?php
$form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data'
            ],
            'fieldConfig' => [
                'parts' => [
                    '{inputClass}' => 'col-lg-12',
                ],
                'template' => '<div class="row">'
                                . '<div class="col-lg-3">'
                                    . '{label}'
                                . '</div>'
                                . '<div class="col-lg-6">'
                                    . '<div class="{inputClass}">'
                                        . '{input}'
                                    . '</div>'
                                . '</div>'
                                . '<div class="col-lg-3">'
                                    . '{error}'
                                . '</div>'
                            . '</div>',
            ]
    ]);

    $dynamicFormMenuRecipe = new DynamicFormField([
        'dataModel' => $modelMenuRecipe,
        'form' => $form,
        'formFields' => [
            'item_id' => [
                'type' => 'dropdown',
                'data' => ArrayHelper::map(

                        Item::find()->orderBy('nama_item')->asArray()->all(),
                        'id',
                        function($data) {
                            return $data['nama_item'] . ' (' . $data['id'] . ')';
                        }
                ),
                'affect' => [
                    'field' => 'item_sku_id',
                    'url' => Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-sku/get-sku-item']),
                ],
                'colOption' => 'style="width: 50%"',
            ],
            'item_sku_id' => [
                'type' => 'textinput-dropdown',
                'colOption' => 'style="width: 30%"',
            ],
            'jumlah' => [
                'type' => 'textinput',
                'colOption' => 'style="width: 10%"',
            ],
        ],
        'title' => 'Resep Menu',
        'columnClass' => 'col-sm-8 col-sm-offset-2'
    ]);?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="menu-form">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?php
                                    if (!$model->isNewRecord) {
                                        echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create'], ['class' => 'btn btn-success']);
                                        echo '&nbsp; &nbsp;';
                                        echo Html::a('<i class="fa fa-history"></i>&nbsp;&nbsp;&nbsp;' . 'History HPP', ['hpp-history', 'id' => $model->id], ['class' => 'btn btn-default']);
                                    } ?>
                                </div>
                            </div>
                        </div>

                        <?php
                        if (!$model->isNewRecord) {

                            echo $form->field($model, 'id', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->textInput(['maxlength' => true, 'readonly' => 'readonly']);
                        } ?>

                        <?= $form->field($model, 'nama_menu')->textInput(['maxlength' => 128]) ?>

                        <?= $form->field($model, 'menu_category_id')->dropDownList(
                                ArrayHelper::map(
                                    MenuCategory::find()->where(['not_active' => false])->andWhere(['IS NOT', 'parent_category_id', NULL])->orderBy('nama_category')->asArray()->all(),
                                    'id',
                                    function($data) {
                                        return $data['nama_category'] . ' (' . $data['id'] . ')';
                                    }
                                ),
                                [
                                    'prompt' => '',
                                ]) ?>

                        <?= $form->field($model, 'menu_satuan_id')->dropDownList(
                                ArrayHelper::map(
                                    MenuSatuan::find()->where(['!=', 'id', ''])->orderBy('nama_satuan')->asArray()->all(),
                                    'id',
                                    function($data) {
                                        return $data['nama_satuan'] . ' (' . $data['id'] . ')';
                                    }
                                ),
                                [
                                    'prompt' => '',
                                    'style' => 'width: 80%'
                                ]) ?>

                        <?= $form->field($model, 'keterangan')->textarea(['rows' => 2]) ?>

                        <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>


                        <?= $form->field($model, 'harga_pokok', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->widget(MaskMoney::className()) ?>

                        <?= $form->field($model, 'harga_jual', [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-7'
                                ],
                            ])->widget(MaskMoney::className()) ?>

                        <?= $form->field($model, 'image')->widget(FileInput::classname(), [
                                'options' => [
                                    'accept' => 'image/*'
                                ],
                                'pluginOptions' => [
                                    'initialPreview' => [
                                        Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/menu/', 'image', 200, 200), ['class'=>'file-preview-image']),
                                    ],
                                    'showRemove' => false,
                                    'showUpload' => false,
                                ]
                            ]); ?>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div><!-- /.row -->

    <?php
    echo $dynamicFormMenuRecipe->component(); ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-footer">
                    <?php
                    $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                    echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                </div>
            </div>
        </div>
    </div>

<?php
ActiveForm::end(); ?>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#menu-menu_category_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });

    $("#menu-menu_satuan_id").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>
