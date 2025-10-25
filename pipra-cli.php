<?php

if (php_sapi_name() !== 'cli') {
    if (!headers_sent() && function_exists('header')) {
        header('HTTP/1.1 404 Not Found');
    }
    $errorPage = __DIR__ . '/error.php';
    if (is_file($errorPage)) {
        include $errorPage;
    } else {
        echo "Not Found";
    }
    exit;
}

$flagFile = __DIR__ . '/pipra-cli.allow';
if (!is_file($flagFile)) {
    $message = "pipra-cli is disabled. Create an empty pipra-cli.allow file alongside pipra-cli.php to enable it.\n";
    if (defined('STDERR')) {
        fwrite(STDERR, $message);
    } else {
        echo $message;
    }
    exit(1);
}

if ($argc < 3) {
    echo "Usage: php pipra-cli.php <command> <slug>\n";
    echo "Commands Example:\n";
    echo "  make:plugin:gateway my-gateway\n";
    echo "  make:plugin:module my-module\n";
    exit(1);
}

$command = $argv[1];
$slug = $argv[2];

if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    echo "Error: Invalid slug. The slug can only contain lowercase letters, numbers, and hyphens no spaces.\n";
    exit(1);
}

$slug_underscore = str_replace('-', '_', $slug);
$plugin_name = ucwords(str_replace('-', ' ', $slug));

if ($command === 'make:plugin:gateway') {
    make_gateway($slug, $slug_underscore, $plugin_name);
} elseif ($command === 'make:plugin:module') {
    make_module($slug, $slug_underscore, $plugin_name);
} else {
    echo "Error: Unknown command \"$command\".\n";
    echo "Available commands:\n";
    echo "  make:plugin:gateway my-gateway - For Creating a payment gateway plugin\n";
    echo "  make:plugin:module my-module  - For Creating a module plugin\n";
    exit(1);
}

function make_gateway($slug, $slug_underscore, $plugin_name) {
    $base_path = __DIR__ . '/pp-content/plugins/payment-gateway/' . $slug;

    if (file_exists($base_path)) {
        echo "Error: Plugin directory already exists at $base_path\n";
        exit(1);
    }

    mkdir($base_path, 0755, true);
    mkdir($base_path . '/views', 0755, true);
    mkdir($base_path . '/assets', 0755, true);

    // meta.json
    $meta_json_content = <<<EOT
{
  "type": "payment-gateway",
  "slug": "$slug",
  "name": "$plugin_name",
  "mrdr": "payment-gateway"
}
EOT;
    file_put_contents($base_path . '/meta.json', $meta_json_content);

    // <slug>-class.php
    $class_php_content = <<<EOT
<?php
\$plugin_meta = [
  'Plugin Name' => '$plugin_name',
  'Version'     => '1.0.0',
  'Author'      => 'Your Name'
];

function {$slug_underscore}_admin_page() {
  \$viewFile = __DIR__ . '/views/admin-ui.php';
  if (file_exists(\$viewFile)) { include \$viewFile; } 
  else { echo "<div class='alert'>It’s possible that there’s an issue with the Admin UI, or that the plugin doesn’t require it to function properly.</div>"; }
}

function {$slug_underscore}_checkout_page(\$payment_id) {
  \$viewFile = __DIR__ . '/views/checkout-ui.php';
  if (file_exists(\$viewFile)) { include \$viewFile; } 
  else { echo "<div class='alert'>It’s possible that there’s an issue with the Checkout UI.</div>"; }
}
EOT;
    file_put_contents($base_path . '/' . $slug . '-class.php', $class_php_content);

    // views/admin-ui.php
    $admin_ui_content = <<<EOT
<?php
\$plugin_slug = '$slug';
\$plugin_info = pp_get_plugin_info(\$plugin_slug);
\$settings    = pp_get_plugin_setting(\$plugin_slug);
?>

<form id="gatewaySettings" method="post" action="">
  <input type="hidden" name="action" value="plugin_update-submit">
  <input type="hidden" name="plugin_slug" value="<?php echo \$plugin_slug; ?>">
  
  <!-- Add your gateway settings here -->
  
  <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
EOT;
    file_put_contents($base_path . '/views/admin-ui.php', $admin_ui_content);

    // views/checkout-ui.php
    $checkout_ui_content = <<<EOT
<?php
\$transaction = pp_get_transation(\$payment_id);
\$setting     = pp_get_settings();
\$plugin_slug = '$slug';
\$settings    = pp_get_plugin_setting(\$plugin_slug);

// Amount calculation
\$base = convertToDefault(\$transaction['response'][0]['transaction_amount'],
                         \$transaction['response'][0]['transaction_currency'],
                         \$settings['currency']);
\$fee  = safeNumber(\$settings['fixed_charge']) + (\$base * (safeNumber(\$settings['percent_charge']) / 100));
\$payable = \$base + \$fee;
?>

<!-- Add your checkout UI here -->
EOT;
    file_put_contents($base_path . '/views/checkout-ui.php', $checkout_ui_content);

    // readme.txt
// Write plugin information and documentation to a readme.txt file
file_put_contents(
    $base_path . '/readme.txt',
    "=== $plugin_name ===\n" .                              // Plugin title
    "Contributors: (this should be the name of the plugin author)\n" . // Contributor info
    "Donate link: https://example.com/\n" .                 // Donation link
    "Tags: comments, plugin\n" .                            // Plugin tags
    "Requires at least: 1.0\n" .                            // Minimum PipraPay version
    "Tested up to: 1.0\n" .                                 // Tested up to version
    "Stable tag: 1.0\n" .                                   // Stable release version
    "License: GPLv3 or later\n" .                           // License type
    "License URI: https://www.gnu.org/licenses/gpl-3.0.html\n\n" . // License link

    // Short description
    "Here is a short description of the plugin.\n\n" .

    // Section: Description
    "== Description ==\n\n" .
    "This is the long description. No limit, and you can use Markdown.\n\n" .

    // Section: Installation
    "== Installation ==\n\n" .
    "This section describes how to install the plugin and get it working.\n\n" .
    "1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.\n" .
    "2. Activate the plugin through the 'Plugins' screen in WordPress\n" .
    "3. Use the Settings->Plugin Name screen to configure the plugin\n\n" .

    // Section: FAQ
    "== Frequently Asked Questions ==\n\n" .
    "* A question that someone might have.\n\n" .
    "  An answer to that question.\n\n" .

    // Section: Screenshots
    "== Screenshots ==\n\n" .
    "1. This is the first screenshot\n\n" .

    // Section: Changelog
    "== Changelog ==\n\n" .
    "= 1.0 =\n" .
    "* A change since the previous version.\n" .
    "* Another change.\n\n" .
    "= 0.5 =\n" .
    "* Initial release.\n\n" .

    // Section: Upgrade Notice
    "== Upgrade Notice ==\n\n" .
    "= 1.0 =\n" .
    "Upgrade notices describe the reason a user should upgrade. No limit, and you can use Markdown.\n\n"
);


    echo "Payment gateway plugin '$plugin_name' created successfully at $base_path\n";
}

