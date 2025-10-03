<?php
    if (file_exists(__DIR__."/../pp-config.php")) {
        if (file_exists(__DIR__.'/../maintenance.lock')) {
            if (file_exists(__DIR__.'/../pp-include/pp-maintenance.php')) {
               include(__DIR__."/../pp-include/pp-maintenance.php");
            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../pp-include/pp-controller.php')) {
                include(__DIR__."/../pp-include/pp-controller.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../pp-include/pp-model.php')) {
                include(__DIR__."/../pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../pp-include/pp-view.php')) {
                include(__DIR__."/../pp-include/pp-view.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if($global_user_login == false){
?>
                <script>
                    location.href="https://<?php echo $_SERVER['HTTP_HOST']?>/admin/login";
                </script>
<?php
                exit();
            }
        }
    }else{
?>
        <script>
            location.href="https://<?php echo $_SERVER['HTTP_HOST']?>/install/";
        </script>
<?php
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }
    
    if(isset($_GET['download'])){
?>
        <script>location.href = "https://github.com/PipraPay/PipraPay-Open-Source-App/";</script>
<?php
        exit();
    }
    
    if (function_exists('pp_trigger_hook')) {
        pp_trigger_hook('pp_admin_initialize');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Dashboard - PipraPay</title>
  <link rel="icon" type="image/x-icon" href="https://cdn.piprapay.com/media/favicon.png">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/vendor.min.css">

  <link rel="stylesheet" href="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme.minc619.css?v=1.3">

  <link rel="preload" href="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme.min.css?v=1.4" data-hs-appearance="default" as="style">
  <link rel="preload" href="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme-dark.min.css" data-hs-appearance="dark" as="style">

  <style data-hs-appearance-onload-styles>
    *
    {
      transition: unset !important;
    }

    body
    {
      opacity: 0;
    }
  </style>

  <style>
    body
    {
      opacity: 0;
    }
    table tr:hover {
      background-color: #f8fafd;
      cursor: pointer;
    }
  </style>
  
  <script>
    window.hs_config = {"autopath":"@@autopath","deleteLine":"hs-builder:delete","deleteLine:build":"hs-builder:build-delete","deleteLine:dist":"hs-builder:dist-delete","previewMode":false,"startPath":"/javascript:void(0)","vars":{"themeFont":"https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap","version":"?v=1.0"},"layoutBuilder":{"extend":{"switcherSupport":true},"header":{"layoutMode":"default","containerMode":"container-fluid"},"sidebarLayout":"default"},"themeAppearance":{"layoutSkin":"default","sidebarSkin":"default","styles":{"colors":{"primary":"#3BB77E","transparent":"transparent","white":"#fff","dark":"132144","gray":{"100":"#f9fafc","900":"#1e2022"}},"font":"Inter"}},"languageDirection":{"lang":"en"},"skipFilesFromBundle":{"dist":["https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/hs.theme-appearance.js","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/hs.theme-appearance-charts.js","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/demo.js"],"build":["https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme.css","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/demo.js","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme-dark.html","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/docs.css","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/vendor/icon-set/style.html","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/hs.theme-appearance.js","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/hs.theme-appearance-charts.js","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.html","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/demo.js"]},"minifyCSSFiles":["https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme.css","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css/theme-dark.css"],"copyDependencies":{"dist":{"*assets/js/theme-custom.js":""},"build":{"*assets/js/theme-custom.js":"","https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/node_modules/bootstrap-icons/font/*fonts/**":"https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/css"}},"buildFolder":"","replacePathsToCDN":{},"directoryNames":{"src":"./src","dist":"./dist","build":"./build"},"fileNames":{"dist":{"js":"theme.min.js","css":"theme.min.css"},"build":{"css":"theme.min.css","js":"theme.min.js","vendorCSS":"vendor.min.css","vendorJS":"vendor.min.js"}},"fileTypes":"jpg|png|svg|mp4|webm|ogv|json"}
    window.hs_config.gulpRGBA = (p1) => {
    const options = p1.split(',')
    const hex = options[0].toString()
    const transparent = options[1].toString()
    
    var c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
    c= hex.substring(1).split('');
    if(c.length== 3){
    c= [c[0], c[0], c[1], c[1], c[2], c[2]];
    }
    c= '0x'+c.join('');
    return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+',' + transparent + ')';
    }
    throw new Error('Bad Hex');
    }
    window.hs_config.gulpDarken = (p1) => {
    const options = p1.split(',')
    
    let col = options[0].toString()
    let amt = -parseInt(options[1])
    var usePound = false
    
    if (col[0] == "#") {
    col = col.slice(1)
    usePound = true
    }
    var num = parseInt(col, 16)
    var r = (num >> 16) + amt
    if (r > 255) {
    r = 255
    } else if (r < 0) {
    r = 0
    }
    var b = ((num >> 8) & 0x00FF) + amt
    if (b > 255) {
    b = 255
    } else if (b < 0) {
    b = 0
    }
    var g = (num & 0x0000FF) + amt
    if (g > 255) {
    g = 255
    } else if (g < 0) {
    g = 0
    }
    return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16)
    }
    window.hs_config.gulpLighten = (p1) => {
    const options = p1.split(',')
    
    let col = options[0].toString()
    let amt = parseInt(options[1])
    var usePound = false
    
    if (col[0] == "#") {
    col = col.slice(1)
    usePound = true
    }
    var num = parseInt(col, 16)
    var r = (num >> 16) + amt
    if (r > 255) {
    r = 255
    } else if (r < 0) {
    r = 0
    }
    var b = ((num >> 8) & 0x00FF) + amt
    if (b > 255) {
    b = 255
    } else if (b < 0) {
    b = 0
    }
    var g = (num & 0x0000FF) + amt
    if (g > 255) {
    g = 255
    } else if (g < 0) {
    g = 0
    }
    return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16)
    }
  </script>
</head>

<body class="has-navbar-vertical-aside navbar-vertical-aside-show-xl   footer-offset">
    <div class="progress" style=" position: fixed; top: 0; width: 100%; left: 0; height: 3px; z-index: 101; ">
      <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/hs.theme-appearance.js"></script>

  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js"></script>

  <!-- ========== HEADER ========== -->

  <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-container navbar-bordered bg-white">
    <div class="navbar-nav-wrap">
      <!-- Logo -->
      <a class="navbar-brand" href="javascript:void(0)" aria-label="Front">
        <img class="navbar-brand-logo" src="https://cdn.piprapay.com/media/logo.png" alt="Logo" data-hs-theme-appearance="default">
        <img class="navbar-brand-logo" src="https://cdn.piprapay.com/media/logo-black.png" alt="Logo" data-hs-theme-appearance="dark">
        <img class="navbar-brand-logo-mini" src="https://cdn.piprapay.com/media/favicon.png" alt="Logo" data-hs-theme-appearance="default">
        <img class="navbar-brand-logo-mini" src="https://cdn.piprapay.com/media/favicon.png" alt="Logo" data-hs-theme-appearance="dark">
      </a>
      <!-- End Logo -->

      <div class="navbar-nav-wrap-content-start">
        <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
          <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
          <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
        </button>
      </div>

      <div class="navbar-nav-wrap-content-end">
        <!-- Navbar -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <!-- Account -->
            <div class="dropdown">
              <a class="navbar-dropdown-account-wrapper" href="javascript:;" id="accountNavbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" data-bs-dropdown-animation>
                <div class="avatar avatar-sm avatar-circle">
                  <img class="avatar-img" src="https://cdn.piprapay.com/media/default.jpg" alt="Image Description">
                  <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                </div>
              </a>

              <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-account" aria-labelledby="accountNavbarDropdown" style="width: 16rem;">
                <div class="dropdown-item-text">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm avatar-circle">
                      <img class="avatar-img" src="https://cdn.piprapay.com/media/default.jpg" alt="Image Description">
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h5 class="mb-0"><?php echo $global_user_response['response'][0]['name']?></h5>
                      <p class="card-text text-body">@<?php echo $global_user_response['response'][0]['username']?></p>
                    </div>
                  </div>
                </div>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="javascript:void(0);" onclick="load_content('Admin Settings','system-admin-setting','nav-btn-admin-setting')">Settings</a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="?logout">Sign out</a>
              </div>
            </div>
            <!-- End Account -->
          </li>
        </ul>
        <!-- End Navbar -->
      </div>
    </div>
  </header>

  <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered bg-white  ">
    <div class="navbar-vertical-container">
      <div class="navbar-vertical-footer-offset">
        <!-- Logo -->

        <a class="navbar-brand" href="javascript:void(0)" aria-label="Front">
          <img class="navbar-brand-logo" src="https://cdn.piprapay.com/media/logo.png" alt="Logo" data-hs-theme-appearance="default">
          <img class="navbar-brand-logo" src="https://cdn.piprapay.com/media/logo-black.png" alt="Logo" data-hs-theme-appearance="dark">
          <img class="navbar-brand-logo-mini" src="https://cdn.piprapay.com/media/favicon.png" alt="Logo" data-hs-theme-appearance="default">
          <img class="navbar-brand-logo-mini" src="https://cdn.piprapay.com/media/favicon.png" alt="Logo" data-hs-theme-appearance="dark">
        </a>

        <!-- End Logo -->

        <!-- Navbar Vertical Toggle -->
        <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
          <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
          <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
        </button>

        <!-- End Navbar Vertical Toggle -->

        <!-- Content -->
        <div class="navbar-vertical-content">
          <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">
            <!-- Collapse -->
            <div class="nav-item">
              <a class="nav-link nav-btn-dashboard" href="javascript:void(0);" onclick="load_content('Dashboard','dashboard','nav-btn-dashboard')">
                <i class="bi-house-door nav-icon"></i>
                <span class="nav-link-title">Dashboard</span>
              </a>
            </div>
            <!-- End Collapse -->
            
            <div id="navbarVerticalMenuPagesMenu">
                <span class="dropdown-header mt-4">Payment</span>
                <small class="bi-three-dots nav-subtitle-replacer"></small>
                
                <!-- Collapse -->
                <div class="nav-item">
                  <a class="nav-link nav-btn-transaction" href="javascript:void(0);" onclick="load_content('Transaction','transaction','nav-btn-transaction')">
                    <i class="bi bi-receipt nav-icon"></i>
                    <span class="nav-link-title">Transaction</span>
                  </a>
                </div>
                <!-- End Collapse -->

                <!-- Collapse -->
                <div class="nav-item">
                  <a class="nav-link nav-btn-customers" href="javascript:void(0);" onclick="load_content('Customers','customers','nav-btn-customers')">
                    <i class="bi bi-people nav-icon"></i>
                    <span class="nav-link-title">Customers</span>
                  </a>
                </div>
                <!-- End Collapse -->
                
                <!-- Collapse -->
                <div class="nav-item">
                  <a class="nav-link nav-btn-invoices" href="javascript:void(0);" onclick="load_content('Invoices','invoices','nav-btn-invoices')">
                    <i class="bi bi-receipt-cutoff nav-icon"></i>
                    <span class="nav-link-title">Invoices</span>
                  </a>
                </div>
                <!-- End Collapse -->
                
                <!-- Collapse -->
                <div class="nav-item">
                  <a class="nav-link nav-btn-payment-link" href="javascript:void(0);" onclick="load_content('Payment Links','payment-link','nav-btn-payment-link')">
                    <i class="bi bi-link-45deg nav-icon"></i>
                    <span class="nav-link-title">Payment Links</span>
                  </a>
                </div>
                <!-- End Collapse -->
                
                <!-- Collapse -->
                <div class="nav-item">
                  <a class="nav-link nav-btn-sms-data" href="javascript:void(0);" onclick="load_content('SMS Data','sms-data','nav-btn-sms-data')">
                    <i class="bi bi-phone nav-icon"></i>
                    <span class="nav-link-title">SMS Data</span>
                  </a>
                </div>
                <!-- End Collapse -->
                
                <span class="dropdown-header mt-4">Customize</span>
                <small class="bi-three-dots nav-subtitle-replacer"></small>
                
                <!-- Collapse -->
                <div class="nav-item">
                    <a class="nav-link dropdown-toggle nav-btn-appearance" href="#appearance_menu" role="button" data-bs-toggle="collapse" data-bs-target="#appearance_menu" aria-expanded="false" aria-controls="appearance_menu">
                      <i class="bi bi-brush nav-icon"></i>
                      <span class="nav-link-title">Appearance</span>
                    </a>
    
                    <div id="appearance_menu" class="nav-collapse collapse " data-bs-parent="#appearance_menu">
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('Appearance','appearance-themes','nav-btn-appearance')">Themes</a>
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('Appearance','appearance-customize','nav-btn-appearance')">Customize</a>
                      <a class="nav-link" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-new-plugins">Add New</a>
                    </div>
                </div>
                <!-- End Collapse -->
                
                <!-- Collapse -->
                <div class="nav-item">
                    <a class="nav-link dropdown-toggle nav-btn-plugin" href="#plugin_menu" role="button" data-bs-toggle="collapse" data-bs-target="#plugin_menu" aria-expanded="false" aria-controls="plugin_menu">
                      <i class="bi bi-plugin nav-icon"></i>
                      <span class="nav-link-title">Plugin</span>
                    </a>
    
                    <div id="plugin_menu" class="nav-collapse collapse " data-bs-parent="#plugin_menu">
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('Plugin','plugin-manager','nav-btn-plugin')">Installed Plugin</a>
                      <a class="nav-link" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-new-plugins">Add New</a>
                    </div>
                </div>
                <!-- End Collapse -->
                
                <!-- Collapse -->
                <div class="nav-item">
                    <a class="nav-link dropdown-toggle nav-btn-system-setting" href="#system-setting-menu" role="button" data-bs-toggle="collapse" data-bs-target="#system-setting-menu" aria-expanded="false" aria-controls="navbarVerticalMenuPagesUsersMenu">
                      <i class="bi bi-gear nav-icon"></i>
                      <span class="nav-link-title">System Settings</span>
                    </a>
    
                    <div id="system-setting-menu" class="nav-collapse collapse " data-bs-parent="#system-setting-menu">
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('System Settings','system-setting-general','nav-btn-system-setting')">General Settings</a>
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('System Settings','system-setting-api','nav-btn-system-setting')">API Settings</a>
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('CronJob','system-setting-cron-job','nav-btn-system-setting')">Cron Job</a>
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('System Currency','system-setting-currency','nav-btn-system-currency')">Currency Settings</a>
                      <a class="nav-link" href="javascript:void(0);" onclick="load_content('System Settings','system-setting-faq','nav-btn-system-setting')">FAQ Settings</a>
                    </div>
                </div>
                <!-- End Collapse -->
                

                <span class="dropdown-header mt-4">More</span>
                <small class="bi-three-dots nav-subtitle-replacer"></small>
                <?php
                    $baseDir = __DIR__.'/../pp-content/plugins/';
                    
                    $mainFolders = array_filter(scandir($baseDir), function($item) use ($baseDir) {
                        return $item !== '.' && $item !== '..' && is_dir($baseDir . DIRECTORY_SEPARATOR . $item);
                    });
                    
                    foreach ($mainFolders as $mainFolder) {
                        $mainFolder_rand = rand();
                ?>
                        <!-- Collapse -->
                        <div class="nav-item">
                            <a class="nav-link dropdown-toggle nav-btn-more-<?php echo $mainFolder.$mainFolder_rand?>" href="#more-<?php echo $mainFolder.$mainFolder_rand?>" role="button" data-bs-toggle="collapse" data-bs-target="#more-<?php echo $mainFolder.$mainFolder_rand?>" aria-expanded="false" aria-controls="navbarVerticalMenuPagesUsersMenu">
                              <i class="bi bi-gear nav-icon"></i>
                              <span class="nav-link-title"><?php echo ucwords(str_replace('-', ' ', $mainFolder))?></span>
                            </a>
                    
                            <div id="more-<?php echo $mainFolder.$mainFolder_rand?>" class="nav-collapse collapse " data-bs-parent="#more-<?php echo $mainFolder.$mainFolder_rand?>">
                                <div class="p-2">
                                  <input type="text" class="form-control form-control-sm" placeholder="Search..." onkeyup="filterMenu(this, 'more-<?php echo $mainFolder.$mainFolder_rand?>')">
                                </div>
                                
                                <?php
                                    $pluginBasePath = $baseDir . DIRECTORY_SEPARATOR . $mainFolder;
                                
                                    $pluginFolders = array_filter(scandir($pluginBasePath), function($item) use ($pluginBasePath) {
                                        return $item !== '.' && $item !== '..' && is_dir($pluginBasePath . DIRECTORY_SEPARATOR . $item);
                                    });
                    
                                    $foundPlugins = false;
                    
                                    foreach ($pluginFolders as $pluginFolder) {
                                        $mainFile = $pluginBasePath . DIRECTORY_SEPARATOR . $pluginFolder . DIRECTORY_SEPARATOR . $pluginFolder . '-class.php';
                                
                                        if (file_exists($mainFile)) {
                                            $response_plugin = json_decode(getData($db_prefix.'plugins', 'WHERE plugin_slug="'.$pluginFolder.'" AND status="active"'), true);
                                            if($response_plugin['status'] == true){
                                                $foundPlugins = true;
                                                $pluginInfo = parsePluginHeader($mainFile);
                                ?>
                                                <a class="nav-link" href="javascript:void(0);" onclick="load_content('<?php echo htmlspecialchars($pluginInfo['Plugin Name'] ?? '')?>','plugin-loader?page=<?php echo $mainFolder.'--'.htmlspecialchars($pluginFolder)?>','nav-btn-more-<?php echo $mainFolder.$mainFolder_rand?>')"><?php echo htmlspecialchars($pluginInfo['Plugin Name'] ?? '')?></a>
                                <?php
                                            }
                                        }
                                    }
                    
                                    if (!$foundPlugins) {
                                ?>
                                        <a class="nav-link" href="javascript:void(0);">No plugins available</a>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- End Collapse -->
                <?php
                    }
                ?>

                <span class="dropdown-header mt-4">Documentation</span>
                <small class="bi-three-dots nav-subtitle-replacer"></small>
    
                <div class="nav-item">
                  <a class="nav-link " href="https://play.google.com/store/apps/details?id=com.qube.piprapay" target="blank" data-placement="left">
                    <i class="bi-android nav-icon"></i>
                    <span class="nav-link-title">Android Payment Panel</span>
                  </a>
                </div>
    
                <div class="nav-item">
                  <a class="nav-link " href="https://piprapay.readme.io/" target="blank" data-placement="left">
                    <i class="bi-book nav-icon"></i>
                    <span class="nav-link-title">Documentation</span>
                  </a>
                </div>
          </div>

        </div>

        <div class="navbar-vertical-footer">
          <ul class="navbar-vertical-footer-list">
            <li class="navbar-vertical-footer-list-item">
              <div class="dropdown dropup">
                <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>

                </button>

                <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless" aria-labelledby="selectThemeDropdown">
                  <a class="dropdown-item" href="#" data-icon="bi-moon-stars" data-value="auto">
                    <i class="bi-moon-stars me-2"></i>
                    <span class="text-truncate" title="Auto (system default)">Auto (system default)</span>
                  </a>
                  <a class="dropdown-item" href="#" data-icon="bi-brightness-high" data-value="default">
                    <i class="bi-brightness-high me-2"></i>
                    <span class="text-truncate" title="Default (light mode)">Default (light mode)</span>
                  </a>
                </div>
              </div>

            </li>

            <li class="navbar-vertical-footer-list-item">
              <div class="dropdown dropup">
                <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="otherLinksDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>
                  <i class="bi-info-circle"></i>
                </button>

                <div class="dropdown-menu navbar-dropdown-menu-borderless" aria-labelledby="otherLinksDropdown">
                  <span class="dropdown-header">Help</span>
                  <a class="dropdown-item" href="https://www.youtube.com/@piprapay" target="blank">
                    <i class="bi-journals dropdown-item-icon"></i>
                    <span class="text-truncate" title="Resources &amp; tutorials">Resources &amp; tutorials</span>
                  </a>
                  <div class="dropdown-divider"></div>
                  <span class="dropdown-header">Contacts</span>
                  <a class="dropdown-item" href="https://www.facebook.com/piprapay" target="blank">
                    <i class="bi-chat-left-dots dropdown-item-icon"></i>
                    <span class="text-truncate" title="Contact support">Contact support</span>
                  </a>
                </div>
              </div>
            </li>

            <li class="navbar-vertical-footer-list-item">
              <div class="dropdown dropup">
                <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="selectLanguageDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>
                  <img class="avatar avatar-xss avatar-circle" src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/vendor/flag-icon-css/flags/1x1/us.svg" alt="United States Flag">
                </button>

                <div class="dropdown-menu navbar-dropdown-menu-borderless" aria-labelledby="selectLanguageDropdown">
                  <span class="dropdown-header">Select language</span>
                  <a class="dropdown-item" href="#">
                    <img class="avatar avatar-xss avatar-circle me-2" src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/vendor/flag-icon-css/flags/1x1/us.svg" alt="Flag">
                    <span class="text-truncate" title="English">English (US)</span>
                  </a>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </aside>


  <main id="content" role="main" class="main">
    <div class="content container-fluid layout-content-prase">

    </div>

    <div class="footer">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <p class="fs-6 mb-0">&copy; PipraPay. <span class="d-none d-sm-inline-block">2025 QubePlug Bangladesh. <strong><?php echo $global_version['version'];?></strong></span></p>
        </div>
        <div class="col-auto">
          <div class="d-flex justify-content-end">
            <ul class="list-inline list-separator">
              <li class="list-inline-item">
                <a class="list-separator-link" href="https://piprapay.com/#faq">FAQ</a>
              </li>

              <li class="list-inline-item">
                <a class="list-separator-link" href="https://piprapay.com/">Visit</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>

  <div class="modal fade" id="add-new-plugins" tabindex="-1" aria-labelledby="add-new-plugins" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="add-new-plugins">Add Themes or Plugins</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">
            <label for="add-themes-plugins-files">Choose Zip files that you download from piprapay directory</label>
            <br>
            <br>
            <input type="file" class="form-control" id="add-themes-plugins-files" accept=".zip">
            
            <span class="response-add-themes-plugins-files"></span>
        </div>
        <!-- End Body -->

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-white" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          <button type="button" class="btn btn-primary btn-add-themes-plugins">Upload</button>
        </div>
        <!-- End Footer -->
      </div>
    </div>
  </div>

  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/demo.js?v=1.8"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/vendor.min.js"></script>

  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>

  <!-- JS Front -->
  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/theme.min.js"></script>
  <script src="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/admin/assets/js/hs.theme-appearance-charts.js"></script>

  <script>
        document.querySelector(".btn-add-themes-plugins").addEventListener("click", function () {
          const fileInput = document.getElementById("add-themes-plugins-files");
          const responseBox = document.querySelector(".response-add-themes-plugins-files");
        
          if (fileInput.files.length === 0) {
            responseBox.innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px">Please select a ZIP file to upload.</div>';
            return;
          }
        
          const file = fileInput.files[0];
          const formData = new FormData();
          formData.append("zip_file", file);
          formData.append("action", "pp-theme-plugins-import");
          
          responseBox.innerHTML = "";
          
          document.querySelector(".btn-add-themes-plugins").innerHTML = '<div class="spinner-border spinner-border-sm text-white" role="status"> <span class="visually-hidden">Loading...</span> </div>';
        
          fetch("dashboard", {
            method: "POST",
            body: formData
          })
          .then(res => res.json())
          .then(data => {
              document.querySelector(".btn-add-themes-plugins").innerHTML = 'Upload';
              
            if (data.status === "success") {
              responseBox.innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px">Uploaded and installed successfully.</div>';
            } else {
              responseBox.innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px">Error: ' + data.message + '</div>';
            }
          })
          .catch(err => {
            console.error(err);
            responseBox.innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px">Something went wrong.</div>';
          });
        });
          
  
  
    function filterMenu(inputElement, parentId) {
      const filter = inputElement.value.toLowerCase();
      const parent = document.getElementById(parentId);
      const links = parent.querySelectorAll('a.nav-link');
    
      links.forEach(link => {
        const text = link.textContent.toLowerCase();
        if (text.includes(filter)) {
          link.style.display = '';
        } else {
          link.style.display = 'none';
        }
      });
    }
  
    (function() {
      localStorage.removeItem('hs_theme')

      window.onload = function () {
        new HSSideNav('.js-navbar-vertical-aside').init()
      }
    })()
  </script>

  <script>
      (function () {
        const $dropdownBtn = document.getElementById('selectThemeDropdown') 
        const $variants = document.querySelectorAll(`[aria-labelledby="selectThemeDropdown"] [data-icon]`)

        const setActiveStyle = function () {
          $variants.forEach($item => {
            if ($item.getAttribute('data-value') === HSThemeAppearance.getOriginalAppearance()) {
              $dropdownBtn.innerHTML = `<i class="${$item.getAttribute('data-icon')}" />`
              return $item.classList.add('active')
            }

            $item.classList.remove('active')
          })
        }

        $variants.forEach(function ($item) {
          $item.addEventListener('click', function () {
            HSThemeAppearance.setAppearance($item.getAttribute('data-value'))
          })
        })

        setActiveStyle()

        window.addEventListener('on-hs-appearance-change', function () {
          setActiveStyle()
        })
      })()
    </script>
    <input type="hidden" id="navbar-aside-toggler-value" value = "0">
    <script>
        function checkDevice() {
            if (window.innerWidth <= 600) {
                var values = document.querySelector("#navbar-aside-toggler-value").value;
                
                if(values == 1){
                   document.querySelector(".navbar-aside-toggler").click();
                }else{
                    document.querySelector("#navbar-aside-toggler-value").value = '1';
                }
            }
        }
    
        function load_content(page, url, nav_id, fromPopState = false) {
            const cleanPath = url.split('?')[0];
        
            document.querySelector('.progress').style.display = 'block';
            let progress = 0;
        
            const interval = setInterval(function () {
                if (progress < 90) {
                    progress += 5;
                    document.querySelector('.progress-bar').style.width = progress + '%';
                }
            }, 100);
        
            fetch('https://<?php echo $_SERVER['HTTP_HOST']?>/admin/' + url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ webpage: cleanPath })
            })
            .then(res => res.text())
            .then(html => {
                $('.layout-content-prase').html(html);
        
                clearInterval(interval);
                document.querySelector('.progress').style.display = 'none';
        
                checkDevice();
        
                // Only push to history if not from popstate
                if (!fromPopState) {
                    history.pushState({ 
                        page: page, 
                        path: url, 
                        nav_id: nav_id 
                    }, "", url);
                }
            })
            .catch(error => {
                clearInterval(interval);
                document.querySelector('.progress').style.display = 'none';
                console.error('Error:', error);
            });
        
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            if (document.querySelector('.' + nav_id)) {
                document.querySelector('.' + nav_id).classList.add("active");
            }
        
            document.title = page + ' - PipraPay';
        }
        
        window.addEventListener("popstate", function (event) {
            if (event.state) {
                load_content(event.state.page, event.state.path, event.state.nav_id, true);
            }
        });
    
        <?php
            if(isset($_GET['name'])){
        ?>
                load_content('Welcome','<?php echo extractPathAndQuery(getCurrentUrl());?>','nav-btn-<?php echo $_GET['name']?>');
        <?php
            }else{
        ?>
                load_content('Dashboard','dashboard','nav-btn-dashboard');
        <?php
            }
        ?>
    </script>
</body>
</html>
