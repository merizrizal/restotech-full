<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

$strH1 = '';

switch ($exception->statusCode) {
    case 400:
        $strH1 = 'Parameter data tidak lengkap.';
        break;
    case 403:
        $strH1 = 'Maaf Anda tidak berkewenangan untuk mengakses data ini.';
        break;
    case 404:
        $strH1 = 'Data yang Anda cari tidak ada.';
        break;
    case 405:
        $strH1 = 'Terdapat kesalahan dalam proses penginputan data.';
        break;
    case 406:
        $strH1 = $exception->getMessage();
        break;
}
?>

<section class="content-header">
    <h1><?= $name ?></h1>
</section>

<section class="content">

    <div class="site-error">

        <div class="alert alert-danger">
            <h3><?= $strH1 ?></h3>   
        </div>

        <p>
            <?= Html::a('Back.', Yii::$app->request->referrer); ?>
        </p>

    </div>
</section>
