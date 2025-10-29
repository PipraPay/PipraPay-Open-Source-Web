<?php
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }
    
    $theme_slug = 'vercel';
    $settings = pp_get_theme_setting($theme_slug);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - <?php echo $setting['response'][0]['site_name']?></title>
    <link rel="icon" type="image/x-icon" href="<?php if(isset($setting['response'][0]['favicon'])){if($setting['response'][0]['favicon'] == "--"){echo 'https://cdn.piprapay.com/media/favicon.png';}else{echo $setting['response'][0]['favicon'];};}else{echo 'https://cdn.piprapay.com/media/favicon.png';}?>">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --stripe-blue: #635bff;
            --stripe-blue-dark: #4a42d4;
            --stripe-text: #1a1a1a;
            --stripe-light-text: #6b7c93;
            --stripe-border: #e0e6ed;
            --stripe-light-bg: #f6f9fc;
            --stripe-success: #24b47e;
            --stripe-error: #ff5252;
            --stripe-step-active: #635bff;
            --stripe-step-completed: #24b47e;
            --stripe-step-pending: #e0e6ed;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--stripe-text);
            background-color: var(--stripe-light-bg);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        
        .payment-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid var(--stripe-border);
        }
        
        
        @keyframes bounceIn {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 12px;
        }
        
        .detail-label {
            font-weight: 500;
            min-width: 120px;
            color: var(--stripe-light-text);
            font-size: 14px;
        }
        
        .detail-value {
            font-weight: 500;
            flex: 1;
        }
        
        .btn-done {
            background: var(--stripe-blue);
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 16px;
        }
        
        .btn-done:hover {
            background: var(--stripe-blue-dark);
        }
        
        .btn-print {
            background: white;
            color: var(--stripe-blue);
            border: 1px solid var(--stripe-blue);
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 12px;
        }
        
        .btn-print:hover {
            background: var(--stripe-light-bg);
        }
        
        /* Failure page styles */
        .failure-page {
            text-align: center;
            padding: 40px 24px;
        }
        
        .failure-icon {
            width: 60px;
            height: 60px;
            background: var(--stripe-error);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
            animation: bounceIn 0.5s ease-in-out;
        }
        
        .failure-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--stripe-error);
        }
        
        .failure-message {
            color: var(--stripe-light-text);
            margin-bottom: 24px;
            font-size: 15px;
        }
        
        .failure-details {
            background: var(--stripe-light-bg);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }
        
        .btn-retry {
            background: var(--stripe-blue);
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 16px;
        }
        
        .btn-retry:hover {
            background: var(--stripe-blue-dark);
        }
        
        .btn-cancel {
            background: white;
            color: var(--stripe-error);
            border: 1px solid var(--stripe-error);
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 12px;
        }
        
        .btn-cancel:hover {
            background: rgba(255, 82, 82, 0.05);
        }
    </style>
</head>
<body>
    <!-- Failure Page (shown when payment fails or is canceled) -->
    <div class="payment-container" id="failure-page">
        <div class="failure-page">
            <div class="failure-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1 class="failure-title">Transaction Failed</h1>
            <p class="failure-message" id="failure-message">You have cancelled this Transaction.</p>
            
            <?php
                if(isset($settings['auto_redirect']) && $settings['auto_redirect'] == "Enable"){
            ?>
                   <p class="countdown-message">Redirecting in <span id="countdown">4</span> seconds...</p>
            <?php
                }
            ?>
            
            <div class="failure-details">
                <div class="detail-row">
                    <div class="detail-label">Error Code</div>
                    <div class="detail-value">Failed</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Invoice ID</div>
                    <div class="detail-value" id="failure-date"><?php echo $transaction_details['response'][0]['pp_id']?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Amount</div>
                    <div class="detail-value"><?php echo number_format($transaction_details['response'][0]['transaction_amount']+$transaction_details['response'][0]['transaction_fee'], 2).$transaction_details['response'][0]['transaction_currency']?></div>
                </div>
            </div>
            
            <?php
                if($transaction_details['response'][0]['transaction_cancel_url'] == "" || $transaction_details['response'][0]['transaction_cancel_url'] == "--"){
                    
                }else{
            ?>
                    <a href="<?php echo $transaction_details['response'][0]['transaction_cancel_url']?>">
                        <button class="btn-cancel" id="cancel-button">
                            <i class="fas fa-times"></i> Back to website
                        </button>
                    </a>
            <?php
                }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php
        if(isset($settings['auto_redirect']) && $settings['auto_redirect'] == "Enable"){
    ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Countdown functionality
                    let countdown = 4;
                    const countdownElement = document.getElementById('countdown');
                    
                    // Update countdown every second
                    const countdownInterval = setInterval(function() {
                        countdown--;
                        countdownElement.textContent = countdown;
                        
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            document.getElementById('cancel-button').click();
                        }
                    }, 1000);
                });
            </script>
    <?php
        }
    ?>
</body>
</html>