# PipraPay Module Plugin Developer Guide

* **Version**: v1.0
* **Author**: [Fattain Naime](https://iamnaime.info.bd)
* **Summary**: PipraPay exposes a lightweight plugin system that allows you to extend the core gateway with module plugins. Modules run alongside payment gateways and can listen to lifecycle hooks, schedule background work, expose admin UI, and react to transaction or invoice changes. This document explains how the system works and how to build, package, and maintain a new module plugin.

## Files and Directory Layout
Module plugins live under `pp-content/plugins/modules/<plugin-slug>/`. A typical module contains:

```
pp-content/plugins/modules/<plugin-slug>/
+-- meta.json               # Required manifest used when importing from ZIP
+-- <plugin-slug>-class.php # Required entry point that populates $plugin_meta and loads helpers
+-- functions.php           # Optional; registers hooks with add_action()
+-- views/                  # Optional admin UI or partial templates
+-- assets/icon.png         # 256x256 icon surfaced in the admin panel
+-- other PHP/JS/CSS files  # Any module-specific code (IPN endpoints, utilities, etc.)
```
You can use CLI Tool for Genarat plugin files. Use the command in you root dir of PipraPay. add a new empty file `pipra-cli.allow` for allow the cli operation.
```php pipra-cli.php make:plugin:gateway my-gateway
```

### `meta.json`
The importer expects the following keys:

```json
{
  "type": "plugins",
  "slug": "my-module",
  "name": "My Module",
  "mrdr": "modules"
}
```

* `type` must be `plugins`.
* `slug` becomes the directory name and database identifier.
* `mrdr` is the plugin directory root (`modules` for module plugins).

### `<slug>-class.php`
Each plugin must expose `$plugin_meta` (used for the admin listing) and may load additional files:

```php
if (!defined('pp_allowed_access')) {
    die('Direct access not allowed');
}

$plugin_meta = [
    'Plugin Name' => 'My Module',
    'Description' => 'What your plugin does.',
    'Version' => '1.0.0',
    'Author' => 'Acme Devs'
];

$funcFile = __DIR__ . '/functions.php';
if (file_exists($funcFile)) {
    require_once $funcFile;
}

function my_module_admin_page() {
    $viewFile = __DIR__ . '/views/admin-ui.php';
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        echo "<div class='alert alert-warning'>Admin UI not found.</div>";
    }
}
```

`parsePluginHeader()` reads `$plugin_meta`, so keep the keys consistent with the sample above.

### `functions.php`
Place all hook registrations and module business logic inside this file. Always guard against direct access with the `pp_allowed_access` constant. Use `add_action()` to subscribe to hooks (details below). Split complex logic into smaller functions or dedicated classes when helpful.

## How PipraPay Loads Module Plugins
1. **Import (optional).** Uploading a ZIP through *Admin -> Add New* uses the importer defined in `pp-include/pp-model.php` to unpack into `pp-content/plugins/<type>/<mrdr>/<slug>/` and validate `meta.json`.
2. **Activation.** The admin panel toggles plugin records via the `pp_plugins` table. When you activate a module, PipraPay stores the slug, marks it `active`, and remembers the directory.
3. **Execution.** Whenever `pp_trigger_hook()` runs, it finds all active plugins, includes their `functions.php`, and then executes callbacks that were registered with `add_action()` (or matching `<hook>_<plugin_slug>` functions). This happens at runtime for each hook invocation, so make sure hook registration is idempotent.
4. **Admin view loading.** When you open a module inside the admin navigation the system loads `pp-include/pp-resource/plugin-loader.php`, requires `<slug>-class.php`, and calls `<slug>_admin_page()`.

## Hook System
The hook API lives in `pp-include/pp-controller.php`:

* `add_action($hook, $callback)` - registers a callback for a hook.
* `do_action($hook, ...$args)` - runs all callbacks for a hook.
* `pp_trigger_hook($hook, ...$args)` - loads every active plugin, requires its `functions.php`, then delegates to `do_action()`. After firing registered callbacks, PipraPay also looks for a function named `<hook>_<plugin_slug>` and calls it as a fallback.

Hooks have no priority or argument count enforcement, so write callbacks that can accept optional parameters (`function foo($arg = null)` or `function foo(...$args)`).

## Hook Reference
| Hook | Trigger location | Arguments | Typical use |
|------|------------------|-----------|-------------|
| `pp_cron` | `index.php?cron` | none | Schedule tasks, auto-update checks, queue processing. |
| `pp_admin_initialize` | Early in `admin/index.php` | none | Inject admin guards (2FA), load extra assets, enforce restrictions. |
| `pp_transaction_ipn` | Multiple transaction updates (manual approval, cron auto-verify, slip upload) | `$transactionId` | Send notifications, push webhooks, reconcile ledgers. |
| `pp_invoice_ipn` | Invoice status changes (IPN handler, bulk updates, manual edits) | `$invoiceId` | Notify customers, send receipts, sync accounting. |
| `pp_invoice_initialize` | After invoice theme render (`invoice/index.php`) | none | Inject custom JavaScript, tracking pixels, or preload data when a public invoice is opened. |
| `pp_payment_initialize` | After checkout theme render (`payment/index.php`) | none | Adjust checkout UI, preload payment metadata, fire analytics. |
| `pp_payment_link_initialize` | When a payment link page is shown (`payment-link/index.php`) | none | Inject custom scripts or banner content for payment links. |

> Tip: `pp_transaction_ipn` and `pp_invoice_ipn` are invoked frequently; keep handlers lightweight and fail-safe so they never block the core workflow.

## Working with Plugin Settings
Use the helper functions in `pp-include/pp-controller.php`:

* `pp_get_plugin_setting($slug)` - returns the JSON blob stored for your plugin.
* `pp_set_plugin_setting($slug, $array)` - persists new settings to the `plugins` table.
* `pp_get_plugin_info($slug)` - metadata such as display name and directory.

The admin panel expects AJAX forms to submit with `action=plugin_update-submit` and `plugin_slug=<slug>`. After activation you can render a settings form in `views/admin-ui.php` similar to the SMTP module. When posted, the core handler stores all remaining fields as JSON.

## Accessing Platform Data
Useful helpers available to modules include:

* `pp_get_site_url()` - base URL with the current host.
* `pp_get_settings()` - global settings row (currency, API keys, etc.).
* `pp_get_transation($id)` - fetches a payment transaction by PipraPay ID (or accepts a custom SQL fragment).
* `pp_get_invoice($invoiceId)` / `pp_get_invoice_items($invoiceId)` - fetch invoice header and line items.
* `pp_get_payment_link($linkId)` and related item fetchers.
* `pp_verify_transaction()` - cross-checks a transaction against SMS gateway data.
* `pp_set_transaction_byid()` / `pp_set_transaction_byslip()` - mark transactions as completed, refunded, or slip-verified (these fire `pp_transaction_ipn`).

Inspect `pp-include/pp-controller.php` for many more helpers (customer lookup, file utilities, etc.).

## Building a New Module Plugin
1. **Choose a slug.** Stick to lowercase letters, numbers, and dashes (`my-new-module`).
2. **Create the directory.** `pp-content/plugins/modules/my-new-module/`.
3. **Add `meta.json`.** Follow the schema shown above.
4. **Create `my-new-module-class.php`.** Populate `$plugin_meta`, require `functions.php`, and define `my_new_module_admin_page()` if you need admin UI.
5. **Create `functions.php`.** Guard with the `pp_allowed_access` constant, register hooks via `add_action()`, and implement callback logic. Retrieve data using the helper functions listed earlier.
6. **(Optional) Add `views/admin-ui.php`.** Include a form that posts with `action=plugin_update-submit` and saves settings through AJAX.
7. **Add an icon.** Place a PNG at `assets/icon.png` so the module is recognizable in the admin menu.
8. **Activate the plugin.** In the admin panel go to *More -> Modules*, click your plugin, and press *Activate*. The first activation creates a database record if one does not already exist.
9. **Test hooks.** Trigger the relevant flows (new transaction, invoice payment, cron job) to ensure your callbacks run.

## Example: Minimal IPN Handler
```php
<?php
if (!defined('pp_allowed_access')) {
    die('Direct access not allowed');
}

add_action('pp_transaction_ipn', 'my_new_module_handle_transaction');

function my_new_module_handle_transaction($transactionId)
{
    $transaction = pp_get_transation($transactionId);
    if (!$transaction['status']) {
        return;
    }

    $data = $transaction['response'][0];
    $payload = [
        'pp_id' => $data['pp_id'],
        'amount' => $data['transaction_amount'],
        'currency' => $data['transaction_currency'],
        'status' => $data['transaction_status'],
    ];

    $ch = curl_init('https://example.com/webhook');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
```

## Best Practices
* **Stay idempotent.** Hooks like `pp_transaction_ipn` can fire multiple times. Track delivery state in your own storage if repeating work is problematic.
* **Avoid long blocking calls.** Offload slow work to queues or keep timeouts low so you never stall payment processing.
* **Validate input.** Use `escape_string()`, `filter_var()`, or built-in sanitizers before storing data supplied by users.
* **Respect `pp_allowed_access`.** Never execute plugin code when the core constant is missing.
* **Graceful failures.** Catch exceptions, return early when settings are incomplete, and write to PHP error logs instead of echoing raw errors.
* **Secure endpoints.** If you expose additional public PHP files (for example, an IPN endpoint), validate tokens or signatures before executing logic.
* **Use settings APIs.** Store configuration via `pp_set_plugin_setting()` instead of writing your own files so backups and migrations capture everything.

## Debugging Tips
* Enable PHP error logging (`pp-config.php`) and tail the log while developing.
* Temporarily add `error_log()` calls inside your callbacks to confirm hook order and payloads.
* Use the built-in bulk actions (Send IPN) inside the admin *Transactions* grid to manually re-fire `pp_transaction_ipn` during testing.

## Packaging & Distribution
* Ship modules as ZIP files that contain the plugin folder at the root. The importer understands both `my-module/` (folder) and flat archives.
* Ensure all files are UTF-8 (ASCII-safe) and avoid BOM markers.
* Include a short `readme.txt` if you want to surface installation notes inside the admin UI.

Following these guidelines keeps your module plugins consistent with the bundled examples (SMTP mailer, webhook dispatch, Telegram notifications, etc.) and ensures they interoperate cleanly with core PipraPay updates.




---

© [PipraPay](https://piprapay.com) — Module Plugin Developer Documentation