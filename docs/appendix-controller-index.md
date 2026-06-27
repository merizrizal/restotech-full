# Appendix: Controller Index

Exhaustive controller/action index discovered with `grep` across `api/controllers`, `frontend/controllers`, and `backend/controllers`.

## Root/module classes

| File | Class |
|---|---|
| `Module.php` | `restotech\full\Module` |
| `api/ApiModule.php` | `restotech\full\api\ApiModule` |
| `backend/BackendModule.php` | `restotech\full\backend\BackendModule` |
| `frontend/FrontendModule.php` | `restotech\full\frontend\FrontendModule` |

## API controllers

### `api/controllers/frontend/ActionController.php`

Extends `restotech\standard\api\controllers\frontend\ActionController`.

Actions:

- `actionCatatan()`
- `actionSplit()`
- `actionQueueMenu()`
- `actionCashdrawer()`
- `actionTransferTable()`
- `actionTransferMenu()`
- `actionJoinTable()`
- `actionPaymentCorrection()`
- `actionQueueFinish($id)`
- `actionQueueSend($id)`
- `actionCreateBooking()`
- `actionBookingOpen($id, $tid)`

### `api/controllers/frontend/DataController.php`

Extends `restotech\standard\api\controllers\frontend\DataController`.

Actions:

- `actionTableLayout($id)`
- `actionInfoTable()`
- `actionTableCategory($isOpened = false)`
- `actionTable($id, $isOpened = false)`
- `actionLimitKaryawan()`
- `actionVoucher()`
- `actionGetMtable($id)`

### `api/controllers/frontend/HomeController.php`

Extends `restotech\standard\api\controllers\frontend\HomeController`.

Actions:

- `actionRoom()`
- `actionTable($id)`
- `actionRoomLayout()`
- `actionViewSession($id, $cid, $sessId = null)`
- `actionOpenedTable()`
- `actionMenuQueue()`
- `actionMenuQueueFinished()`
- `actionCorrectionInvoice()`
- `actionCorrectionInvoiceSubmit()`
- `actionBooking()`
- `actionCreateBooking()`

## Frontend controllers

### `frontend/controllers/ActionController.php`

Extends `restotech\standard\frontend\controllers\ActionController`.

Actions:

- `actionCatatan()`
- `actionSplit()`
- `actionQueueMenu()`
- `actionCashdrawer()`
- `actionTransferTable()`
- `actionTransferMenu()`
- `actionJoinTable()`
- `actionPaymentCorrection()`
- `actionQueueFinish($id)`
- `actionQueueSend($id)`
- `actionCreateBooking()`
- `actionBookingOpen($id, $tid)`

### `frontend/controllers/DataController.php`

Extends `restotech\standard\frontend\controllers\DataController`.

Actions:

- `actionTableLayout($id)`
- `actionInfoTable()`
- `actionTableCategory($isOpened = false)`
- `actionTable($id, $isOpened = false)`
- `actionLimitKaryawan()`
- `actionVoucher()`
- `actionGetMtable($id)`

### `frontend/controllers/HomeController.php`

Extends `restotech\standard\frontend\controllers\HomeController`.

Actions:

- `actionRoom()`
- `actionTable($id)`
- `actionRoomLayout()`
- `actionViewSession($id, $cid, $sessId = null)`
- `actionOpenedTable()`
- `actionMenuQueue()`
- `actionMenuQueueFinished()`
- `actionCorrectionInvoice()`
- `actionCorrectionInvoiceSubmit()`
- `actionBooking()`
- `actionCreateBooking()`

## Backend controllers

### `backend/controllers/MtableCategoryController.php`

Extends `restotech\standard\backend\controllers\MtableCategoryController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`.

### `backend/controllers/MtableController.php`

Extends `restotech\standard\backend\controllers\MtableController`.

Actions: `index($cid)`, `view($id)`, `create($cid)`, `update($id)`, `delete($id)`, `table-layout($catid = null)`.

### `backend/controllers/PageController.php`

Extends `restotech\standard\backend\controllers\PageController`.

Actions: `report-aktifitas-keuangan`.

### `backend/controllers/PaymentMethodController.php`

Extends `restotech\standard\backend\controllers\PaymentMethodController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`.

### `backend/controllers/PurchaseOrderController.php`

Extends `restotech\standard\backend\controllers\PurchaseOrderController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`, `get-po($id)`, `print($id)`.

### `backend/controllers/ReturPurchaseController.php`

Extends `restotech\standard\backend\controllers\ReturPurchaseController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`, `get-rp-by-id($id)`.

### `backend/controllers/SaleInvoiceController.php`

Extends `restotech\standard\backend\controllers\SaleInvoiceController`.

Actions: `refund`, `view($id)`.

### `backend/controllers/StockController.php`

Extends `restotech\standard\backend\controllers\StockController`.

Actions: `index`, `input-stock($type)`, `stock-convert`, `get-sku-item($id)`, `get-storage($id)`, `get-storage-rack($sid, $isid, $iid)`, `get-sku-item-descent($iid, $isid)`, `report-stock`.

Private helpers: `stockInflow()`, `stockOutflow()`, `stockTransfer()`.

### `backend/controllers/StockKoreksiController.php`

Extends `restotech\standard\backend\controllers\StockKoreksiController`.

Actions: `index`, `view($id)`, `create($id)`, `update($id)`, `delete($id)`.

Private helper: `opnameVerify($postParams)`.

### `backend/controllers/StockMovementController.php`

Extends `restotech\standard\backend\controllers\StockMovementController`.

Actions: `index($type, $date = null)`, `view($id)`, `update($id)`, `convert($date = null)`.

### `backend/controllers/StorageController.php`

Extends `restotech\standard\backend\controllers\StorageController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`.

### `backend/controllers/StorageRackController.php`

Extends `restotech\standard\backend\controllers\StorageRackController`.

Actions: `index($sid)`, `view($id)`, `create($sid)`, `update($id)`, `delete($id)`.

### `backend/controllers/SupplierController.php`

Extends `restotech\standard\backend\controllers\SupplierController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`.

### `backend/controllers/SupplierDeliveryController.php`

Extends `restotech\standard\backend\controllers\SupplierDeliveryController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`, `get-sd($id)`, `get-sd-by-id($id)`, `print($id)`, `report-penerimaan`.

### `backend/controllers/SupplierDeliveryInvoiceController.php`

Extends `restotech\standard\backend\controllers\SupplierDeliveryInvoiceController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`, `report-hutang`.

### `backend/controllers/SupplierDeliveryInvoicePaymentController.php`

Extends `restotech\standard\backend\controllers\SupplierDeliveryInvoicePaymentController`.

Actions: `create($id)`, `update($id)`, `delete($id)`, `report-pembayaran-hutang`.

### `backend/controllers/TransactionAccountController.php`

Extends `restotech\standard\backend\controllers\TransactionAccountController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`.

### `backend/controllers/TransactionCashController.php`

Extends `restotech\standard\backend\controllers\TransactionAccountController` in this repository, while implementing `TransactionCash` CRUD.

Actions: `index($type)`, `view($id)`, `create($type)`, `update($id)`, `delete($id)`.

### `backend/controllers/VoucherController.php`

Extends `restotech\standard\backend\controllers\VoucherController`.

Actions: `index`, `view($id)`, `create`, `update($id)`, `delete($id)`.
