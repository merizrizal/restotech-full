<?php
use yii\helpers\Html;

foreach ($data as $dataSDTrx): ?>    

    <tr>
        <td id="supplier_delivery_id"><?= $dataSDTrx['supplier_delivery_id'] ?></td>
        <td id="nama_item"><?= $dataSDTrx['item']['nama_item'] ?></td>
        <td id="nama_sku"><?= $dataSDTrx['itemSku']['nama_sku'] ?></td>
        <td id="jumlah_terima"><?= $dataSDTrx['jumlah_terima'] ?></td>
        <td id="harga_satuan"><?= Yii::$app->formatter->asCurrency($dataSDTrx['harga_satuan']) ?></td>
        <td>
            <?= Html::a('<i class="fa fa-check"></i>', null, [
                            'class' => 'btn btn-primary btn-xs',
                            'id' => 'check-sd-trx',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'left',
                            'title' => 'Pilih',
            ]) ?>
        
            <?= Html::hiddenInput('item_id', $dataSDTrx['item_id'], ['id' => 'item_id']) ?>
            <?= Html::hiddenInput('item_sku_id', $dataSDTrx['item_sku_id'], ['id' => 'item_sku_id']) ?>
            <?= Html::hiddenInput('harga_satuan', $dataSDTrx['harga_satuan'], ['id' => 'harga_satuan']) ?>
            <?= Html::hiddenInput('id', $dataSDTrx['id'], ['id' => 'id']) ?>
        </td>
    </tr>
    
<?php
endforeach; 

