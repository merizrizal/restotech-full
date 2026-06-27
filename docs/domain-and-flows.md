# Domain and Flows

## Domain overview

The project implements a restaurant/POS module with connected backend inventory and purchasing functions. Indonesian labels in views/controllers indicate these major domains:

- `Ruangan` / `Meja` - room and table management.
- POS table sessions - open tables, join/split/transfer orders, payment correction.
- `Booking` - future/open table reservations.
- Menu queue - kitchen/order queue completion and send status.
- Purchase ordering and supplier receiving.
- Stock inflow/outflow/transfer/conversion/correction.
- Supplier delivery invoicing, payable reporting, and payable payment.
- Sale invoice refund/correction and finance activity reporting.
- Voucher and employee-limit payment support.
- Cash in/out transaction recording.

## Frontend POS flows

### Room and table browsing

Evidence: `frontend/controllers/HomeController.php` and `frontend/controllers/DataController.php`.

- `home/room` returns active, non-deleted table categories.
- `home/table?id=<category>` loads a room with tables, open sessions, joins, and active joined table information.
- `home/room-layout` renders room layout data.
- `data/table-layout?id=<category>` returns tables for a category as JSON.
- `data/info-table` returns details for a selected table with open session/join metadata.

### Open table/session selection

Evidence: `HomeController::actionViewSession()`.

If a requested table has exactly one open session or no specific session was requested, the controller delegates to inherited `open-table`. If multiple sessions exist, it renders `_view_session` to let the user select.

### Split order/session

Evidence: `ActionController::actionSplit()`.

Flow:

1. Create a new `MtableSession` for target table/customer.
2. Move selected `MtableOrder` records to the new session.
3. Recalculate moved subtotal with item-level discounts.
4. Add moved total to new session.
5. Subtract moved total from original session.
6. Commit or roll back transaction.

### Transfer table

Evidence: `ActionController::actionTransferTable()`.

Updates an existing `MtableSession` to a different `mtable_id` and returns a URL to reopen the moved session.

### Transfer menu/items

Evidence: `ActionController::actionTransferMenu()`.

Flow:

1. Find currently open target session for target table.
2. Move selected `MtableOrder` rows to target session.
3. Calculate subtotal/discount for moved orders.
4. Add moved total to target session.
5. Subtract moved total from source session.
6. Return route/URL to target open table.

### Join tables

Evidence: `ActionController::actionJoinTable()`.

Flow:

1. Find source session and open target table session.
2. Create or reuse an `MtableJoin` aggregate.
3. Move selected orders to active joined session.
4. Create/update `MtableSessionJoin` records for both source and target sessions.
5. Mark sessions as joined.
6. Target active session carries the combined bill; source session totals/discount/tax/service are zeroed.

### Kitchen/order queue

Evidence: `HomeController::actionMenuQueue()`, `HomeController::actionMenuQueueFinished()`, `ActionController::actionQueueMenu()`, `ActionController::actionQueueFinish()`, `ActionController::actionQueueSend()`.

- Queued order items are `MtableOrderQueue` records.
- `queue-menu` creates queue records from posted order IDs/menu IDs/quantities/notes.
- Queue list shows unfinished and unsent rows.
- Finished queue list shows finished but unsent rows.
- `queue-finish` marks `is_finish = 1`.
- `queue-send` marks `is_send = 1`.

### Booking

Evidence: `HomeController::actionBooking()`, `HomeController::actionCreateBooking()`, `ActionController::actionCreateBooking()`, `ActionController::actionBookingOpen()`.

Flow:

1. `home/booking` lists open `MtableBooking` records.
2. `home/create-booking` renders a new booking form.
3. `action/create-booking` creates a booking ID from `Settings::getTransNumber('no_booking')`.
4. `action/booking-open` checks target table has no open session, closes the booking, and opens a new `MtableSession` using booking customer/table and current tax/service settings.

### Invoice correction

