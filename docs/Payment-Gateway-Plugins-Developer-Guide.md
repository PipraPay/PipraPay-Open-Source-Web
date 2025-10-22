# PipraPay Payment Gateway Plugins — Developer Guide

> **Version:** v1.0   
> **Author:** [Fattain Naime](https://facebook.com/fattain.naime)  
> **Summary:** A well‑detailed payment gateway developer documentation for PipraPay.

---

## Contents
1. [Architecture Overview](#1-architecture-overview)
2. [Database & Settings](#2-database--settings)
3. [Plugin Anatomy & Files](#3-plugin-anatomy--files)
4. [Runtime Contracts](#4-runtime-contracts)
5. [Core Helper APIs](#5-core-helper-apis)
6. [Payment Flows (Manual vs API)](#6-payment-flows)
7. [Step‑by‑Step: Build a New Gateway](#7-step-by-step-build-a-new-gateway)
8. [Boilerplates & Templates](#8-boilerplates--templates)
9. [Hooks & Webhooks](#9-hooks--webhooks)
10. [Security & UX](#10-security--ux)
11. [Go‑Live Checklist](#11-go-live-checklist)
12. [Examples](#12-examples)
13. [FAQ](#13-faq)

---

## 1) Architecture Overview

Every payment method is an isolated plugin located under:

```text
pp-content/plugins/payment-gateway/<gateway-slug>
```

> **How PipraPay loads plugins**  
> Active plugins are recorded in the `plugins` table. At runtime, PipraPay autoloads each gateway’s PHP class file and calls your
> `_<slug_underscore>_admin_page()` and `_<slug_underscore>_checkout_page($payment_id)` functions when needed.

---

## 2) Database & Settings

Gateways are registered in `__PREFIX__plugins` with core columns:

- `plugin_name` — display name  
- `plugin_slug` — unique slug (e.g., `stripe-checkout`)  
- `plugin_dir` — usually `payment-gateway`  
- `plugin_array` — JSON of saved settings (credentials, fees, limits, mode)  
- `status` — `active` / `inactive`  

> The Admin UI writes settings into `plugin_array`. Use `pp_get_plugin_setting($slug)` to read them at checkout time.

---

## 3) Plugin Anatomy & Files

```
<slug>/
├─ meta.json
├─ <slug>-class.php
├─ views/
│  ├─ admin-ui.php          # Admin: configure gateway
│  └─ checkout-ui.php       # Checkout: pay flow UI + action
├─ assets/
│  └─ icon.png              # 128×128 recommended
└─ readme.txt               # Description / changelog
```
You can use CLI Tool for Genarat plugin files. Use the command in you root dir of PipraPay. add a new empty file `pipra-cli.allow` for allow the cli operation.
```php pipra-cli.php make:plugin:gateway my-gateway
```

### `meta.json`

```json
{
  "type": "payment-gateway",
  "slug": "your-gateway",
  "name": "Your Gateway",
  "mrdr": "payment-gateway"
}
```

### `<slug>-class.php`

```php
<?php
$plugin_meta = [
  'Plugin Name' => 'Your Gateway',
  'Version'     => '1.0.0',
  'Author'      => 'Your Name'
];

function your_gateway_admin_page() {
  $viewFile = __DIR__ . '/views/admin-ui.php';
  if (file_exists($viewFile)) { include $viewFile; }
  else { echo "<div class='alert'>Admin UI not found.</div>"; }
}

function your_gateway_checkout_page($payment_id) {
  $viewFile = __DIR__ . '/views/checkout-ui.php';
  if (file_exists($viewFile)) { include $viewFile; }
  else { echo "<div class='alert'>Checkout UI not found.</div>"; }
}
```

### `views/admin-ui.php`

```php
<?php
$plugin_slug = 'your-gateway';
$plugin_info = pp_get_plugin_info($plugin_slug);
$settings    = pp_get_plugin_setting($plugin_slug);
?>

<form id="gatewaySettings" method="post" action="">
  <input type="hidden" name="action" value="plugin_update-submit">
  <input type="hidden" name="plugin_slug" value="your-gateway">
  <!-- Display name, limits, fees, credentials, status -->
</form>
```

### `views/checkout-ui.php`

```php
<?php
$transaction = pp_get_transation($payment_id);
$setting     = pp_get_settings();
$plugin_slug = 'your-gateway';
$settings    = pp_get_plugin_setting($plugin_slug);

// Amount math (observed convention across gateways)
$base = convertToDefault($transaction['response'][0]['transaction_amount'],
                         $transaction['response'][0]['transaction_currency'],
                         $settings['currency']);
$fee  = safeNumber($settings['fixed_charge']) + ($base * (safeNumber($settings['percent_charge']) / 100));
$payable = $base + $fee;
?>
```

---

## 4) Runtime Contracts

- Folder name == **slug** (kebab-case).  
- Main file name must be `<slug>-class.php`.  
- Admin function: `<slug_underscore>_admin_page()`  
- Checkout function: `<slug_underscore>_checkout_page($payment_id)`  
- AJAX POST target often appends `?method=<slug>` to `pp_get_paymentlink($payment_id)`.

> **Tip:** Keep `slug` stable. PipraPay uses it to find settings, routes, and function bindings.

---

## 5) Core Helper APIs

Frequently used helpers provided by PipraPay core (observed in real plugins):

- `pp_get_settings()` — global site/app settings (branding, currency, etc.)  
- `pp_get_plugin_info($slug)`  
- `pp_get_plugin_setting($slug)`  
- `pp_get_transation($payment_id)`  
- `pp_get_faq()`, `pp_get_support_links()`  
- `pp_get_paymentlink($payment_id)`  
- `pp_set_transaction_byslip($payment_id, $slug, $display_name, $_FILES['payment_slip'], $status)`  
- Utilities: `convertToDefault()`, `safeNumber()`

---

## 6) Payment Flows

### A) Manual / Slip Upload

```php
<?php
if (isset($_POST['your-gateway'])) {
  $ok = pp_set_transaction_byslip(
    $payment_id, 'your-gateway', 'Your Gateway Name',
    $_FILES['payment_slip'] ?? null, 'pending'
  );
  echo json_encode([
    "status"  => $ok ? "true" : "false",
    "message" => $ok ? "Initialize Payment Slip" : "Failed to upload payment slip"
  ]);
  exit();
}
```
Use for bank transfers or providers without instant API capture.

### B) Redirect / API (e.g., bKash)

```php
<?php
// (Example) Token then create payment and redirect
$base_url = ($settings['bkash_mode'] === 'live')
  ? 'https://tokenized.pay.bka.sh' : 'https://tokenized.sandbox.bka.sh';

$client = new \GuzzleHttp\Client();
$resp = $client->request('POST', $base_url.'/v1.2.0-beta/tokenized/checkout/token/grant', [
  'body' => json_encode(['app_key' => $app_key, 'app_secret' => $app_secret]),
  'headers' => [
    'accept' => 'application/json',
    'content-type' => 'application/json',
    'password' => $password,
    'username' => $username,
  ],
]);
$id_token = json_decode($resp->getBody())->id_token;
// then create payment, redirect to PSP URL
```

---

## 7) Step‑by‑Step: Build a New Gateway

1. Create folder `pp-content/plugins/payment-gateway/your-gateway`  
2. Add `meta.json` and `readme.txt`  
3. Implement `your-gateway-class.php` (admin/checkout loaders)  
4. Build `views/admin-ui.php` (credentials, fees, limits, status)  
5. Build `views/checkout-ui.php` (manual slip OR API/redirect)  
6. (Optional) add `assets/` and `functions.php`  
7. Enable plugin in Admin & test sandbox → live

---

## 8) Boilerplates & Templates

### Folder Layout

```
your-gateway/
├─ meta.json
├─ your-gateway-class.php
├─ views/
│  ├─ admin-ui.php
│  └─ checkout-ui.php
├─ assets/
│  └─ icon.png
└─ readme.txt
```

### Admin Form (AJAX)

```html
<script>
document.querySelector('#gatewaySettings')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.currentTarget;
  const resp = await fetch(form.action || location.href, {
    method: 'POST', body: new FormData(form)
  });
  const data = await resp.json().catch(() => ({}));
  alert(data.message || 'Saved');
});
</script>
```

---

## 9) Hooks & Webhooks

Lightweight action system similar to WordPress:

```php
// Register a callback
add_action('pp_transaction_ipn', 'your_gateway_handle_ipn');

function your_gateway_handle_ipn($transaction_id) {
  // verify provider webhook, update transaction state, notify customer, etc.
}
```

Common core hooks:

- `pp_transaction_ipn` — payment notification received  
- `pp_invoice_ipn` — invoice status updated

---

## 10) Security & UX

- Never echo secrets; mask inputs and store in settings only  
- Validate/escape output (e.g., `htmlspecialchars(...)`)  
- Use HTTPS; verify PSP signatures and webhook authenticity  
- Keep amounts on server as source of truth  
- Offer sandbox/live mode toggle where supported  
- Clear customer messaging and error handling  
- Log key events (avoid storing PCI data)

---

## 11) Go‑Live Checklist

- [x] Slug = folder name (kebab‑case) & file names match  
- [x] `meta.json` complete  
- [x] Admin & checkout functions exported  
- [x] Admin form saves & rehydrates values  
- [x] Amount + fee computation correct  
- [x] Manual flow: slip upload → status `pending`  
- [x] API flow: sandbox tested, credentials validated  
- [x] Icon present; readme has changelog

---

## 12) Examples

### Stripe Checkout
- `stripe-checkout-class.php` with `stripe_checkout_admin_page()` & `stripe_checkout_checkout_page()`  
- Admin: secret key + webhook secret  
- Checkout: Stripe.js or hosted Checkout Session

### PayPal Checkout
- `paypal-checkout-class.php` with `paypal_checkout_admin_page()` & `paypal_checkout_checkout_page()`  
- Admin: client ID + secret  
- Checkout: PayPal SDK smart buttons

> Also see local “manual/slip” gateways and wallet APIs (e.g., bKash) for real-life patterns.

---

## 13) FAQ

### What file names are mandatory?
Folder = *slug*, main PHP = `<slug>-class.php`, `meta.json`, and both view files.

### Where are settings stored?
In `__PREFIX__plugins.plugin_array` as JSON. Use `pp_get_plugin_setting($slug)` to read.

### How do I compute total payable amount?
```php
$base = convertToDefault(amount, fromCurrency, $settings['currency']);
$fee  = fixed_charge + ($base * percent_charge / 100);
$payable = $base + $fee;
```

---

© PipraPay — Payment Gateway Developer Documentation
