<?php
use restotech\standard\backend\components\Tools;
?>

<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 300px; border:1px solid">PENDAPATAN</th>
                    <th style="width: 200px"></th>                                       
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="line">Total Penjualan</td>
                    <td class="line number"><?= Tools::convertToCurrency($penjualan, ($print == 'pdf')) ?></td>                                                                                            
                </tr>
                <tr>
                    <td class="line">Total Cash In</td>
                    <td class="line number"><?= Tools::convertToCurrency($cashIn, ($print == 'pdf')) ?></td>                                                                                            
                </tr>
                <tr>
                    <th class="number">Total</th>
                    <th class="number"><?= Tools::convertToCurrency($penjualan + $cashIn, ($print == 'pdf')) ?></th>                                                                                            
                </tr>                
            </tbody>
        </table>
        
        <br>
        
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 300px; border:1px solid">PENGELUARAN</th>
                    <th style="width: 200px"></th>                                       
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="line">Total Pembelian (PO)</td>
                    <td class="line number"><?= Tools::convertToCurrency($pembelianPO, ($print == 'pdf')) ?></td>                                                                                            
                </tr>
                <tr>
                    <td class="line">Total Pembelian Langsung</td>
                    <td class="line number"><?= Tools::convertToCurrency($pembelian, ($print == 'pdf')) ?></td>                                                                                            
                </tr>
                <tr>
                    <td class="line">Total Cash Out</td>
                    <td class="line number"><?= Tools::convertToCurrency($cashOut, ($print == 'pdf')) ?></td>                                                                                            
                </tr>
                <tr>
                    <th class="number">Total</th>
                    <th class="number"><?= Tools::convertToCurrency($pembelian + $cashOut, ($print == 'pdf')) ?></th>                                                                                            
                </tr>                
            </tbody>
        </table>
        
        <br><br>
        
        <table class="table">
            <thead>
                <tr style="border:1px solid">
                    <th style="width: 300px">GRAND TOTAL</th>
                    <th class="number" style="width: 200px"><?= Tools::convertToCurrency(($penjualan + $cashIn) - ($pembelian + $cashOut), ($print == 'pdf')) ?></th>                                       
                </tr>
            </thead>            
        </table>
    </div>
</div>        