Evidence: `HomeController::actionCorrectionInvoiceSubmit()` and `ActionController::actionPaymentCorrection()`.

Flow:

1. User enters invoice ID and existing invoice is loaded with session/orders/payments.
2. Existing invoice data is copied to correction tables: `SaleInvoiceCorrection`, `SaleInvoiceTrxCorrection`, `SaleInvoicePaymentCorrection`.
3. Existing invoice lines/payments are deleted.
4. For old invoice lines, stock is added back from menu recipes and `StockMovement` type `Inflow` is recorded.
5. Invoice is rewritten with posted totals/discount/tax/service/payment data.
6. New invoice lines deduct recipe stock and record `Outflow-Menu` movements.
7. Employee-limit and voucher payments update `Employee::sisa` or mark `Voucher::not_active`.

## Backend purchase/inventory flows

### Purchase order

Evidence: `backend/controllers/PurchaseOrderController.php`.

- PO IDs are generated via `Settings::getTransNumber('no_po')`.
- Header totals `jumlah_item` and `jumlah_harga` are computed from `PurchaseOrderTrx` rows.
- Detail row total is `jumlah_order * harga_satuan`.
- `get-po` returns open PO lines by supplier for receiving.
- `print` renders an mPDF purchase order.

### Supplier delivery / receiving

Evidence: `backend/controllers/SupplierDeliveryController.php`.

- Supplier delivery IDs use `Settings::getTransNumber('no_sd')`.
- Each `SupplierDeliveryTrx` stores received qty and value.
- Linked `PurchaseOrderTrx::jumlah_terima` is incremented and may be marked closed.
- Stock is increased with `Stock::setStock(...)`.
- A `StockMovement::setInflow('Inflow-PO', ...)` is recorded.
- Reports support PDF and Excel output for receiving between dates.

### Purchase return

Evidence: `backend/controllers/ReturPurchaseController.php`.

- Return IDs use `Settings::getTransNumber('no_rp')`.
- Each `ReturPurchaseTrx` reduces stock with negative `Stock::setStock(...)`.
- A `StockMovement::setOutflow('Outflow-RP', ...)` is recorded during create.
- Note: update path calls `StockMovement::setOutflow('Inflow-PO', ...)`, which appears semantically inconsistent.

### Supplier delivery invoice / payable

Evidence: `SupplierDeliveryInvoiceController.php` and `SupplierDeliveryInvoicePaymentController.php`.

- Invoice IDs use `Settings::getTransNumber('no_sdinv')`.
- Invoice rows are represented by `SupplierDeliveryInvoiceTrx`.
- Payable report filters invoices where payment method type is `Purchase` and method is `Account-Payable`.
- Payment creation increments `SupplierDeliveryInvoice::jumlah_bayar` and stores `SupplierDeliveryInvoicePayment`.
- Payable payment reports support PDF/Excel.

### Stock movement

Evidence: `StockController.php`, `StockMovementController.php`, `StockKoreksiController.php`.

- Manual stock actions: `Inflow`, `Outflow`, `Transfer`.
- Conversion creates an `Inflow-Convert` movement for destination SKU/storage and an `Outflow-Convert` movement for source SKU/storage.
- Stock movement lists filter by type and optional date.
- Stock correction (`StockKoreksi`) records adjustment proposals. Bulk verification can approve rows, update stock quantity, and create `Koreksi` movement with inflow or outflow direction based on adjustment sign.

## Finance/reporting flow

Evidence: `PageController::actionReportAktifitasKeuangan()`.

The finance activity report combines:

- calculated sales from sale invoices and non-free invoice lines,
- cash-in transactions from `transaction_cash` joined to `transaction_account.account_type = Cash-In`,
- supplier-delivery invoice payments,
- direct purchases from `direct_purchase`,
- cash-out transactions from `transaction_cash` joined to `transaction_account.account_type = Cash-Out`.

Date filtering converts UTC timestamps to `+07:00` for Jakarta-local reporting.
