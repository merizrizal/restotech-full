# Appendix: Model, Search, and Component Index

This index lists external classes referenced by this repository. The implementations are not present locally.

## Backend models referenced

From `use restotech\standard\backend\models\...` references:

- `Employee`
- `Item`
- `ItemSku`
- `Menu`
- `MenuRecipe`
- `Mtable`
- `MtableBooking`
- `MtableCategory`
- `MtableJoin`
- `MtableOrder`
- `MtableOrderQueue`
- `MtableSession`
- `MtableSessionJoin`
- `PaymentMethod`
- `PurchaseOrder`
- `PurchaseOrderTrx`
- `ReturPurchase`
- `ReturPurchaseTrx`
- `SaleInvoice`
- `SaleInvoiceCorrection`
- `SaleInvoicePayment`
- `SaleInvoicePaymentCorrection`
- `SaleInvoiceRetur`
- `SaleInvoiceTrx`
- `SaleInvoiceTrxCorrection`
- `Settings`
- `Stock`
- `StockKoreksi`
- `StockMovement`
- `Storage`
- `StorageRack`
- `Supplier`
- `SupplierDelivery`
- `SupplierDeliveryInvoice`
- `SupplierDeliveryInvoicePayment`
- `SupplierDeliveryInvoiceTrx`
- `SupplierDeliveryTrx`
- `TransactionAccount`
- `TransactionCash`
- `Voucher`

## Search models referenced

- `MtableCategorySearch`
- `MtableSearch`
- `PaymentMethodSearch`
- `PurchaseOrderSearch`
- `ReturPurchaseSearch`
- `SaleInvoiceSearch`
- `StockKoreksiSearch`
- `StockMovementSearch`
- `StockSearch`
- `StorageRackSearch`
- `StorageSearch`
- `SupplierDeliveryInvoiceSearch`
- `SupplierDeliverySearch`
- `SupplierSearch`
- `TransactionAccountSearch`
- `TransactionCashSearch`
- `VoucherSearch`

## Components/widgets referenced

From views and controllers:

- `restotech\standard\backend\components\DynamicFormField`
- `restotech\standard\backend\components\DynamicTable`
- `restotech\standard\backend\components\GridView`
- `restotech\standard\backend\components\ModalDialog`
- `restotech\standard\backend\components\NotificationDialog`
- `restotech\standard\backend\components\Tools`
- `kartik\mpdf\Pdf`
- Kartik Yii2 widgets declared in composer: DatePicker, FileInput, TimePicker, Select2, Grid, Money, mPDF, FieldRange.

## Static methods and important contracts

### `Settings`

Observed static calls:

- `Settings::getTransNumber('id_mtable', 'ym', $model->nama_meja)`
- `Settings::getTransNumber('id_payment_method', 'ym', $model->nama_payment)`
- `Settings::getTransNumber('id_gudang', 'ym', $model->nama_storage)`
- `Settings::getTransNumber('id_supplier', 'ym', $model->nama)`
- `Settings::getTransNumber('id_voucher', 'ym')`
- `Settings::getTransNumber('no_po')`
- `Settings::getTransNumber('no_sd')`
- `Settings::getTransNumber('no_rp')`
- `Settings::getTransNumber('no_sdinv')`
- `Settings::getTransNumber('no_booking')`
- `Settings::getSettingsByName(['tax_amount', 'service_charge_amount'])`

### `Stock`

Observed static call:

```php
Stock::setStock($itemId, $itemSkuId, $storageId, $storageRackId, $delta)
```

The sign of `$delta` drives inventory direction:

- positive = increase stock,
- negative = decrease stock.

### `StockMovement`

Observed static helpers:

```php
StockMovement::setInflow($type, $itemId, $skuId, $storageId, $rackId, $qty, $date, $reference)
StockMovement::setOutflow($type, $itemId, $skuId, $storageId, $rackId, $qty, $date, $reference)
```

Manual controller code also directly instantiates `StockMovement` for conversion, correction, and sale invoice correction.

