<?php
    $transaction_details = pp_get_transation($payment_id);
    $setting = pp_get_settings();
    $faq_list = pp_get_faq();
    $support_links = pp_get_support_links();

    $plugin_slug = 'binance-merchant-api';
    $plugin_info = pp_get_plugin_info($plugin_slug);
    $settings = pp_get_plugin_setting($plugin_slug);
    
    $transaction_amount = convertToDefault($transaction_details['response'][0]['transaction_amount'], $transaction_details['response'][0]['transaction_currency'], $settings['currency']);
    $transaction_fee = safeNumber($settings['fixed_charge']) + ($transaction_amount * (safeNumber($settings['percent_charge']) / 100));
    $transaction_amount = $transaction_amount+$transaction_fee;
    $binanceCheckoutData = null;
    $binanceOrderParam = 'binance_order';

    if (!function_exists('pp_binance_generate_signature')) {
        /**
         * Generate Binance Pay signature headers.
         */
        function pp_binance_generate_signature($payload, $apiSecret) {
            $timestamp = round(microtime(true) * 1000);
            $nonce = bin2hex(random_bytes(16));
            $message = $timestamp . "\n" . $nonce . "\n" . $payload . "\n";
            $rawSignature = hash_hmac('SHA512', $message, $apiSecret, true);
            $signature = strtoupper(bin2hex($rawSignature));

            return [$timestamp, $nonce, $signature];
        }
    }

    if (!function_exists('pp_binance_generate_trade_no')) {
        /**
         * Generate a Binance compliant merchantTradeNo (<=32 chars, letters/digits only).
         */
        function pp_binance_generate_trade_no() {
            return strtoupper(bin2hex(random_bytes(16))); // 32 hex characters
        }
    }

    if (!function_exists('pp_remove_query_parameter')) {
        /**
         * Remove a single query parameter from the current URL while keeping everything else intact.
         */
        function pp_remove_query_parameter($url, $parameter) {
            $parts = parse_url($url);
            if ($parts === false) {
                return $url;
            }

            $query = [];
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query);
                unset($query[$parameter]);
            }

            $scheme   = $parts['scheme'] ?? 'https';
            $host     = $parts['host'] ?? ($_SERVER['HTTP_HOST'] ?? '');
            $port     = isset($parts['port']) ? ':' . $parts['port'] : '';
            $path     = $parts['path'] ?? '';
            $rebuilt  = $scheme . '://' . $host . $port . $path;
            $newQuery = http_build_query($query);

            if (!empty($newQuery)) {
                $rebuilt .= '?' . $newQuery;
            }

            if (!empty($parts['fragment'])) {
                $rebuilt .= '#' . $parts['fragment'];
            }

            return $rebuilt;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['display_name']?> - <?php echo $setting['response'][0]['site_name']?></title>
    <link rel="icon" type="image/x-icon" href="<?php if(isset($setting['response'][0]['favicon'])){if($setting['response'][0]['favicon'] == "--"){echo 'https://cdn.piprapay.com/media/favicon.png';}else{echo $setting['response'][0]['favicon'];};}else{echo 'https://cdn.piprapay.com/media/favicon.png';}?>">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        :root {
            --secondary: #00cec9;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
            --dark: #2d3436;
            --light: #f5f6fa;
            --gray: #636e72;
            --border: #dfe6e9;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .accordion-button:not(.collapsed) {
            color: var(--bs-accordion-active-color);
            background-color: transparent;
            box-shadow: inset 0 calc(-1 * transparent) 0 transparent;
        }
        
        .payment-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .payment-header {
            display: flex;
            background: var(--light);
            border-radius: 8px;
            padding: 1rem;
            align-items: center;
            margin-top: 1.5rem;
            margin-left: 1.5rem;
            color: <?php echo $setting['response'][0]['global_text_color']?>;
            margin-right: 1.5rem;
            justify-content: space-between;
        }
        
        .payment-logo {
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .payment-logo img {
            height: 30px;
        }
        
        .merchant-info {
            margin-top: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .payment-body {
            padding: 1.5rem;
        }
        
        /* Updated Payment Amount Section */
        .payment-amount {
            display: flex;
            background: var(--light);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
            position: relative;
        }
        
        .merchant-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
            background: white;
            padding: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .merchant-details {
            flex: 1;
        }
        
        .merchant-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .amount-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: <?php echo $setting['response'][0]['global_text_color']?>;
        }
        
        .amount-label {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .payment-actions {
            display: flex;
            gap: 0.5rem;
            position: absolute;
            right: 1rem;
            bottom: 1rem;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            border: none;
            color: var(--gray);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .action-btn:hover {
            background: <?php echo $setting['response'][0]['active_tab_color']?>;
            color: <?php echo $setting['response'][0]['active_tab_text_color']?>;
            transform: translateY(-2px);
        }
        
        .action-btn i {
            font-size: 0.8rem;
        }
        
        .method-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            margin-bottom: 1rem;
        }
        
        .method-tab {
            padding: 0.75rem 1rem;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        
        .method-tab.active {
            border-bottom-color: <?php echo $setting['response'][0]['active_tab_color']?>;
            color: <?php echo $setting['response'][0]['active_tab_color']?>;
        }
        
        .method-content {
            display: none;
        }
        
        .method-content.active {
            display: block;
        }
        
        .card-form .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: <?php echo $setting['response'][0]['global_text_color']?>;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
            outline: none;
        }
        
        .card-icons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .card-icon {
            width: 40px;
            height: 25px;
            object-fit: contain;
            opacity: 0.3;
            transition: opacity 0.2s;
        }
        
        .card-icon.active {
            opacity: 1;
        }
        
        .row {
            display: flex;
            gap: 1rem;
        }
        
        .col {
            flex: 1;
        }
        
        .btn-pay {
            width: 100%;
            padding: 1rem;
            background: <?php echo $setting['response'][0]['primary_button_color']?>;
            color: <?php echo $setting['response'][0]['button_text_color']?>;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-pay:hover {
            background: <?php echo $setting['response'][0]['button_hover_color']?>;
            color: <?php echo $setting['response'][0]['button_hover_text_color']?>;
            transform: translateY(-1px);
        }
        
        .btn-pay:active {
            transform: translateY(0);
        }
        
        .upi-form {
            text-align: center;
        }
        
        .upi-id {
            background: var(--light);
            border-radius: 8px;
            padding: 1rem;
            font-weight: 500;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 1rem auto;
            background: #f0f0f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray);
        }
        
        .netbanking-form select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        .payment-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            color: var(--gray);
            text-align: center;
        }
        
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .processing {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: <?php echo $setting['response'][0]['global_text_color']?>;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @media (max-width: 576px) {
            .payment-container {
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
            }
            
            .payment-amount {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .merchant-logo {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .payment-actions {
                position: static;
                margin-top: 1rem;
                align-self: flex-end;
            }
        }
                
        .custom-contact-grid {
          display: flex;
          flex-wrap: wrap;
          gap: 16px;
        }
        .contact-box {
          flex: 1 1 calc(50% - 8px);
          text-decoration: none;
        }
        .contact-inner {
          display: flex;
          align-items: center;
          padding: 16px;
          background: #f8f9fa;
          border-radius: 12px;
          box-shadow: 0 2px 5px rgba(0,0,0,0.05);
          transition: 0.3s;
        }
        .contact-inner:hover {
          background-color: #e2e6ea;
        }
        .contact-inner img {
          width: 28px;
          height: 28px;
          margin-right: 12px;
        }
        .contact-inner span {
          font-size: 14px;
          color: #212529;
        }
        @media (max-width: 767px) {
          .contact-box {
            flex: 1 1 100%;
          }
        }
        .list-unstyled{
            border: 1px solid #dddddd;
            border-radius: 8px;
            padding: 19px;
        }
        .list-unstyled li{
            height: 40px;
            font-size: 15px;
            align-items: center;
        }
        .list-unstyled li button{
            font-size: 10px;
        }
        
        .bg-primary{
            background-color: <?php echo hexToRgba($setting['response'][0]['global_text_color'], 0.1);?> !important;
        }
        .text-primary{
            color: <?php echo $setting['response'][0]['global_text_color']?> !important;
        }
        
        .btn-primary{
            background-color: <?php echo $setting['response'][0]['primary_button_color'];?> !important;
            border: 1px solid <?php echo $setting['response'][0]['primary_button_color'];?> !important;
            color: <?php echo $setting['response'][0]['button_text_color'];?> !important;
        }

        .binance-checkout-card{
            margin-top: 1.5rem;
            padding: 1.5rem;
            border: 1px dashed var(--border);
            border-radius: 12px;
            text-align: center;
            background: #ffffff;
        }

        .binance-checkout-card h4{
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .binance-qr-wrapper{
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .binance-qr-wrapper img{
            width: 220px;
            height: 220px;
            object-fit: cover;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px;
            background: #fff;
        }

        .binance-pay-btn{
            margin-top: 1rem;
            min-width: 220px;
            border-radius: 30px;
            padding: 0.9rem 1.5rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <i class="fas fa-arrow-left" style=" cursor: pointer; " onclick="location.href='<?php echo pp_get_paymentlink($payment_id)?>'"></i>
        </div>
        
        <div class="payment-body">
            <!-- Updated Payment Amount Section -->
            <center><img src="<?php echo pp_get_site_url().'/pp-content/plugins/'.$plugin_info['plugin_dir'].'/'.$plugin_slug.'/assets/icon.png';?>" style=" height: 50px; margin-bottom: 20px; "></center>

            <div class="payment-amount">
                <img src="<?php if(isset($setting['response'][0]['favicon'])){if($setting['response'][0]['favicon'] == "--"){echo 'https://cdn.piprapay.com/media/favicon.png';}else{echo $setting['response'][0]['favicon'];};}else{echo 'https://cdn.piprapay.com/media/favicon.png';}?>" alt="Merchant Logo" class="merchant-logo">
                <div class="merchant-details">
                    <div class="merchant-name"><?php echo $setting['response'][0]['site_name']?></div>
                    <div class="amount-value"><?php echo number_format($transaction_amount,2).' '.$settings['currency']?></div>
                </div>
            </div>
            
            <div class="payment-form">
                  <?php
                        $sessionId = $_GET[$binanceOrderParam] ?? ($_GET['session_id'] ?? null);
                        $statusFlag = $_GET['status'] ?? null;

                        if(!empty($sessionId)){
                                
                                $apiKey = $settings['merchant_api_key'];
                                $apiSecret = $settings['merchant_secret_key'];
                                $merchantTradeNo = $sessionId;
                                
                                // Step 1: Prepare payload
                                $payload = json_encode(['merchantTradeNo' => $merchantTradeNo], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                [$timestamp, $nonce, $signature] = pp_binance_generate_signature($payload, $apiSecret);
                                
                                // Step 2: Headers
                                $headers = [
                                    "Content-Type: application/json",
                                    "BinancePay-Timestamp: $timestamp",
                                    "BinancePay-Nonce: $nonce",
                                    "BinancePay-Certificate-SN: $apiKey",
                                    "BinancePay-Signature: $signature"
                                ];
                                
                                // Step 3: Send query request
                                $ch = curl_init("https://bpay.binanceapi.com/binancepay/openapi/v2/order/query");
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                                $response = curl_exec($ch);
                                $curlError = $response === false ? curl_error($ch) : null;
                                curl_close($ch);
                                
                                $data = json_decode($response, true);
                                
                                // Step 4: Check result
                                if ($data && $data['status'] === 'SUCCESS') {
                                    $orderData = $data['data'] ?? [];
                                    $bizStatus = $orderData['status'] ?? null; // PAY_SUCCESS or others
                                    if ($bizStatus === 'PAID' || $bizStatus === 'PAY_SUCCESS') {
                                        $referenceGoodsId = $orderData['goods']['referenceGoodsId'] ?? null;

                                        if (!$referenceGoodsId && !empty($orderData['passThroughInfo'])) {
                                            $decodedPassThrough = json_decode($orderData['passThroughInfo'], true);
                                            if (json_last_error() === JSON_ERROR_NONE && isset($decodedPassThrough['payment_id'])) {
                                                $referenceGoodsId = $decodedPassThrough['payment_id'];
                                            }
                                        }

                                        if (!$referenceGoodsId) {
                                            $referenceGoodsId = $payment_id;
                                        }
                                        
                                        $transactionId = $orderData['transactionId'] ?? $merchantTradeNo;
                                        $payerAccount = $orderData['paymentInfo']['payerId'] ?? $orderData['payerAccount'] ?? 'Unknown';

                                        $check_transactionid = pp_check_transaction_exits($transactionId);
                                        if($check_transactionid['status'] == false){
                                            if(pp_set_transaction_byid($referenceGoodsId, $plugin_slug, $plugin_info['plugin_name'], $payerAccount, $transactionId, 'completed')){
                                                $cleanRedirect = pp_remove_query_parameter(getCurrentUrl(), $binanceOrderParam);
                                                $cleanRedirect = pp_remove_query_parameter($cleanRedirect, 'session_id');
                                                if (empty($cleanRedirect)) {
                                                    $cleanRedirect = pp_get_paymentlink($referenceGoodsId);
                                                }
                                                $cleanRedirect = htmlspecialchars($cleanRedirect, ENT_QUOTES, 'UTF-8');
                                                echo '<script>location.href="'.$cleanRedirect.'";</script>';
                                            }
                                        }else{
                   ?>
                                            <div class="alert alert-danger" role="alert">
                                              Transaction ID already exits
                                            </div>
                   <?php
                                        }
                                    } else {
                   ?>
                                    <div class="alert alert-danger" role="alert">
                                      Transaction not completed yet <?php echo htmlspecialchars($bizStatus ?? 'UNKNOWN', ENT_QUOTES, 'UTF-8')?>
                                    </div>
                   <?php
                                    }
                                } else {
                                    $errorMessage = $curlError ?: ($data['errorMessage'] ?? 'Unable to verify transaction at this time.');
                                    $errorCode = $data['code'] ?? 'N/A';
                   ?>
                                    <div class="alert alert-danger" role="alert">
                                      Transaction verification failed (<?php echo htmlspecialchars($errorCode, ENT_QUOTES, 'UTF-8')?>): <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8')?>
                                    </div>
                   <?php
                                }
                        }elseif(!empty($statusFlag)){
                   ?>
                                <div class="alert alert-danger" role="alert">
                                  Transaction <?php echo htmlspecialchars($statusFlag, ENT_QUOTES, 'UTF-8')?>
                                </div>
                   <?php
                        }else{
                            $separator = (strpos(getCurrentUrl(), '?') !== false) ? '&' : '?';
                                                        
                            $apiKey = $settings['merchant_api_key'];
                            $apiSecret = $settings['merchant_secret_key'];
                            $url = 'https://bpay.binanceapi.com/binancepay/openapi/v3/order';

                            // Generate unique order ID (32 alphanumeric characters as required by Binance)
                            $merchantTradeNo = pp_binance_generate_trade_no();

                            $allowedTerminalTypes = ['APP', 'WEB', 'WAP', 'MINI_PROGRAM', 'OTHERS'];
                            $terminalType = strtoupper($settings['terminal_type'] ?? 'WEB');
                            if(!in_array($terminalType, $allowedTerminalTypes, true)){
                                $terminalType = 'WEB';
                            }

                            $sanitizeGoodsField = function ($value, $fallback, $maxLength = 120) {
                                $value = trim((string)$value);
                                if ($value === '') {
                                    $value = $fallback;
                                }
                                $value = preg_replace('/["\\\\]/', '', $value);
                                $value = preg_replace('/[\r\n]+/', ' ', $value);
                                if(strlen($value) > $maxLength){
                                    $value = substr($value, 0, $maxLength);
                                }
                                return $value;
                            };

                            $orderReference = $transaction_details['response'][0]['pp_id'] ?? $payment_id;
                            $siteName = $setting['response'][0]['site_name'] ?? 'PipraPay';
                            $goodsName = $sanitizeGoodsField($settings['display_name'] ?? $plugin_info['plugin_name'], 'Digital Goods', 120);
                            $goodsDetail = $sanitizeGoodsField('Payment '.$orderReference.' via '.$siteName, $goodsName, 256);
                            $passThroughPayload = json_encode([
                                'payment_id' => $payment_id,
                                'merchantTradeNo' => $merchantTradeNo
                            ], JSON_UNESCAPED_SLASHES);
                            
                            // Order Data
                            $orderData = [
                                'env' => [
                                    'terminalType' => $terminalType
                                ],
                                'merchantTradeNo' => $merchantTradeNo,
                                'orderAmount' => (float)number_format((float)$transaction_amount, 8, '.', ''),
                                'currency' => $settings['payment_currency'],
                                'description' => $goodsDetail,
                                'goodsDetails' => [
                                    [
                                        'goodsType' => '02', // Virtual goods
                                        'goodsCategory' => 'Z000',
                                        'referenceGoodsId' => $payment_id,
                                        'goodsName' => $goodsName,
                                        'goodsDetail' => $goodsDetail
                                    ]
                                ],
                                'returnUrl' => getCurrentUrl() . $separator . $binanceOrderParam . "=" . rawurlencode($merchantTradeNo),
                                'cancelUrl' => getCurrentUrl() . $separator . "status=cancel",
                                'passThroughInfo' => $passThroughPayload
                            ];

                            if (!empty($settings['sub_merchant_id'])) {
                                $orderData['merchant'] = [
                                    'subMerchantId' => $settings['sub_merchant_id']
                                ];
                            }
                            
                            // Convert payload to JSON
                            $payload = json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            
                            // Signature Generation
                            [$timestamp, $nonce, $signature] = pp_binance_generate_signature($payload, $apiSecret);
                            
                            // Headers
                            $headers = [
                                "Content-Type: application/json",
                                "BinancePay-Timestamp: $timestamp",
                                "BinancePay-Nonce: $nonce",
                                "BinancePay-Certificate-SN: $apiKey",
                                "BinancePay-Signature: $signature"
                            ];
                            
                            // Send cURL Request
                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                            $response = curl_exec($ch);
                            $curlError = $response === false ? curl_error($ch) : null;
                            curl_close($ch);
                            
                            // Handle Response
                            $data = json_decode($response, true);
                            
                            if ($data && $data['status'] === 'SUCCESS') {
                                $binanceCheckoutData = [
                                    'checkoutUrl' => $data['data']['checkoutUrl'] ?? '',
                                    'deeplink' => $data['data']['deeplink'] ?? '',
                                    'universalUrl' => $data['data']['universalUrl'] ?? '',
                                    'qrcodeLink' => $data['data']['qrcodeLink'] ?? '',
                                    'qrContent' => $data['data']['qrContent'] ?? ''
                                ];
                            } else {
                                $errorMessage = $curlError ?: ($data['errorMessage'] ?? $response);
                                $errorCode = $data['code'] ?? '';
                  ?>
                                <div class="alert alert-danger" role="alert">
                                   Binance Pay error <?php echo htmlspecialchars($errorCode, ENT_QUOTES, 'UTF-8')?>: <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8')?>
                                </div>
                  <?php
                            }
                        }
                  ?>
            </div>
            <?php if (!empty($binanceCheckoutData)): ?>
                <div class="binance-checkout-card">
                    <h4>Complete your Binance Pay order</h4>
                    <p class="text-muted">Scan the QR code or tap the button below to open Binance Pay.</p>
                    <div class="binance-qr-wrapper">
                        <?php if (!empty($binanceCheckoutData['qrcodeLink'])): ?>
                            <img src="<?php echo htmlspecialchars($binanceCheckoutData['qrcodeLink'], ENT_QUOTES, 'UTF-8'); ?>" alt="Binance Pay QR">
                        <?php endif; ?>
                    </div>
                    <button type="button"
                            class="btn btn-primary binance-pay-btn"
                            id="binancePayCTA"
                            data-checkout="<?php echo htmlspecialchars($binanceCheckoutData['checkoutUrl'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-deeplink="<?php echo htmlspecialchars($binanceCheckoutData['deeplink'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            data-universal="<?php echo htmlspecialchars($binanceCheckoutData['universalUrl'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fa fa-mobile-alt"></i>
                        Continue in Binance Pay
                    </button>
                    <p class="text-muted" style="margin-top:0.75rem;font-size:0.85rem;">
                        We will try to open the Binance app when available, otherwise you will be redirected to the secure web checkout.
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="payment-footer">
            <div>Your payment is secured with 256-bit encryption</div>
            <div class="secure-badge">
                <span>Powered by <a href="https://piprapay.com/" target="blank" style="color: <?php echo $setting['response'][0]['global_text_color']?>; text-decoration: none"><strong style="cursor: pointer">PipraPay</strong></a></span>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var payBtn = document.getElementById('binancePayCTA');
            if (!payBtn) {
                return;
            }

            payBtn.addEventListener('click', function () {
                var checkoutUrl = this.dataset.checkout || '';
                var deepLink = this.dataset.deeplink || '';
                var universalUrl = this.dataset.universal || '';
                var isMobile = /android|iphone|ipad|ipod/i.test(navigator.userAgent);

                if (isMobile && universalUrl) {
                    window.location.href = universalUrl;
                    return;
                }

                if (isMobile && deepLink) {
                    window.location.href = deepLink;
                    if (checkoutUrl) {
                        setTimeout(function () {
                            window.location.href = checkoutUrl;
                        }, 1200);
                    }
                    return;
                }

                if (checkoutUrl) {
                    window.location.href = checkoutUrl;
                } else if (universalUrl) {
                    window.location.href = universalUrl;
                }
            });
        });
    </script>
</body>
</html>
