# Insights and Risks

## Key insights

1. **This is an override/extension layer, not the core model package.** Controllers and views live here, but ActiveRecord classes, base controllers, components, and schema live elsewhere under `restotech/standard`.

2. **Backend is a full restaurant operations module.** It covers rooms/tables, supplier purchasing, receiving, purchase returns, stock, payable invoices/payments, sale invoice refund/correction, vouchers, and finance reports.

3. **Frontend is POS/table-session oriented.** It handles open tables, table joins, split bills/orders, transfers, kitchen queue, bookings, and invoice correction.

4. **API controllers are mostly frontend logic adapted for array responses.** They are parallel to `frontend/controllers`, but access and identity behavior differ.

5. **Stock movement is central and side-effect-heavy.** Purchases, returns, menu invoice correction, conversion, and correction all update both stock quantity and movement history.

6. **Reports are rendered server-side and exported as PDF/Excel.** PDF uses mPDF; Excel is HTML streamed with XLS headers.

## Architectural risks

### Missing direct dependency declaration

Source code imports many `restotech\standard` classes, but `composer.json` does not declare a `restotech/standard` dependency. If not supplied transitively or by the host app, installation/autoloading will fail.

### No local schema/migrations

There are no migrations or model definitions in this repository. Any schema-sensitive change must be checked against the external standard package/database.

### API module registration uncertainty

`api/ApiModule.php` exists, but root `Module.php` only registers `backend` and `frontend`. If the host expects API routes under the root module, the module registration may be incomplete or intentionally delegated to host configuration.

### API authentication gap

API controllers merge `[]` instead of `$this->getAccess()` and set identity fields such as `user_opened`/`user_operator` to `null` with comments about token handling. This is a production security/audit risk unless parent API controllers enforce authentication elsewhere.

### Raw SQL date filters

Several report actions concatenate POST date values into SQL fragments, for example `BETWEEN " . $post['tanggal_from'] . " AND ...`. If UI validation is bypassed, this may become SQL injection risk. Prefer query parameters/bind values.

### Transaction and side-effect coupling

Flows mix model saves with static helpers such as `Stock::setStock()` and `StockMovement::setInflow()`. Transaction correctness depends on those helpers using the same DB connection and not committing independently.

### Semantic inconsistency in return update

`ReturPurchaseController::actionCreate()` records purchase returns with movement type `Outflow-RP`; `actionUpdate()` calls `StockMovement::setOutflow('Inflow-PO', ...)`, which appears likely wrong or at least misleading.

### Possible controller inheritance typo

`backend/controllers/TransactionCashController.php` extends `restotech\standard\backend\controllers\TransactionAccountController` while implementing `TransactionCash` CRUD. This may be intentional reuse, but it is suspicious and should be verified.

### Hard-coded payment method IDs

Invoice correction treats `XLIMIT` and `XVCHR` as special IDs for employee limit and voucher payments. These constants should be documented/configured in the host data seed.

### Deletion behavior varies

Some controllers hard-delete and fall back to soft delete (`is_deleted = 1`) on exception; others only hard-delete. This can create inconsistent lifecycle behavior across domain entities.

## Testing risks

- No local tests were found.
- `common/codeception.yml` references `config/test-local.php`, absent in this repository.
- Most behavior requires DB, authenticated Yii app, standard package models, and host params, so integration tests must be in a host app fixture.

## Suggested next steps

1. Inspect the companion `restotech/standard` package for model rules, relations, migrations, and base controller access behavior.
2. Confirm how the host application mounts the `api` module and defines `Yii::$app->params['posModule']['full']`.
3. Add integration tests for stock-affecting transactions: supplier delivery, return purchase, sale invoice correction, stock conversion, stock correction.
4. Replace raw SQL report date filters with parameterized conditions.
5. Resolve API identity TODOs before production mobile/API use.
6. Review suspicious movement type in `ReturPurchaseController::actionUpdate()`.
7. Decide whether `TransactionCashController` should extend a transaction-cash base controller rather than transaction-account base controller.
