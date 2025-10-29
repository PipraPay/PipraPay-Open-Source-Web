<?php
    if (file_exists(__DIR__."/../../pp-config.php")) {
        if (file_exists(__DIR__.'/../../maintenance.lock')) {
            if (file_exists(__DIR__.'/../../pp-include/pp-maintenance.php')) {

            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../../pp-include/pp-controller.php')) {
                if (file_exists(__DIR__.'/../../pp-include/pp-view.php')) {
    
                }else{
                    echo 'System is under maintenance. Please try again later.';
                    exit();
                }
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../../pp-include/pp-model.php')) {
                include(__DIR__."/../../pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
        }
    }else{
        echo 'System is under maintenance. Please try again later.';
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }
    
    if(isset($global_user_login) && $global_user_login == true){
?>
          <!-- Page Header -->
          <div class="page-header">
            <div class="row align-items-center">
              <div class="col">
                <h1 class="page-header-title">SMS Data</h1>
              </div>
              <!-- End Col -->
              <div class="col-auto">
                  <a class="btn btn-light" onclick="load_content('SMS Data','sms-data-devices','nav-btn-sms-data')"><i class="bi-phone me-1"></i> Connected Devices</a>
                  <a class="btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#connect-android-app" aria-controls="connect-android-app"><i class="bi-wifi me-1"></i> Connect Android App</a>
                  <a class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#new-sms-datatab" aria-controls="new-sms-datatab"><i class="bi-plus-circle me-1"></i> New SMS Data</a>
              </div>
            </div>
            <!-- End Row -->
          </div>
          <!-- End Page Header -->
          
          <div class="row justify-content-end mb-3 bulk-manage-tab" style="display: none">
            <div class="col-lg">
              <!-- Datatable Info -->
              <div id="datatableCounterInfo" style="">
                <div class="d-sm-flex justify-content-lg-end align-items-sm-center">
                  <span class="d-block d-sm-inline-block fs-5 me-3 mb-2 mb-sm-0">
                    <span id="bulk-manage-tab-counter">20</span>
                    Selected
                  </span>
                  <a class="btn btn-outline-danger btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-delete" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-delete', 'delete')">
                    <i class="bi-trash"></i> Delete
                  </a>
                  <a class="btn btn-success btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-approved" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-approved', 'approved')">
                    <i class="bi-check-circle"></i> Approved
                  </a>
                  <a class="btn btn-warning btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-review" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-review', 'review')">
                    <i class="bi-send"></i> Awaiting review
                  </a>
                  <a class="btn btn-primary btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-used" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-used', 'used')">
                    <i class="bi-send"></i> Used
                  </a>
                </div>
              </div>
              
              <span class="response-bulk-action"></span>
            </div>
          </div>
          
          
          
          
          
          
      <!-- Card -->
      <div class="card">
        <!-- Header -->
        <div class="card-header">
          <div class="row justify-content-between align-items-center flex-grow-1">
            <div class="col-md">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-header-title">SMS Data</h4>
              </div>
            </div>
            <!-- End Col -->
    
            <div class="col-auto">
              <!-- Filter -->
              <div class="row align-items-sm-center">
                <div class="col-sm-auto">
                  <div class="row align-items-center gx-0">
                    <div class="col">
                      <span class="text-secondary me-2">Status:</span>
                    </div>
                    <!-- End Col -->
    
                    <div class="col-auto">
                      <!-- Select -->
                      <div class="tom-select-custom tom-select-custom-end">
                        <select class="js-select form-select form-select-sm form-select-borderless sms_status" data-hs-tom-select-options='{
                                  "searchInDropdown": false,
                                  "hideSearch": true,
                                  "dropdownWidth": "10rem"
                                }' onchange="loadDataTable()">
                          <option value="all" selected>All</option>
                          <option value="approved">Approved</option>
                          <option value="review">Awaiting review</option>
                          <option value="used">Used</option>
                        </select>
                      </div>
                      <!-- End Select -->
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
                </div>
                <!-- End Col -->
    
                <div class="col-md">
                  <form>
                    <!-- Search -->
                    <div class="input-group input-group-merge input-group-flush">
                      <div class="input-group-prepend input-group-text">
                        <i class="bi-search"></i>
                      </div>
                      <input id="datatableSearch" type="search" class="form-control" placeholder="Search" aria-label="Search" onkeyup="loadDataTable()">
                    </div>
                    <!-- End Search -->
                  </form>
                </div>
                <!-- End Col -->
              </div>
              <!-- End Filter -->
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
        </div>
        <!-- End Header -->

        <!-- Table -->
        <div class="table-responsive datatable-custom">
          <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
            <thead class="thead-light">
              <tr>
                  <th scope="col" class="table-column-pe-0"><input type="checkbox" id="select-all" class="form-check-input"></th>
                  <th>Entry Type</th>
                  <th>Payment Method</th>
                  <th>SIM Slot</th>
                  <th>Mobile Number</th>
                  <th>Transaction ID</th>
                  <th>Amount</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Action</th>
              </tr>
            </thead>
            <tbody id="datatable">
                
            </tbody>
          </table>
        </div>
        <!-- End Table -->

        <!-- Footer -->
        <div class="card-footer">
          <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
            <div class="col-sm mb-2 mb-sm-0">
              <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                <span class="me-2">Showing:</span> <div class="tom-select-custom" id="showing-result"></div> <span class="text-secondary me-2" style="margin-left:8px;">of</span> <span id="total-result"></span>
              </div>
            </div>
            <!-- End Col -->

            <div class="col-sm-auto">
              <div class="d-flex justify-content-center justify-content-sm-end">
                <nav id="datatablePagination" aria-label="Activity pagination">
                    <div class="dataTables_paginate" id="datatable_paginate">
                        <ul id="datatable_pagination" class="pagination datatable-custom-pagination">
                            <li class="paginate_item page-item" id="prev-page">
                                <a class="paginate_button previous page-link" href="javascript:void(0)">
                                    <span aria-hidden="true">Prev</span>
                                </a>
                            </li>
                            <span id="page-numbers" style=" display: flex; "></span>
                            <li class="paginate_item page-item" id="next-page">
                                <a class="paginate_button next page-link" href="javascript:void(0)">
                                    <span aria-hidden="true">Next</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
              </div>
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
        </div>
        <!-- End Footer -->
      </div>
      <!-- End Card -->
              

              
        <div class="offcanvas offcanvas-end" tabindex="-1" id="new-sms-datatab" aria-labelledby="new-sms-datatab" style="max-width: 650px; width: 100%">
          <div class="offcanvas-header">
            <h5 id="new-sms-data">Create SMS Data</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="selectOption" class="form-label">Sim Slot</label>
                  <!-- Select -->
                  <div class="tom-select-custom tom-select-custom-end">
                    <select id="new-sms-data-sim" class="form-select form-select-sm">
                      <option value="sim1" selected>SIM 1</option>
                      <option value="sim2">SIM 2</option>
                    </select>
                  </div>
                  <!-- End Select -->
              </div>

              <div class="col-md-6">
                <label for="selectOption" class="form-label">Status</label>
                  <!-- Select -->
                  <div class="tom-select-custom tom-select-custom-end">
                    <select id="new-sms-data-status" class="form-select form-select-sm">
                      <option value="approved" selected>Approved</option>
                      <option value="review">Awaiting review</option>
                      <option value="used">Used</option>
                    </select>
                  </div>
                  <!-- End Select -->
              </div>

              <div class="col-md-6">
                <label for="selectOption" class="form-label">Payment Method</label>
                  <!-- Select -->
                  <div class="tom-select-custom tom-select-custom-end">
                    <select id="new-sms-data-payment-method" class="form-select form-select-sm">
                      <option value="bKash" selected>bKash</option>
                      <option value="Nagad">Nagad</option>
                      <option value="Rocket">Rocket</option>
                      <option value="Upay">Upay</option>
                      <option value="Tap">Tap</option>
                      <option value="OkWallet">OkWallet</option>
                      <option value="Cellfin">Cellfin</option>
                      <option value="Ipay">Ipay</option>
                    </select>
                  </div>
                  <!-- End Select -->
              </div>
        
              <!-- Amount -->
              <div class="col-md-6">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="new-sms-data-amount"  placeholder="Amount" required>
              </div>
        
              <!-- Phone Number -->
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="new-sms-data-phone-number" placeholder="Phone Number" required>
              </div>
        
              <!-- Transaction ID -->
              <div class="col-md-6">
                <label for="transactionId" class="form-label">Transaction ID</label>
                <input type="text" class="form-control" id="new-sms-data-transaction"  placeholder="Transaction ID" required>
              </div>
            </div>
            
            <span class="response-model-new-message"></span>
            
            <button class="btn btn-primary btn-insert-new-message mt-3" onclick="insert_newmessage()">Create</button>
          </div>
        </div>
              
              
              
              
              
              
              
              
              
              
        <div class="offcanvas offcanvas-end" tabindex="-1" id="connect-android-app" aria-labelledby="connect-android-app" style="max-width: 650px; width: 100%;">
          <div class="offcanvas-header">
            <h5 id="connect-android-app">Connect Android App</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body" style=" padding: 10px; ">
                <div class="card border-0 shadow-sm mb-4">
                  <div class="card-body">
                    <h2 class="h4 fw-bold text-dark mb-2">
                      Download Android App
                    </h2>
                    <p class="text-primary small mb-3">Only for Bangladeshi Users</p>
                
                    <p class="text-secondary mb-3">
                      Download our Android app to automate your personal payments effortlessly:
                    </p>
                
                    <ul class="list-unstyled text-secondary small">
                      <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Automatically verify your transactions</span>
                      </li>
                      <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-shield-lock-fill text-info me-2"></i>
                        <span>Grant necessary permissions for seamless operation</span>
                      </li>
                      <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-lock-fill text-warning me-2"></i>
                        <span>Secure background verification of your payments</span>
                      </li>
                      <li class="d-flex align-items-start">
                        <i class="bi bi-eye-fill text-primary me-2"></i>
                        <span>View verified transactions in the <strong>SMS Data</strong> section</span>
                      </li>
                    </ul>
                    
                    <center>
                        <button class="btn btn-warning btn-download-old-app" onclick="triggerDownload('old')"><i class="bi bi-download"></i> Download Old Version App</button><br><br>
                        <button class="btn btn-primary btn-download-new-app" onclick="triggerDownload('new')"><i class="bi bi-download"></i> Download New Version App</button>
                    </center>
                  </div>
                </div>
    
                <div class="card border-0 shadow-sm mb-4">
                  <div class="card-body">
                    <h2 class="h4 fw-bold text-dark mb-2">
                      Connect Android App
                    </h2>

                    <label for="sitename" class="col-sm-12 col-form-label form-label">Webhook URL</label>
                    <div class="input-group">
                         <input type="text" class="form-control" name="webhook-url" id="webhook-url" placeholder="Enter webhook url" aria-label="Enter webhook url" value="https://<?php echo $_SERVER['HTTP_HOST']?>/?webhook=<?php if($global_setting_response['response'][0]['webhook'] !== "--"){echo $global_setting_response['response'][0]['webhook'];}?>" readonly>
                         <span class="input-group-text" style="cursor:pointer" onclick="copyToClipboard('webhook-url')"><i class="bi bi-clipboard"></i></span>
                         <span class="input-group-text btn-arrow-repeat" style="cursor:pointer" onclick="generateapi()"><i class="bi bi-arrow-repeat"></i></span>
                    </div>

                  </div>
                </div>
          </div>
        </div>
              
              
        <script> 
            function triggerDownload(fileName) {
                document.querySelector(".btn-download-"+fileName+"-app").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
            
                // Create hidden link
                const link = document.createElement('a');
                link.href = 'https://<?php echo $_SERVER['HTTP_HOST']?>/admin/sms-data?download=' + encodeURIComponent(fileName);
                link.download = 'piprapay-tool-' + fileName + '.apk';
            
                // Start download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            
                // Hide loading after a few seconds (simulate)
                setTimeout(() => {
                    if(fileName == "new"){
                        document.querySelector(".btn-download-"+fileName+"-app").innerHTML = '<i class="bi bi-download"></i> Download New Version App';
                    }else{
                        document.querySelector(".btn-download-"+fileName+"-app").innerHTML = '<i class="bi bi-download"></i> Download Old Version App';
                    }
                }, 3000); // Adjust time as needed
            }
        
          function copyToClipboard(inputs) {
            const input = document.getElementById(inputs);
            input.select();
            input.setSelectionRange(0, 99999); 
        
            navigator.clipboard.writeText(input.value)
              .then(() => {

              });
          }
        
            function generateapi(){
              document.querySelector(".btn-arrow-repeat").innerHTML = '<div class="spinner-border text-primary spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "https://<?php echo $_SERVER['HTTP_HOST']?>/admin/dashboard",
                    data: { "action": "pp_generate_webhook"},
                    success: function (data) {
                        document.querySelector(".btn-arrow-repeat").innerHTML = '<i class="bi bi-arrow-repeat"></i>';
                        
                        var dedata = JSON.parse(data);
                        
                        document.querySelector("#webhook-url").value = 'https://<?php echo $_SERVER['HTTP_HOST']?>/?webhook='+dedata.api;
                    }
                });
            }
        </script>
        
        
        
        
        
        
      <script>
                function initialize111() {
                    new HSFormSearch('.js-form-search')
            
                    HSBsDropdown.init()
        
            
                    new HsNavScroller('.js-nav-scroller')
            
                    HSCore.components.HSDropzone.init('.js-dropzone')

            
                    HSBsDropdown.init()
                    
            
                    HSCore.components.HSTomSelect.init('.js-select')
            
                    HSCore.components.HSClipboard.init('.js-clipboard')
              }
              
              initialize111();
      
            $("#select-all").click(function(){
                $(".select-box").prop('checked', this.checked);
                
                var selectedCount = $(".select-box:checked").length;
                
                document.querySelector("#bulk-manage-tab-counter").innerHTML = selectedCount;
                
                if(selectedCount === 0){
                    document.querySelector(".bulk-manage-tab").style.display = "none";
                }else{
                    document.querySelector(".bulk-manage-tab").style.display = "flex";
                }
            });
            
            $(document).on('click', '.select-box', function() {
                var selectedCount = $(".select-box:checked").length;
                
                document.querySelector("#bulk-manage-tab-counter").innerHTML = selectedCount;
                
                if(selectedCount === 0){
                    document.querySelector(".bulk-manage-tab").style.display = "none";
                }else{
                    document.querySelector(".bulk-manage-tab").style.display = "flex";
                }
            });

            function loadDataTable(page = 1) {
                var search = document.querySelector("#datatableSearch").value;
                var sms_status = document.querySelector(".sms_status").value;
                
                const tbody = $("#datatable");
                tbody.empty();
        
                 tbody.html(`              
                    <tr class="odd">
                        <td valign="top" colspan="11" class="dataTables_empty">
                            <div class="text-center p-4">
                                  <div class="spinner-border text-primary" role="status"> <span class="visually-hidden">Loading...</span> </div>
                            </div>
                        </td>
                    </tr>
                `);
                
              $.ajax({
                type: "POST",
                url: "/admin/dashboard",
                data: { action: "pp_sms_data_list", page: page, search: search, sms_status: sms_status },
                success: function (data) {
                    tbody.empty();
                    
                  const dedata = JSON.parse(data);
            
                  if (dedata.total === 0) {
                    tbody.html(`
                        <tr class="odd">
                            <td valign="top" colspan="11" class="dataTables_empty">
                                <div class="text-center p-4">
                                      <img class="mb-3" src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/svg/illustrations/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
                                      <img class="mb-3" src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/svg/illustrations-light/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark">
                                      <p class="mb-0">No data to show</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    totalPages = 1;
                  } else {
                    totalPages = dedata.totalPages;
                    dedata.data.forEach(tx => {
                      tbody.append(`
                        <tr>
                          <td><input type="checkbox" class="form-check-input select-box" value="${tx.id || "--"}"></td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.entry_type || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.payment_method || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.sim || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.mobile_number || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.transaction_id || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.amount || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">${tx.balance || "--"}</td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id || "--"}" aria-controls="new-sms-data${tx.id || "--"}">
                              ${(() => {
                                switch (tx.status) {
                                  case 'approved':
                                    return '<span class="badge bg-success">Approved</span>';
                                  case 'review':
                                    return '<span class="badge bg-warning text-dark">Awaiting review</span>';
                                  case 'used':
                                    return '<span class="badge bg-primary">Used</span>';
                                  default:
                                    return '<span class="badge bg-dark">Unknown</span>';
                                }
                              })()}
                          </td>
                          <td data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id}" aria-controls="new-sms-data${tx.id}">${tx.created_at}</td>
                          <td>
                              <div class="btn-group" role="group"  data-bs-toggle="offcanvas" data-bs-target="#new-sms-data${tx.id}" aria-controls="new-sms-data${tx.id}">
                                <a class="btn btn-white btn-sm">
                                  <i class="bi-pencil-square me-1"></i> Edit
                                </a>
                              </div>
                              
                                  
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="new-sms-data${tx.id}" aria-labelledby="new-sms-data${tx.id}" style="max-width: 650px; width: 100%">
                              <div class="offcanvas-header">
                                <h5 id="new-sms-data">Edit SMS Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                              </div>
                              <div class="offcanvas-body">
                                <div class="row g-3">
                                  <div class="col-md-6">
                                    <label for="selectOption" class="form-label">Sim Slot</label>
                                      <!-- Select -->
                                      <div class="tom-select-custom tom-select-custom-end">
                                        <select id="new-sms-data-sim${tx.id}" class="form-select form-select-sm">
                                          <option value="sim1" ${tx.sim === 'SIM 1' ? 'selected' : ''}>SIM 1</option>
                                          <option value="sim2" ${tx.sim === 'SIM 2' ? 'selected' : ''}>SIM 2</option>
                                        </select>
                                      </div>
                                      <!-- End Select -->
                                  </div>
                    
                                  <div class="col-md-6">
                                    <label for="selectOption" class="form-label">Status</label>
                                      <!-- Select -->
                                      <div class="tom-select-custom tom-select-custom-end">
                                        <select id="new-sms-data-status${tx.id}" class="form-select form-select-sm">
                                          <option value="approved" ${tx.status === 'approved' ? 'selected' : ''}>Approved</option>
                                          <option value="review" ${tx.status === 'review' ? 'selected' : ''}>Awaiting review</option>
                                          <option value="used" ${tx.status === 'used' ? 'selected' : ''}>Used</option>
                                        </select>
                                      </div>
                                      <!-- End Select -->
                                  </div>
                    
                                  <div class="col-md-6">
                                    <label for="selectOption" class="form-label">Payment Method</label>
                                      <!-- Select -->
                                      <div class="tom-select-custom tom-select-custom-end">
                                        <select id="new-sms-data-payment-method${tx.id}" class="form-select form-select-sm">
                                          <option value="bKash" ${tx.payment_method === 'bKash' ? 'selected' : ''}>bKash</option>
                                          <option value="Nagad" ${tx.payment_method === 'Nagad' ? 'selected' : ''}>Nagad</option>
                                          <option value="Rocket" ${tx.payment_method === 'Rocket' ? 'selected' : ''}>Rocket</option>
                                          <option value="Upay" ${tx.payment_method === 'Upay' ? 'selected' : ''}>Upay</option>
                                          <option value="Tap" ${tx.payment_method === 'Tap' ? 'selected' : ''}>Tap</option>
                                          <option value="OkWallet" ${tx.payment_method === 'OkWallet' ? 'selected' : ''}>OkWallet</option>
                                          <option value="Cellfin" ${tx.payment_method === 'Cellfin' ? 'selected' : ''}>Cellfin</option>
                                          <option value="Ipay" ${tx.payment_method === 'Ipay' ? 'selected' : ''}>Ipay</option>
                                        </select>
                                      </div>
                                      <!-- End Select -->
                                  </div>
                            
                                  <!-- Amount -->
                                  <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="new-sms-data-amount${tx.id}"  placeholder="Amount" value="${tx.amount}">
                                  </div>
                            
                                  <!-- Phone Number -->
                                  <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="new-sms-data-phone-number${tx.id}" placeholder="Phone Number" value="${tx.mobile_number}">
                                  </div>
                            
                                  <!-- Transaction ID -->
                                  <div class="col-md-6">
                                    <label for="transactionId" class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control" id="new-sms-data-transaction${tx.id}"  placeholder="Transaction ID" value="${tx.transaction_id}">
                                  </div>
                                      
                                  ${(() => {
                                    switch (tx.entry_type) {
                                      case 'Automatic':
                                        return `
                                          <div class="col-md-12">
                                            <label for="transactionId" class="form-label">Message</label>
                                            <textarea class="form-control" rows="3">${tx.message}</textarea>
                                          </div>
                                        `;
                                      default:
                                        return '';
                                    }
                                  })()}
                                </div>
                                
                                <span class="response-model-new-message${tx.id}"></span>
                                
                                <button class="btn btn-primary btn-insert-new-message${tx.id} mt-3" onclick="save_newmessage(${tx.id})">Save Changes</button>
                              </div>
                            </div>
                                  
                          </td>
                        </tr>
                      `);
                    });
                    totalPages = dedata.totalPages;
                    currentPage = dedata.currentPage;
                    
                    document.querySelector("#total-result").innerHTML = dedata.total;
                    document.querySelector("#showing-result").innerHTML = dedata.showing;
                    
                    renderPagination();
                  }
                }
              });
            }
            
            loadDataTable();
            
            function save_newmessage(id){
              var method = document.querySelector("#new-sms-data-payment-method"+id).value;
              var amount = document.querySelector("#new-sms-data-amount"+id).value;
              var phone_number = document.querySelector("#new-sms-data-phone-number"+id).value;
              var transaction_id = document.querySelector("#new-sms-data-transaction"+id).value;
              var sim_slot = document.querySelector("#new-sms-data-sim"+id).value;
              var status = document.querySelector("#new-sms-data-status"+id).value;
              
              document.querySelector(".btn-insert-new-message"+id).innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "https://<?php echo $_SERVER['HTTP_HOST']?>/admin/sms-data",
                    data: { "action": "pp_sms-edit-message", "method": method, "amount": amount, "phone_number": phone_number, "transaction_id": transaction_id, "sim_slot": sim_slot, "status": status, "sms_id": id},
                    success: function (data) {
                        document.querySelector(".btn-insert-new-message"+id).innerHTML = 'Save Changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-model-new-message"+id).innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            $('#new-sms-data').modal('hide');
    
                            load_content('SMS Data','sms-data','nav-btn-sms-data');
                            document.querySelector(".response-model-new-message"+id).innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
            }
            
            function renderPagination() {
                const pageNumbers = document.getElementById("page-numbers");
                pageNumbers.innerHTML = "";
            
                // Show up to 5 pages for simplicity
                const start = Math.max(1, currentPage - 2);
                const end = Math.min(totalPages, currentPage + 2);
            
                for (let i = start; i <= end; i++) {
                    const li = document.createElement("li");
                    li.className = `paginate_item page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="paginate_button page-link" href="javascript:void(0)">${i}</a>`;
                    li.addEventListener("click", () => loadDataTable(i));
                    pageNumbers.appendChild(li);
                }
            
                // Enable/disable prev/next
                document.getElementById("prev-page").classList.toggle("disabled", currentPage === 1);
                document.getElementById("next-page").classList.toggle("disabled", currentPage === totalPages);
            }
            
            document.getElementById("prev-page").addEventListener("click", () => {
                if (currentPage > 1) loadDataTable(currentPage - 1);
            });
            
            document.getElementById("next-page").addEventListener("click", () => {
                if (currentPage < totalPages) loadDataTable(currentPage + 1);
            });
            
            function bulk_action(bclass, action){
                var btn_value = document.querySelector("."+bclass).innerHTML;
                
                $("."+bclass).html('<div class="spinner-border spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>');
                $("."+bclass).prop("disabled", true);
    
                var ids = [];
                $(".select-box:checked").each(function(){
                    ids.push($(this).val());
                });
            
                var selectedCount = ids.length;
            
                if (selectedCount === 0) {
                    $(".response-bulk-action").html('<div class="alert alert-danger" style="margin-top: 20px; margin-bottom: -1px;" role="alert">Please select at least one record to delete.</div>');
                }else{
                    var formData = new FormData();
                    formData.append('action', 'pp_bulk_action_sms_data');
                    formData.append('action_name', action);
                    formData.append('ids', ids);
                    
                    $.ajax({
                        type: "POST",
                        url: "/admin/sms-data",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            console.log(data);
                            $("."+bclass).prop("disabled", false);
                            $("."+bclass).html(btn_value);
            
                            var response = JSON.parse(data);
        
                            if (response.status === "false") {
                                $(".response-bulk-action").html('<div class="alert alert-danger" style="margin-top: 20px; margin-bottom: -1px;" role="alert">'+response.message+'</div>');
                            } else {
                                $(".response-bulk-action").html('');
                                load_content('SMS Data','sms-data','nav-btn-sms-data');
                            }
                        }
                    });
                }
            }
            
            function insert_newmessage(){
              var method = document.querySelector("#new-sms-data-payment-method").value;
              var amount = document.querySelector("#new-sms-data-amount").value;
              var phone_number = document.querySelector("#new-sms-data-phone-number").value;
              var transaction_id = document.querySelector("#new-sms-data-transaction").value;
              var sim_slot = document.querySelector("#new-sms-data-sim").value;
              var status = document.querySelector("#new-sms-data-status").value;
              
              document.querySelector(".btn-insert-new-message").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "https://<?php echo $_SERVER['HTTP_HOST']?>/admin/sms-data",
                    data: { "action": "pp_sms-new-message", "method": method, "amount": amount, "phone_number": phone_number, "transaction_id": transaction_id, "sim_slot": sim_slot, "status": status},
                    success: function (data) {
                        document.querySelector(".btn-insert-new-message").innerHTML = 'Create';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-model-new-message").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            $('#new-sms-datatab').modal('hide');
    
                            load_content('SMS Data','sms-data','nav-btn-sms-data');
                            document.querySelector(".response-model-new-message").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
            }
      </script>
<?php
    }
?>