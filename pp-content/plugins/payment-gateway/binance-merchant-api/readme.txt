=== Binance Merchant Api ===
Contributors: piprapay  
Donate link: https://piprapay.com/donate  
Tags: Binance Merchant Api
Requires at least: 1.0.0  
Tested up to: 1.0.0  
Stable tag: 1.0.0  
License: GPL-2.0+  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

== Description ==

**Binance Merchant Api** is a payment gateway plugin for PipraPay that allows users to accept payments via Binance Pay merchant accounts. Designed for small sellers and individuals.

**Key Features:**
* Uses Binance Pay **Order Create v3** endpoint with automatic redirect to the hosted checkout.
* Verifies payments through the Binance Pay **Order Query v2** API before closing an order inside PipraPay.
* Supports Binance terminal types (WEB, WAP, APP, MINI_PROGRAM) plus optional sub-merchant IDs for channel partners.
* Pass-through metadata keeps the original PipraPay payment ID attached to every Binance Pay transaction.

== Changelog ==

= 1.1.0 =
* Switch create requests to `/binancepay/openapi/v3/order` per the latest Binance documentation.
* Query using `/binancepay/openapi/v2/order/query` and rely on `passThroughInfo` for reliable payment reconciliation.
* Added admin controls for terminal type and optional sub-merchant IDs.

= 1.0.0 =
* Initial release
