<?php
    $plugin_slug = 'binance-merchant-api';
    $plugin_info = pp_get_plugin_info($plugin_slug);
    $settings = pp_get_plugin_setting($plugin_slug);
?>

<form id="smtpSettingsForm" method="post" action="">
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-end">
        <div class="col-sm mb-2 mb-sm-0">
          <h1 class="page-header-title">Edit Gateway</h1>
        </div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="d-grid gap-3 gap-lg-5">
          <!-- Card -->
          <div class="card">
            <div class="card-header">
              <h2 class="card-title h4">Gateway Information</h2>
            </div>

            <!-- Body -->
            <div class="card-body">
                <input type="hidden" name="action" value="plugin_update-submit">
                <input type="hidden" name="plugin_slug" value="<?php echo $plugin_slug?>">
                
                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="host" class="col-sm-12 col-form-label form-label">Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($settings['name'] ?? $plugin_info['plugin_name']) ?>" readonly>
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  <div class="col-sm-6">
                    <label for="display_name" class="col-sm-12 col-form-label form-label">Display name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="display_name" id="display_name" value="<?= htmlspecialchars($settings['display_name'] ?? $plugin_info['plugin_name']) ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="min_amount" class="col-sm-12 col-form-label form-label">Min amount</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">USD</span>
                        <input type="text" class="form-control" name="min_amount" id="min_amount" value="<?= htmlspecialchars($settings['min_amount'] ?? '0') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  <div class="col-sm-6">
                    <label for="max_amount" class="col-sm-12 col-form-label form-label">Max amount</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">USD</span>
                        <input type="text" class="form-control" name="max_amount" id="max_amount" value="<?= htmlspecialchars($settings['max_amount'] ?? '0') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>
                
                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="fixed_charge" class="col-sm-12 col-form-label form-label">Fixed charge</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">USD</span>
                        <input type="text" class="form-control" name="fixed_charge" id="fixed_charge" value="<?= htmlspecialchars($settings['fixed_charge'] ?? '0') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                    
                  <div class="col-sm-6">
                    <label for="percent_charge" class="col-sm-12 col-form-label form-label">Percent charge</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">USD</span>
                        <input type="text" class="form-control" name="percent_charge" id="percent_charge" value="<?= htmlspecialchars($settings['percent_charge'] ?? '0') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  
                  <div class="col-sm-6">
                    <label for="status" class="col-sm-12 col-form-label form-label">Status</label>
                    <div class="input-group">
                      <select class="form-control" name="status" id="status">
                        <?php $status_gateway = isset($settings['status']) ? strtolower($settings['status']) : ''; ?>
                        <option value="disable" <?php echo ($status_gateway === 'disable') ? 'selected' : ''; ?>>Disable</option>
                        <option value="enable" <?php echo ($status_gateway === 'enable') ? 'selected' : ''; ?>>Enable</option>
                      </select>
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  
                  <div class="col-sm-6">
                    <label for="status" class="col-sm-12 col-form-label form-label">Category</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="category" id="category" value="International" readonly>
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  
                  <div class="col-sm-6">
                    <label for="currency" class="col-sm-12 col-form-label form-label">Currency</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="currency" id="currency" value="USD" readonly>
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>
            </div>
            <!-- End Body -->
          </div>
          
          
          <div class="card">
            <div class="card-header">
              <h2 class="card-title h4">Configuration</h2>
            </div>

            <!-- Body -->
            <div class="card-body">
                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="merchant_api_key" class="col-sm-12 col-form-label form-label">Merchant API key</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="merchant_api_key" id="merchant_api_key" value="<?= htmlspecialchars($settings['merchant_api_key'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>

                  <div class="col-sm-6">
                    <label for="merchant_secret_key" class="col-sm-12 col-form-label form-label">Merchant Secret Key</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="merchant_secret_key" id="merchant_secret_key" value="<?= htmlspecialchars($settings['merchant_secret_key'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                  
                  <div class="col-sm-6">
                    <label for="payment_currency" class="col-sm-12 col-form-label form-label">Payment Currency</label>
                    <div class="input-group">
                        <?php
                            $selectedCurrency = $settings['payment_currency'] ?? ''; // Example: fetched from DB or POST
                            
                            $currencies = [
                                'USDT'  => 'Tether (USDT)',
                                'BUSD'  => 'Binance USD (BUSD)',
                                'BTC'   => 'Bitcoin (BTC)',
                                'ETH'   => 'Ethereum (ETH)',
                                'BNB'   => 'Binance Coin (BNB)',
                                'DOGE'  => 'Dogecoin (DOGE)',
                                'FDUSD' => 'First Digital USD (FDUSD)',
                                'DAI'   => 'Dai (DAI)',
                                'TUSD'  => 'TrueUSD (TUSD)',
                                'SUI'   => 'Sui (SUI)',
                                'SHIB'  => 'Shiba Inu (SHIB)',
                                'SOL'   => 'Solana (SOL)',
                                'TRX'   => 'TRON (TRX)',
                                'LTC'   => 'Litecoin (LTC)',
                                'MATIC' => 'Polygon (MATIC)',
                                'XRP'   => 'XRP (XRP)',
                                'APE'   => 'ApeCoin (APE)',
                                'ADA'   => 'Cardano (ADA)'
                            ];
                        ?>
                        
                        <select class="form-control" name="payment_currency" id="payment_currency">
                            <?php foreach ($currencies as $code => $label): ?>
                                <option value="<?= $code ?>" <?= ($code === $selectedCurrency) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-sm-6">
                    <label for="terminal_type" class="col-sm-12 col-form-label form-label">Terminal type</label>
                    <div class="input-group">
                        <?php
                            $terminalType = strtoupper($settings['terminal_type'] ?? 'WEB');
                            $terminalOptions = [
                                'WEB' => 'WEB (desktop browser)',
                                'WAP' => 'WAP (mobile browser)',
                                'APP' => 'APP (native application)',
                                'MINI_PROGRAM' => 'MINI_PROGRAM (Binance mini program)',
                                'OTHERS' => 'OTHERS'
                            ];
                        ?>
                        <select class="form-control" name="terminal_type" id="terminal_type">
                            <?php foreach ($terminalOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($terminalType === $value) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-secondary mt-2">
                        Matches <code>env.terminalType</code> in <a href="https://developers.binance.com/docs/binance-pay/api-order-create-v3" target="_blank" rel="noopener noreferrer">Binance Pay Order Create v3</a>.
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <label for="sub_merchant_id" class="col-sm-12 col-form-label form-label">Sub-merchant ID (optional)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="sub_merchant_id" id="sub_merchant_id" value="<?= htmlspecialchars($settings['sub_merchant_id'] ?? '') ?>">
                    </div>
                    <div class="text-secondary mt-2">
                        Required only if Binance Pay classifies your account as a channel partner.
                    </div>
                  </div>
                </div>
            </div>
            <!-- End Body -->
          </div>

          <div id="ajaxResponse"></div>

          <button type="submit" class="btn btn-primary btn-primary-add" style=" max-width: 150px; ">Save Settings</button>
          <!-- End Card -->
        <div id="stickyBlockEndPoint"></div>
      </div>
    </div>
</form>


        
<script>
    $(document).ready(function() {
        $('#smtpSettingsForm').on('submit', function(e) {
            e.preventDefault();
    
            document.querySelector(".btn-primary-add").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
    
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    document.querySelector(".btn-primary-add").innerHTML = 'Save Settings';
                    
                    if(response.status) {
                        $('#ajaxResponse').addClass('alert alert-success mb-3').html(response.message);
                    } else {
                        $('#ajaxResponse').addClass('alert alert-danger mb-3').html(response.message);
                    }
                },
                error: function() {
                    $('#ajaxResponse').addClass('alert alert-danger').html('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
