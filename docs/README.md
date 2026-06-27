# Restotech Full Project Notes

Generated from repository inspection of `restotech-full`.

## Document map

- [Architecture](architecture.md) - module layout, dependencies, request handling patterns.
- [Domain and flows](domain-and-flows.md) - restaurant/POS, table, purchase, inventory, and finance flows.
- [Data model](data-model.md) - inferred entities, fields, relationships, and caveats.
- [API reference](api-reference.md) - frontend/API/backend route inventory and behavior summaries.
- [Operations and setup](operations-and-setup.md) - install/runtime assumptions and available checks.
- [Insights and risks](insights-and-risks.md) - observations, missing pieces, and implementation risks.
- [Controller index](appendix-controller-index.md) - exhaustive controller/action index found in this repository.
- [Model index](appendix-model-index.md) - external model/search/component dependencies referenced by code.

## High-level summary

`restotech/full` is a Yii2 extension (`type: yii2-extension`) for a full restaurant/POS workflow. It extends the `restotech/standard` application layer and supplies module overrides for:

- frontend POS table/session UI and AJAX actions,
- API-style frontend controllers returning arrays/JSON-like payloads,
- backend CRUD screens for room/table setup, suppliers, purchase orders, stock, payment methods, vouchers, cash transactions, and reports.

The repository contains controllers and views, but no local ActiveRecord model definitions or database migrations. Most domain behavior depends on external classes under `restotech\standard\backend\models`, `restotech\standard\backend\controllers`, and `restotech\standard\backend\components`.

## Evidence base

Inspected files/directories include:

- `composer.json`
- `README.md`
- `Module.php`
- `api/ApiModule.php`, `backend/BackendModule.php`, `frontend/FrontendModule.php`
- `api/controllers/frontend/*.php`
- `frontend/controllers/*.php`
- `backend/controllers/*.php`
- `backend/views/**`, `frontend/views/**` field references
- `api/.htaccess`, `backend/.htaccess`, `frontend/.htaccess`, `common/.htaccess`
- `common/codeception.yml`
