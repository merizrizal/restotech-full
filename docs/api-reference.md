# API and Route Reference

Routes below use Yii controller/action naming. Exact public URLs depend on how the host application mounts `restotech\full\Module`, `BackendModule`, `FrontendModule`, and `ApiModule`.

## Conventions

- Backend controllers generally require inherited access rules from `getAccess()` and only force `delete` actions to POST via `VerbFilter` unless otherwise noted.
- Frontend controllers require inherited access rules and mark POS AJAX actions as POST-only.
- API frontend controllers mirror frontend actions but merge no inherited access rules in this repository (`[]`) and generally return arrays.
- Many frontend/backend actions assume AJAX partial rendering through layouts under `@restotech/standard/.../layouts/ajax`.

## Frontend HTML/AJAX routes

### `frontend/home`

All listed actions are POST-only.

| Action | Params | Returns/behavior |
|---|---:|---|
| `room` | - | Renders active room/table categories via `_room`. |
| `table` | `id` | Renders tables for category with open sessions and joined-table metadata via `_table`. |
| `room-layout` | - | Renders room layout categories via `_room_layout`. |
| `view-session` | `id`, `cid`, optional `sessId` | Delegates to inherited `open-table` when one/no session; otherwise renders session chooser. |
| `opened-table` | POST `nama_tamu` optional | Lists open tables filtered by guest name. |
| `menu-queue` | - | Lists unfinished, unsent active order queue rows. |
| `menu-queue-finished` | - | Lists finished, unsent active order queue rows. |
| `correction-invoice` | - | Renders invoice input partial from standard frontend. |
| `correction-invoice-submit` | POST `id` | Loads invoice and opens related table/session for correction. |
| `booking` | - | Lists open bookings. |
| `create-booking` | - | Renders booking creation partial. |

### `frontend/data`

| Action | Verb | Params | Returns/behavior |
|---|---|---:|---|
| `table-layout` | POST | `id` | JSON list of tables for a category. |
| `info-table` | POST | POST `id` | HTML partial `_info_table` for selected table/session. |
| `table-category` | POST | optional `isOpened` | HTML partial `_table_category`. |
| `table` | POST | `id`, optional `isOpened` | HTML partial `_table`. |
| `limit-karyawan` | not listed in VerbFilter | POST `kode_karyawan`, `jml_limit` | JSON success/message after checking employee limit. |
| `voucher` | not listed in VerbFilter | POST `kode_voucher`, `tagihan` | JSON success/message and calculated voucher amount. |
| `get-mtable` | not listed in VerbFilter | `id` | JSON Select2-style table list for a category. |

### `frontend/action`

All listed actions are POST-only.

| Action | Params/body | Behavior |
|---|---|---|
| `catatan` | POST `order_id`, `catatan` | Updates note on an `MtableOrder`. |
| `split` | POST target session/table/customer fields and `order_id[]` | Creates new session and moves selected order rows. |
| `queue-menu` | POST `order_id[]` structs | Creates `MtableOrderQueue` rows. |
| `cashdrawer` | POST | Currently returns success without side effects. |
| `transfer-table` | POST `sess_id`, `mtable_id` | Moves session to another table and returns open-table URL. |
| `transfer-menu` | POST `sess_id`, `mtable_id`, `order_id[]` | Moves selected orders to target table's active session. |
| `join-table` | POST `sess_id`, `mtable_id`, optional `order_id[]` | Joins source session/table into target joined table aggregate. |
| `payment-correction` | POST invoice/session/order/payment totals | Archives old invoice rows/payments, restores stock, rewrites invoice, deducts stock again. |
| `queue-finish` | `id` | Sets queue row `is_finish = 1`. |
| `queue-send` | `id` | Sets queue row `is_send = 1`. |
| `create-booking` | POST `MtableBooking` | Creates booking with generated booking number. |
| `booking-open` | `id`, `tid` | Closes booking and opens table session if target table is free. |

## API frontend routes

Files under `api/controllers/frontend` define `api/frontend/home`, `api/frontend/data`, and `api/frontend/action` equivalents. They use the same action names as frontend controllers, with key differences observed from diffs:

