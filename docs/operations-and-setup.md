# Operations and Setup

## Installation assumptions

This package is a Yii2 extension. `composer.json` provides PSR-4 autoloading:

```json
"restotech\\full\\": ""
```

Install through Composer in a host Yii2 application, then mount the module in the host configuration. The root module registers `backend` and `frontend` submodules automatically, but does not register `api`.

Example conceptual host config:

```php
'bootstrap' => ['restotechFull'],
'modules' => [
    'restotechFull' => [
        'class' => 'restotech\\full\\Module',
    ],
    // If API is needed, mount explicitly based on host route convention:
    'restotechFullApi' => [
        'class' => 'restotech\\full\\api\\ApiModule',
    ],
]
```

Exact module IDs and route prefixes are host-application decisions. Controllers also reference `Yii::$app->params['posModule']['full']` for frontend route generation, so the host app must provide that parameter.

## Composer dependencies

Declared requirements:

- `synctech/yii2-synctbase: dev-master`
- `kartik-v/yii2-widget-datepicker: @dev`
- `kartik-v/yii2-widget-fileinput: @dev`
- `kartik-v/yii2-widget-timepicker: *`
- `kartik-v/yii2-widget-select2: @dev`
- `kartik-v/yii2-grid: @dev`
- `kartik-v/yii2-money: dev-master`
- `kartik-v/yii2-mpdf: @dev`
- `kartik-v/yii2-field-range: *`

Runtime source code also depends heavily on `restotech/standard` namespaces, although that package is not directly listed in this repository's `composer.json`.

## Web access

Each app directory contains `.htaccess` with:

```apache
deny from all
```

This suggests source directories should not be directly served. Requests should enter through the host Yii front controller.

## Configuration required from host app

The host app must provide, directly or through dependencies:

- Yii2 application object and DB component.
- User identity/session support for backend/frontend controllers.
- `restotech/standard` controllers/models/components.
- Aliases used by views/controllers:
  - `@restotech/full`
  - `@restotech/standard`
  - `@vendor`
- `Yii::$app->params['posModule']['full']` used to build POS routes.
- `Yii::$app->params['errMysql']` mapping used when delete operations catch MySQL exceptions.
- Application timezone/formatter behavior; code switches between `Asia/Jakarta` and `UTC` in transactional flows.
- Database schema for all external models and tables.

## Testing

Only `common/codeception.yml` exists locally. It declares:

- namespace: `common\tests`
- Yii2 config file: `config/test-local.php`
- paths under `common/tests`

No test files or `config/test-local.php` are present in this repository snapshot, so tests likely live in the host app or are incomplete here.

## Validation commands used for this documentation

Repository exploration used bounded commands such as:

```bash
find api backend common frontend -maxdepth 5 -type f | sort
grep -RIn "^class \|function action\|namespace \|use restotech\|use synctech\|extends" api backend frontend common --include='*.php'
for f in api/controllers/frontend/*.php backend/controllers/*.php frontend/controllers/*.php; do grep -n "^class \|function action" "$f"; done
```

After doc generation, recommended local validation is:

```bash
find docs -maxdepth 1 -type f | sort
git diff -- docs
```

## Runtime reports/output

PDF reports are generated with `kartik\mpdf\Pdf` and generally download files directly. Excel exports are emitted by setting XLS response headers, echoing rendered HTML content, and calling `exit`.

Report CSS is loaded from:

```php
Yii::getAlias('@restotech/standard/backend/media/css/report.css')
```

## Operational cautions

- This extension cannot run in isolation because models/controllers/components are external.
- Several transactional flows update stock and movement rows together; failed `save()` calls roll back within the controller transaction, but external static helper behavior must be confirmed.
- API controllers currently set some user fields to `null` with comments about token-based identity; production API deployment needs authentication/identity handling.
- Date filtering for reports uses raw SQL string interpolation from POST values. Validate/sanitize date inputs in host routing/forms.
