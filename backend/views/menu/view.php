<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use restotech\standard\backend\components\ModalDialog;
use restotech\standard\backend\components\DynamicTable;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Menu */

$dynamicTableMenuRecipe = new DynamicTable([
    'model' => $modelMenuRecipe,
    'tableFields' => [
        'item_id',
        'item.nama_item',
        'itemSku.nama_sku',
        'jumlah',
    ],
    'dataProvider' => $dataProviderMenuRecipe,
    'title' => 'Resep Menu',
    'columnClass' => 'col-sm-8 col-sm-offset-2'
]);

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="menu-view">

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">
                        <?= Html::a('<i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;' . 'Edit',
                            ['update', 'id' => $model->id],
                            [
                                'class' => 'btn btn-primary',
                                'style' => 'color:white'
                            ]) ?>

                        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;' . 'Delete',
                            ['delete', 'id' => $model->id],
                            [
                                'id' => 'delete',
                                'class' => 'btn btn-danger',
                                'style' => 'color:white',
                                'model-id' => $model->id,
                                'model-name' => $model->nama_menu,
                            ]) ?>

                        <?= Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;' . 'Cancel',
                            ['index'],
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
                        'menuCategory.nama_category',
                        'menuSatuan.nama_satuan',
                        'keterangan:ntext',
                        [
                            'attribute' => 'not_active',
                            'format' => 'raw',
                            'value' => Html::checkbox('not_active[]', $model->not_active, ['value' => $model->id, 'disabled' => 'disabled']),
                        ],
                        'harga_pokok:currency',
                        'harga_jual:currency',
                        [
                            'attribute' => 'image',
                            'format' => 'raw',
                            'value' => Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/menu/', 'image', 200, 200), ['class'=>'img-thumbnail file-preview-image']),
                        ],
                    ],
                ]) ?>

            </div>
        </div>
    </div>

    <?= $dynamicTableMenuRecipe->tableData() ?>

</div>

<?php

$modalDialog = new ModalDialog([
    'clickedComponent' => 'a#delete',
    'modelAttributeId' => 'model-id',
    'modelAttributeName' => 'model-name',
]);

$modalDialog->theScript();

echo $modalDialog->renderDialog();

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = Yii::$app->params['checkbox-radio-script']()
        . '$(".iCheck-helper").parent().removeClass("disabled");';

$this->registerJs($jscript); ?>