function make_module($slug, $slug_underscore, $plugin_name) {
    $base_path = __DIR__ . '/pp-content/plugins/modules/' . $slug;

    if (file_exists($base_path)) {
        echo "Error: Plugin directory already exists at $base_path\n";
        exit(1);
    }

    mkdir($base_path, 0755, true);
    mkdir($base_path . '/views', 0755, true);
    mkdir($base_path . '/assets', 0755, true);

    // meta.json
    $meta_json_content = <<<EOT
{
  "type": "plugins",
  "slug": "$slug",
  "name": "$plugin_name",
  "mrdr": "modules"
}
EOT;
    file_put_contents($base_path . '/meta.json', $meta_json_content);

    // <slug>-class.php
    $class_php_content = <<<EOT
<?php
if (!defined('pp_allowed_access')) {
    die('Direct access not allowed');
}
//Todo: Update plugin meta by CLI
\$plugin_meta = [
    'Plugin Name' => '$plugin_name',
    'Description' => 'A brief description of what this module does.',
    'Version' => '1.0.0',
    'Author' => 'Your Name'
];

\$funcFile = __DIR__ . '/functions.php';
if (file_exists(\$funcFile)) {
    require_once \$funcFile;
}

function {$slug_underscore}_admin_page() {
    \$viewFile = __DIR__ . '/views/admin-ui.php';
    if (file_exists(\$viewFile)) {
        include \$viewFile;
    } else {
        echo "<div class='alert alert-warning'>Admin UI not found.</div>";
    }
}
EOT;
    file_put_contents($base_path . '/' . $slug . '-class.php', $class_php_content);

    // functions.php
    $functions_php_content = <<<EOT
<?php
if (!defined('pp_allowed_access')) {
    die('Direct access not allowed');
}

add_action('pp_transaction_ipn', '{$slug_underscore}_handle_transaction');

function {$slug_underscore}_handle_transaction(\$transactionId)
{
    // Your logic here.
    // For example, log the transaction ID to a file.
    // file_put_contents(__DIR__ . '/transaction.log', "Transaction ID: " . \$transactionId . "\n", FILE_APPEND);
}
EOT;
    file_put_contents($base_path . '/functions.php', $functions_php_content);
    
    // views/admin-ui.php
    $admin_ui_content = <<<EOT
<?php
\$plugin_slug = '$slug';
\$settings    = pp_get_plugin_setting(\$plugin_slug);
?>

<form id="moduleSettings" method="post" action="">
  <input type="hidden" name="action" value="plugin_update-submit">
  <input type="hidden" name="plugin_slug" value="<?php echo \$plugin_slug; ?>">
  
  <!-- Add your module settings here -->
  
  <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
EOT;
    file_put_contents($base_path . '/views/admin-ui.php', $admin_ui_content);

    // readme.txt
    file_put_contents($base_path . '/readme.txt', "=== $plugin_name ===\nContributors: (this should be the author name)\nDonate link: https://example.com/\nTags: plugin, piprapay\nRequires at least: 1.0.0\nTested up to: 2.0.6\nStable tag: 1.0.0\nLicense: GPLv3 or later\nLicense URI: https://www.gnu.org/licenses/gpl-3.0.html\n\nHere is a short description of the plugin.\n\n== Description ==\n\nThis is the long description.  No limit, and you can use Markdown.\n\n== Installation ==\n\nThis section describes how to install the plugin and get it working.\n\n1.  Upload the plugin files to the `/pp-content/plugins/modules/plugin-name` directory, or install the plugin through the PipraPay plugins screen directly.\n2.  Activate the plugin through the 'Plugins' screen in PipraPay\n3.  Use the Modules->Plugin Name screen to configure the plugin\n\n== Frequently Asked Questions ==\n\n* A question that someone might have.\n\n  An answer to that question.\n\n== Screenshots ==\n\n1. This is the first screenshot\n\n== Changelog ==\n\n= 1.0 =\n* A change since the previous version.\n* Another change.\n\n= 0.5 =\n* Initial release.\n\n== Upgrade Notice ==\n\n= 1.0 =\nUpgrade notices describe the reason a user should upgrade.  No limit, and you can use Markdown.\n\n");

    echo "Module plugin '$plugin_name' created successfully at $base_path\n";
}
