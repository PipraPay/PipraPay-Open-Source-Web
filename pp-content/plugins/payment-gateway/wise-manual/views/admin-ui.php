<?php
    $plugin_slug = 'wise-manual';
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
                    <label for="received_by" class="col-sm-12 col-form-label form-label">Received by</label>
                    <div class="input-group">
                      <select class="form-control" name="received_by" id="received_by" onchange="received_by()">
                        <?php $received_by = isset($settings['received_by']) ? strtolower($settings['received_by']) : ''; ?>
                        <option value="wise-directly" <?php echo ($received_by === 'wise-directly') ? 'selected' : ''; ?>>Wise Directly</option>
                        <option value="to-mfs" <?php echo ($received_by === 'to-mfs') ? 'selected' : ''; ?>>To MFS</option>
                      </select>
                    </div>
                    <div class="text-secondary mt-2"> </div>
                  </div>
                </div>
                
                <span class="wise-directly">
                    <div class="row mb-4">
                      <div class="col-sm-6">
                        <label for="email_address" class="col-sm-12 col-form-label form-label">Wise Email Address</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="email_address" id="email_address" value="<?= htmlspecialchars($settings['email_address'] ?? '') ?>">
                        </div>
                        <div class="text-secondary mt-2"> </div>
                      </div>
                    </div>
                </span>
                
                <span class="to-mfs">
                    <div class="row mb-4">
                      <div class="col-sm-6">
                        <label for="email_address" class="col-sm-12 col-form-label form-label">Wise Email Address</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="email_address" id="email_address" value="<?= htmlspecialchars($settings['email_address'] ?? '') ?>">
                        </div>
                        <div class="text-secondary mt-2"> </div>
                      </div>
                    </div>
                    
                    <div class="row mb-4">
                      <div class="col-sm-6">
                        <label for="email_address" class="col-sm-12 col-form-label form-label">Wise Email Address</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="email_address" id="email_address" value="<?= htmlspecialchars($settings['email_address'] ?? '') ?>">
                        </div>
                        <div class="text-secondary mt-2"> </div>
                      </div>
                    </div>
                </span>
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
    function received_by(){
        var received_by = document.querySelector("#received_by").value;
        
        document.querySelector(".wise-directly").style.display = "none";
        document.querySelector(".to-mfs").style.display = "none";
        
        document.querySelector("."+received_by).style.display = "block";
    }
    received_by();
    
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