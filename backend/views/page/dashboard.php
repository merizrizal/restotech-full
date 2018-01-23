<?php

use restotech\standard\backend\components\Tools;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

Tools::loadIsIncludeScp();

$dataPenjualan = [];
foreach ($modelSaleInvoice as $dataSaleInvoice) {

    $dateSplit = explode('-', $dataSaleInvoice['date']);
    $key = $dateSplit[0] . '-' . $dateSplit[1] . '-' . '01';

    $dataPenjualan[$key]['bulan'] = $key;

    if (empty($dataPenjualan[$key]['total']))
        $dataPenjualan[$key]['total'] = 0;

    $discBill = empty($dataSaleInvoice['discount']) ? 0 : $dataSaleInvoice['discount'];
    $discBillType = $dataSaleInvoice['discount_type'];
    $discBillValue = 0;

    if ($discBillType == 'Percent') {
        $discBillValue = $discBill * 0.01 * $dataSaleInvoice['jumlah_harga'];
    } else if ($discBillType == 'Value') {
        $discBillValue = $discBill;
    }

    $jumlahSubtotal = 0;

    foreach ($dataSaleInvoice['saleInvoiceTrxes'] as $dataSaleInvoiceTrx) {

        $subtotal = $dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['jumlah'];
        $discount = 0;

        if ($dataSaleInvoiceTrx['discount_type'] == 'Percent') {

            $discount = ($dataSaleInvoiceTrx['harga_satuan'] * $dataSaleInvoiceTrx['discount'] / 100) * $dataSaleInvoiceTrx['jumlah'];
            $subtotal = $subtotal - $discount;
        } else if ($dataSaleInvoiceTrx['discount_type'] == 'Value') {

            $discount = $dataSaleInvoiceTrx['discount'] * $dataSaleInvoiceTrx['jumlah'];
            $subtotal = $subtotal - $discount;
        }

        $jumlahSubtotal += $subtotal;
    }

    $scp = Tools::hitungServiceChargePajak($jumlahSubtotal, $dataSaleInvoice['service_charge'], $dataSaleInvoice['pajak']);
    $serviceCharge = $scp['serviceCharge'];
    $pajak = $scp['pajak'];
    $grandTotal = $jumlahSubtotal + $serviceCharge + $pajak - $discBillValue;

    $dataPenjualan[$key]['total'] += $grandTotal;
} ?>

<div class="row">

    <section class="col-lg-6">
        <div class="chart-container" id="chart-penjualan" style="position: relative; height: 400px;"></div>
    </section>

    <section class="col-lg-6">
        <div class="chart-container" id="chart-menu" style="position: relative; height: 400px;"></div>
    </section>
</div><!-- /.row (main row) -->

<?php

$penjualanBulan = '';
$penjualanValue = '';
foreach ($dataPenjualan as $value) {
    $penjualanValue .= $value['total'] . ',';
    $penjualanBulan .= '"' . date("M", strtotime($value['bulan'])) . '",';
}

$topMenuItem = '';

foreach ($topMenu as $key => $value) {

    foreach ($value as $key2 => $value2) {

        $topMenuItem .= '
            {
                name: "' . $value2['nama_menu']  . '",
                y: ' . $value2['jumlah']  . '
            },
        ';

        if ($key2 == 5) {
            break;
        }
    }
} ?>

<?php
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/highchart/highcharts.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/highchart/themes/dark-unica.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/highchart/plugins/grouped-categories.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    var chart_penjualan = new Highcharts.chart("chart-penjualan", {
        chart: {
            type: "line"
        },
        title: {
            text: "Penjualan Tahun ' . Yii::$app->formatter->asDate(time(), 'yyyy') . '"
        },
        legend: {
            enabled: false
        },
        xAxis: {
            categories: [
                ' . trim($penjualanBulan, ',') . '
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: "Nilai Penjualan (Rupiah)"
            }
        },
        series: [
            {
                name: "Nilai",
                data: [' . trim($penjualanValue, ',') . ']
            }
        ],
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        colors: ["#F56954"],
        credits: {
            enabled: false
        }
    });

    var chart_menu = new Highcharts.chart("chart-menu", {
        chart: {
            type: "column",
        },
        title: {
            text: "Top Menu ' . Yii::$app->formatter->asDate(time(), 'MMMM yyyy') . '"
        },
        legend: {
            enabled: false
        },
        xAxis: {
            type: "category"
        },
        yAxis: {
            min: 0,
            title: {
                text: "Jumlah Penjualan"
            }
        },
        series: [{
            name: "Jumlah",
            colorByPoint: true,
            data: [' . trim($topMenuItem) . ']
        }],
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        credits: {
            enabled: false
        }
    });
';

$this->registerJs($jscript); ?>