- Namespace is `restotech\full\api\controllers\frontend`.
- Parent classes are `restotech\standard\api\controllers\frontend\*`.
- `behaviors()` merges `[]` instead of `$this->getAccess()`.
- Rendered frontend partials are replaced by returned arrays for many actions.
- Response format is not explicitly set in actions that return arrays.
- User identity fields in API actions are currently set to `null` with comments indicating token-based identity should be added later.
- URL return values such as `open_table` are route arrays instead of generated URL strings.

### API action inventory

- `api/frontend/home`: `room`, `table`, `room-layout`, `view-session`, `opened-table`, `menu-queue`, `menu-queue-finished`, `correction-invoice`, `correction-invoice-submit`, `booking`, `create-booking`.
- `api/frontend/data`: `table-layout`, `info-table`, `table-category`, `table`, `limit-karyawan`, `voucher`, `get-mtable`.
- `api/frontend/action`: `catatan`, `split`, `queue-menu`, `cashdrawer`, `transfer-table`, `transfer-menu`, `join-table`, `payment-correction`, `queue-finish`, `queue-send`, `create-booking`, `booking-open`.

## Backend route inventory

### Master data

| Controller | Main actions | Notes |
|---|---|---|
| `mtable-category` | `index`, `view`, `create`, `update`, `delete` | Room/category CRUD; soft-delete fallback on delete failure. |
| `mtable` | `index($cid)`, `view`, `create($cid)`, `update`, `delete`, `table-layout($catid)` | Table CRUD and drag/layout updates. |
| `payment-method` | `index`, `view`, `create`, `update`, `delete` | Payment method CRUD. |
| `storage` | `index`, `view`, `create`, `update`, `delete` | Warehouse/storage CRUD. |
| `storage-rack` | `index($sid)`, `view`, `create($sid)`, `update`, `delete` | Rack CRUD under storage. |
| `supplier` | `index`, `view`, `create`, `update`, `delete` | Supplier CRUD; soft-delete fallback. |
| `transaction-account` | `index`, `view`, `create`, `update`, `delete` | Cash transaction account CRUD. |
| `voucher` | `index`, `view`, `create`, `update`, `delete` | Voucher CRUD and generated voucher IDs. |

### Purchasing and payables

| Controller | Actions | Notes |
|---|---|---|
| `purchase-order` | `index`, `view`, `create`, `update`, `delete`, `get-po($id)`, `print($id)` | PO CRUD, open PO-line lookup by supplier, PDF print. |
| `supplier-delivery` | `index`, `view`, `create`, `update`, `delete`, `get-sd($id)`, `get-sd-by-id($id)`, `print($id)`, `report-penerimaan` | Receiving CRUD, stock inflow, reports. |
| `retur-purchase` | `index`, `view`, `create`, `update`, `delete`, `get-rp-by-id($id)` | Purchase return CRUD and stock outflow. |
| `supplier-delivery-invoice` | `index`, `view`, `create`, `update`, `delete`, `report-hutang` | Supplier delivery invoice and payable report. |
| `supplier-delivery-invoice-payment` | `create($id)`, `update`, `delete`, `report-pembayaran-hutang` | Payable payment entry and payment report. |

### Stock

| Controller | Actions | Notes |
|---|---|---|
| `stock` | `index`, `input-stock($type)`, `stock-convert`, `get-sku-item($id)`, `get-storage($id)`, `get-storage-rack($sid,$isid,$iid)`, `get-sku-item-descent($iid,$isid)`, `report-stock` | Stock on hand, manual movement, conversion, lookup JSON, report. |
| `stock-movement` | `index($type,$date)`, `view`, `update`, `convert($date)` | Movement logs filtered by type/date. |
| `stock-koreksi` | `index`, `view`, `create($id)`, `update`, `delete` | Stock correction/opname verification. |

### Sales and finance

| Controller | Actions | Notes |
|---|---|---|
| `sale-invoice` | `refund`, `view($id)` | Invoice refund list and return entry on invoice view. |
| `transaction-cash` | `index($type)`, `view`, `create($type)`, `update`, `delete` | Cash-in/cash-out entries. |
| `page` | `report-aktifitas-keuangan` | Sales/cash/purchase finance activity report. |