### `Tools`

Observed methods:

- `Tools::loadIsIncludeScp()`
- `Tools::hitungServiceChargePajak($subtotal, $serviceChargeRate, $taxRate)`

Used in finance activity reporting.

## Inferred model attributes by view directory

These are fields observed in backend/frontend form and detail views.

| View directory | Observed attributes |
|---|---|
| `backend/views/mtable` | `id`, `mtable_category_id`, `nama_meja`, `kapasitas`, `status`, `keterangan`, `not_ppn`, `not_service_charge`, `not_active`, `image`, `shape`, audit fields |
| `backend/views/mtable-category` | `id`, `nama_category`, `color`, `image`, `keterangan`, `not_active`, audit fields |
| `backend/views/payment-method` | `id`, `nama_payment`, `type`, `method`, `keterangan`, `not_active`, audit fields |
| `backend/views/purchase-order` | `id`, `date`, `kd_supplier`, `jumlah_item`, `jumlah_harga`, detail fields `item_id`, `item_sku_id`, `harga_satuan`, `jumlah_order` |
| `backend/views/retur-purchase` | `id`, `date`, `kd_supplier`, `jumlah_item`, `jumlah_harga`, detail fields `supplier_delivery_id`, `supplier_delivery_trx_id`, `item_id`, `item_sku_id`, `jumlah_item`, `harga_satuan`, `storage_id`, `storage_rack_id` |
| `backend/views/sale-invoice` | return fields `sale_invoice_trx_id`, `menu_id`, `nama_menu`, `jumlah`, `harga`, `discount_type`, `discount`, `keterangan` |
| `backend/views/stock` | `id`, `item_id`, `item_sku_id`, `jumlah_stok`, `storage_id`, `storage_rack_id`, movement fields `tanggal`, `storage_from`, `storage_to`, `jumlah`, `reference`, `keterangan` |
| `backend/views/stock-koreksi` | `id`, `date_action`, `user_action`, `item_id`, `item_sku_id`, `storage_id`, `storage_rack_id`, `jumlah_awal`, `jumlah`, `jumlah_adjustment`, `action` |
| `backend/views/stock-movement` | `id`, `tanggal`, `type`, `item_id`, `item_sku_id`, `storage_from`, `storage_rack_from`, `storage_to`, `storage_rack_to`, `jumlah`, `reference`, `keterangan` |
| `backend/views/storage` | `id`, `nama_storage`, `keterangan` |
| `backend/views/storage-rack` | `id`, `storage_id`, `nama_rak`, `keterangan` |
| `backend/views/supplier` | `kd_supplier`, `nama`, `alamat`, `telp`, `fax`, `keterangan`, `kontak1..4`, `kontak*_telp`, audit fields |
| `backend/views/supplier-delivery` | `id`, `date`, `kd_supplier`, `jumlah_item`, `jumlah_harga`, detail fields `purchase_order_id`, `purchase_order_trx_id`, `item_id`, `item_sku_id`, `jumlah_order`, `jumlah_terima`, `harga_satuan`, `storage_id`, `storage_rack_id`, `is_closed` |
| `backend/views/supplier-delivery-invoice` | `id`, `date`, `supplier_delivery_id`, `payment_method`, `jumlah_harga`, `jumlah_bayar`, audit fields |
| `backend/views/supplier-delivery-invoice-payment` | `supplier_delivery_invoice_id`, `date`, `jumlah_bayar`, audit fields |
| `backend/views/transaction-account` | `id`, `nama_account`, `account_type`, audit fields |
| `backend/views/transaction-cash` | `id`, `account_id`, `date`, `jumlah`, `reference_id`, `keterangan`, audit fields |
| `backend/views/voucher` | `id`, `voucher_type`, `jumlah_voucher`, `start_date`, `end_date`, `not_active`, `keterangan`, audit fields |
| `frontend/views/home` | booking/table fields `mtable_category_id`, `mtable_id`, `nama_pelanggan`, `date`, `time`, `keterangan` |
