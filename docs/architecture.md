# Architecture

## Package identity

Evidence: `composer.json` declares:

- package: `synctech/restotech-full`
- description: `Restotech Full, Property of synctech.id`
- type: `yii2-extension`
- PSR-4 autoload: `restotech\full\` mapped to repository root.

The root `README.md` describes it as the core of Restotech Full, encapsulated in a Yii2 extension.

## Module structure

### Root module

`Module.php` defines namespace `restotech\full` and extends `yii\base\Module`. In `init()` it registers two submodules:

- `backend` => `restotech\full\backend\BackendModule`
- `frontend` => `restotech\full\frontend\FrontendModule`

Notable: `api/ApiModule.php` exists, but `Module.php` does not register it. Host applications may mount it separately.

### Submodules

- `backend/BackendModule.php` - empty Yii module subclass for backend routes.
- `frontend/FrontendModule.php` - empty Yii module subclass for frontend routes.
- `api/ApiModule.php` - empty Yii module subclass for API routes.

Each submodule mostly delegates lifecycle behavior to Yii and parent modules/controllers.

## Directory layout

```text
api/
  ApiModule.php
  controllers/frontend/{ActionController,DataController,HomeController}.php
backend/
  BackendModule.php
  controllers/*.php
  views/**
common/
  codeception.yml
frontend/
  FrontendModule.php
  controllers/{ActionController,DataController,HomeController}.php
  views/**
Module.php
composer.json
```

All `api`, `backend`, `frontend`, and `common` directories include `.htaccess` with `deny from all`, implying direct web access is intended to be blocked and requests should go through the host Yii entry point.

## Dependency architecture

The extension is not self-contained. It extends and uses external Restotech Standard classes:

- controllers: `restotech\standard\backend\controllers\*`, `restotech\standard\frontend\controllers\*`, `restotech\standard\api\controllers\frontend\*`
- models: `restotech\standard\backend\models\*`
- search models: `restotech\standard\backend\models\search\*`
- components: `GridView`, `ModalDialog`, `NotificationDialog`, `DynamicTable`, `DynamicFormField`, `Tools`

Declared composer requirements include Yii/Kartik widgets and `synctech/yii2-synctbase`, but this repository's `composer.json` does not directly require `restotech/standard`, even though source code depends on it.

## Controller override pattern

Most backend controllers follow the same pattern:

1. Extend a matching `restotech\standard\backend\controllers\...Controller` class.
2. Override `beforeAction()` to set the view path to `@restotech/full/backend/views/<controller-id>`.
3. Merge inherited access rules via `$this->getAccess()` with local `VerbFilter` rules.
4. Implement CRUD and domain-specific actions using external ActiveRecord models.

Frontend controllers follow a similar pattern but set view path to `@restotech/full/frontend/views/<controller-id>` where HTML partials are used.

API controllers mirror frontend controller logic but:

- use namespace `restotech\full\api\controllers\frontend`,
- extend `restotech\standard\api\controllers\frontend\*`,
- merge no access rules (`[]`) in `behaviors()`,
- generally return arrays instead of rendering partials,
- avoid setting `Yii::$app->response->format = Response::FORMAT_JSON`, relying on API parent/application behavior.

## UI architecture

Backend views are organized by controller route and mostly implement Yii CRUD screens, reports, and partials. Frontend views are POS-oriented AJAX partials for:

- room/table layout,
- active/opened table sessions,
- menu kitchen/queue lists,
- booking creation/opening,
- invoice correction/reprint flows.

Kartik widgets are used for dates, timepicker, Select2, GridView, Money, mPDF reports, and field ranges.

## Persistence architecture

No database schema or migrations exist in this repository. Persistence is inferred from external ActiveRecord models and table names referenced by queries, including:

- `mtable`, `mtable_category`, `mtable_session`, `mtable_order_queue`
- `purchase_order`, `purchase_order_trx`
- `supplier_delivery`, `supplier_delivery_trx`
- `supplier_delivery_invoice`, `supplier_delivery_invoice_payment`
- `stock`, `stock_movement`, `stock_koreksi`
- `sale_invoice`, `sale_invoice_trx`, `sale_invoice_payment`
- `transaction_cash`, `transaction_account`
- `voucher`, `supplier`, `payment_method`

## Reporting architecture

Several controllers generate PDF or Excel outputs:

- `PurchaseOrderController::actionPrint()`
- `SupplierDeliveryController::actionPrint()`
- `SupplierDeliveryController::actionReportPenerimaan()`
- `SupplierDeliveryInvoiceController::actionReportHutang()`
- `SupplierDeliveryInvoicePaymentController::actionReportPembayaranHutang()`
- `StockController::actionReportStock()`
- `PageController::actionReportAktifitasKeuangan()`

PDF generation uses `kartik\mpdf\Pdf`; Excel output is emitted by setting response headers and echoing rendered HTML.