$jscript = '
    var maskMoney_config = {"prefix":"Rp ","suffix":"","affixesStay":true,"thousands":".","decimal":",","precision":0,"allowZero":false,"allowNegative":false};
    
    $("a[data-toggle=\"tooltip\"]").tooltip();
    
    var disableKdSupplier = function() {
    
        if ($("#table-retur-purchase").children("tbody").find("tr").length > 0) {
        
            $("#returpurchase-kd_supplier").on("select2:opening",function(e) {
                return false;
            });
            
            $("#returpurchase-kd_supplier").on("select2:unselecting",function(e) {
                return false;
            });
        } else {            
            
            $("#returpurchase-kd_supplier").off("select2:opening");
            $("#returpurchase-kd_supplier").off("select2:unselecting");
        }
    };
    
    var changeIndex = function(content, field, index, validation) {
        
        var inputClass = "";
        var inputName = "";
        var inputId = "";

        inputClass = content.find("#" + field).parent().attr("class");
        inputClass = inputClass.replace("index", index);
        
        content.find("#" + field).parent().attr("class", inputClass);
            
        inputName = content.find("#" + field).attr("name");
        inputName = inputName.replace("index", index);
        
        content.find("#" + field).attr("name", inputName);
            
        inputId = content.find("#" + field).attr("id");
        inputId = inputId.replace("index", index);
        
        content.find("#" + field).attr("id", inputId);
        
        $("#formReturPurchase").yiiActiveForm("add", {
            id: inputId,
            name: inputName,
            container: ".field-" + inputId,
            input: "#" + inputId,
            validate: function(attribute, value, messages, deferred, $form) {            
            
                $.each(validation, function(index, val) {                
                
                    if (val == "required") {

                        yii.validation.required(value, messages, {"message": "Tidak boleh kosong"});        
                    }

                    if (val == "number") {

                        yii.validation.number(value, messages, {"pattern":/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/, "message": "Harus berupa angka","skipOnEmpty":1});                                             
                    }
                });
            },
        });
        
        return content;
    };
    
    $("a#check-sd-trx").on("click", function(event) {
    
        var parent = $(this).parent().parent();
    
        var index = parseFloat($("#index").val());
        
        var content = $("#temp-RPTrx").children().children().children().clone();
        
        content.find("#returpurchasetrx-index-supplier_delivery_id").val(parent.find("#supplier_delivery_id").html());
        content = changeIndex(content, "returpurchasetrx-index-supplier_delivery_id", index);
        
        content.find("#item-index-nama_item").val(parent.find("#nama_item").html());
        content = changeIndex(content, "item-index-nama_item", index);
        
        content.find("#itemsku-index-nama_sku").val(parent.find("#nama_sku").html());
        content = changeIndex(content, "itemsku-index-nama_sku", index);
        
        content = changeIndex(content, "returpurchasetrx-index-jumlah_item", index, ["number", "required"]);
        
        content.find("#returpurchasetrx-index-harga_satuan").val(parent.find("input#harga_satuan").val());                
        content.find("#returpurchasetrx-index-harga_satuan-disp").val(parent.find("input#harga_satuan").val());

        content = changeIndex(content, "returpurchasetrx-index-harga_satuan", index);
        content = changeIndex(content, "returpurchasetrx-index-harga_satuan-disp", index);
        
        content.find("#returpurchasetrx-" + index + "-harga_satuan-disp").maskMoney(maskMoney_config);
        var val = parseFloat(content.find("#returpurchasetrx-" + index + "-harga_satuan").val());
        content.find("#returpurchasetrx-" + index + "-harga_satuan-disp").maskMoney("mask", val);
        content.find("#returpurchasetrx-" + index + "-harga_satuan-disp").on("change", function () {
            var numDecimal = content.find("#returpurchasetrx-" + index + "-harga_satuan-disp").maskMoney("unmasked")[0];
            content.find("#returpurchasetrx-" + index + "-harga_satuan").val(numDecimal);
            content.find("#returpurchasetrx-" + index + "-harga_satuan").trigger("change");
        });
        
        content = changeIndex(content, "returpurchasetrx-index-storage_id", index, ["required"]);
        
        content.find("#returpurchasetrx-" + index + "-storage_id").select2({
            theme: "krajee",
            placeholder: "Pilih",
            allowClear: true
        });                
        
        content = changeIndex(content, "returpurchasetrx-index-storage_rack_id", index);
        
        var storageRack = function(remoteData) {
            content.find("#returpurchasetrx-" + index + "-storage_rack_id").val(null);
            content.find("#returpurchasetrx-" + index + "-storage_rack_id").select2({
                theme: "krajee",
                placeholder: "Pilih",
                allowClear: true,
                data: remoteData,
            });
        };

        storageRack([]);

        content.find("#returpurchasetrx-" + index + "-storage_id").on("select2:select", function(e) {
            content.find("#returpurchasetrx-" + index + "-storage_rack_id").val(null).trigger("change");

            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage-rack/get-storage-rack']) . '?id=" + $(this).select2("data")[0].id,
                success: function(response) {
                    storageRack(response);
                }
            });
        });

        content.find("#returpurchasetrx-" + index + "-storage_id").on("select2:unselect", function(e) {
            content.find("#returpurchasetrx-" + index + "-storage_rack_id").val(null).trigger("change");
            storageRack([]);
        });        
        
        content.find("#returpurchasetrx-index-supplier_delivery_trx_id").val(parent.find("#id").val());
        content = changeIndex(content, "returpurchasetrx-index-supplier_delivery_trx_id", index);
        
        content.find("#returpurchasetrx-index-item_id").val(parent.find("#item_id").val());
        content = changeIndex(content, "returpurchasetrx-index-item_id", index);
        
        content.find("#returpurchasetrx-index-item_sku_id").val(parent.find("#item_sku_id").val());
        content = changeIndex(content, "returpurchasetrx-index-item_sku_id", index);
        
        content.find("a#aDelete").on("click", function() {
        
            $(this).parent().parent().parent().fadeOut(180, function() {                                
                
                $(this).remove();
                
                disableKdSupplier();
            });    
            
            return false;
        });
        
        $("#table-retur-purchase").children("tbody").append(content);        
        
        $("#index").val(index + 1);
        
        disableKdSupplier();
        
        return false;
    });
';

$this->registerJs($jscript); ?>

 