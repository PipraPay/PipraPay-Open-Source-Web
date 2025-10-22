<?php
    define('pp_allowed_access', true);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require __DIR__ . '/../pp-external/phpmailer/autoload.php';
    require __DIR__ . '/../pp-external/pdf-generator/autoload.php';
    
    if (file_exists(__DIR__ . '/../pp-config.php')) {
        require __DIR__ . '/../pp-config.php';
    }else{
        echo 'System is under maintenance. Please try again later.';
        exit();
    }
    
    use Dompdf\Dompdf;

    function connectDatabase() {
        global $db_host, $db_user, $db_pass, $db_name;
    
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }
    
        return $conn;
    }
    
    function hexToRgba($hex, $opacity = 1.0) {
        // Remove "#" if present
        $hex = ltrim($hex, '#');
    
        // Convert shorthand to full form (e.g., #F53 to #FF5533)
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] .
                   $hex[1] . $hex[1] .
                   $hex[2] . $hex[2];
        }
    
        // Extract RGB values
        if (strlen($hex) != 6) {
            return 'Invalid hex color';
        }
    
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    
        // Clamp opacity between 0 and 1
        $opacity = max(0, min(1, $opacity));
    
        return "rgba($r, $g, $b, $opacity)";
    }

    $date_default_timezone_set = json_decode(getData($db_prefix.'settings', 'WHERE id="1"'), true);
    date_default_timezone_set($date_default_timezone_set['response'][0]['default_timezone']);

    function timeAgo($datetime) {
        $past = new DateTime($datetime);
        $now = new DateTime();
        $diff = $now->diff($past);
    
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        } elseif ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        } elseif ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }

    function convertToReadableDate($inputDate) {
        // Try to parse with DateTime automatically (ISO format)
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $inputDate);
    
        // If that fails, try common alternative format (d/m/Y)
        if (!$date) {
            $date = DateTime::createFromFormat('d/m/Y', $inputDate);
        }
    
        // If still not valid, return an error message
        if (!$date) {
            return 'Invalid date format';
        }
    
        // Format as: June 11, 2025
        return $date->format('F j, Y');
    }

    function getConnectionStatus($lastdate, $maximumtime) {
        $past = new DateTime($lastdate);
        $now = new DateTime();
        $diffInMinutes = ($now->getTimestamp() - $past->getTimestamp()) / 60;
    
        return $diffInMinutes <= $maximumtime;
    }

    function isNumber($value) {
        return is_numeric($value);
    }

    function safeNumber($value, $default = 0) {
        return (isset($value) && is_numeric($value)) ? (float)$value : $default;
    }

    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    function removeBaseUrl($base_url) {
        $current_url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    
        $modified_url = str_replace($base_url, "", $current_url);
    
        return $modified_url;
    }

    function getAuthorizationHeader() {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['mh-piprapay-api-key'])) {
                return trim($headers['mh-piprapay-api-key']);
            }
        }
    
        // Fallback for environments where getallheaders() doesn't work
        foreach ($_SERVER as $key => $value) {
            if (stripos($key, 'HTTP_MH_PIPRAPAY_API_KEY') !== false) {
                return trim($value);
            }
        }
    
        return null;
    }

    function getCurrentDatetime($format = 'Y-m-d H:i:s') {
        $currentDatetime = new DateTime();

        return $currentDatetime->format($format);
    }    

function convertDateTime($datetime, $daysToSubtract = 0, $newHour = null, $newMinute = null) {
    try {
        $date = new DateTime($datetime);
    } catch (Exception $e) {
        return $datetime;
    }

    if ($daysToSubtract !== 0) {
        $date->modify(($daysToSubtract > 0 ? '-' : '+') . abs($daysToSubtract) . ' days');
    }

    if ($newHour !== null && $newMinute !== null) {
        $date->setTime((int)$newHour, (int)$newMinute);
    }

    return $date->format('Y-m-d h:i A');
}
    
    function extractPathAndQuery($url) {
        $parsedUrl = parse_url($url);
    
        // If no path, return null or empty
        if (!isset($parsedUrl['path'])) {
            return null;
        }
    
        // Remove "/admin/" from path if present
        $path = str_replace("/admin/", "", $parsedUrl['path']);
    
        // Append query if it exists
        if (isset($parsedUrl['query'])) {
            return $path . '?' . $parsedUrl['query'];
        }
    
        return $path;
    }

    function getCurrentUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                     $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
        $host = $_SERVER['HTTP_HOST'];         // e.g., dev.builderpay.com
        $requestUri = $_SERVER['REQUEST_URI']; // e.g., /admin/view-transaction?p=13
    
        return $protocol . $host . $requestUri;
    }

    function maskEmail($email) {
        list($username, $domain) = explode('@', $email);
    
        $firstPart = substr($username, 0, 4);
        $lastPart = substr($username, -2);
    
        $maskedUsername = $firstPart . '****' . $lastPart;
    
        return $maskedUsername . '@' . $domain;
    }

    function maskTest($string) {
        return !empty($string) ? $string[0] : '';
    }
    
    function calculateDiscount($price, $discountPercentage) {
        $discountAmount = ($price * $discountPercentage) / 100;
        $finalPrice = $price - $discountAmount;
        
        return [
            'discountAmount' => $discountAmount,
            'finalPrice' => $finalPrice
        ];
    }
    
    function getUserDeviceInfo() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
        if (preg_match('/mobile/i', $userAgent)) {
            $deviceType = "Mobile";
        } elseif (preg_match('/tablet/i', $userAgent)) {
            $deviceType = "Tablet";
        } else {
            $deviceType = "Desktop";
        }
    
        if (preg_match('/Windows/i', $userAgent)) {
            $os = "Windows";
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = "Mac OS";
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = "Linux";
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = "Android";
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $os = "iOS";
        } else {
            $os = "Unknown OS";
        }
    
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = "Internet Explorer";
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = "Firefox";
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = "Chrome";
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = "Safari";
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = "Opera";
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = "Edge";
        } else {
            $browser = "Unknown Browser";
        }
    
        return [
            'ip_address' => $ipAddress,
            'device' => $deviceType,
            'os' => $os,
            'browser' => $browser
        ];
    }

    // Set a cookie securely (supports all panels)
    function setsCookie($cookieName, $cookieValue, $days = 365) {
        $expiryTime = time() + ($days * 24 * 60 * 60);
    
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    
        setcookie($cookieName, $cookieValue, [
            'expires' => $expiryTime,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax', // Use 'None' if cross-domain needed (and use HTTPS)
        ]);
    }
    
    // Check if a cookie exists
    function checkCookie($cookieName) {
        return isset($_COOKIE[$cookieName]);
    }
    
    // Get the value of a cookie
    function getCookie($cookieName) {
        return $_COOKIE[$cookieName] ?? null;
    }
    
    // Logout: clear all cookies and destroy session
    function logoutCookie() {
        // Expire all cookies
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    
        // Clear session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    function escape_string($value) {
        $conn = connectDatabase();
        $value = mysqli_real_escape_string($conn, $value);

        return $value;
    }   

    function getData($tableName, $coloum_name, $type = "* FROM") {
        $conn = connectDatabase();
    
        $sql = "SELECT $type `$tableName` $coloum_name";
        $result = $conn->query($sql);
    
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return json_encode(['status' => true, 'response' => $data]);
        } else {
            return json_encode(['status' => false, 'response' => []]);
        }
    
        $conn->close();
    }

    function insertData($tableName, $columns, $values) {
        $conn = connectDatabase();

        $columnsString = implode(', ', $columns);
        $escapedValues = array_map('addslashes', $values);
        $valuesString = "'" . implode("', '", $escapedValues) . "'";

        $sql = "INSERT INTO `$tableName` ($columnsString) VALUES ($valuesString)";
        if($conn->query($sql)){
            return true;
        }else{
            return false;
        }

        $conn->close();
    }

    function updateData($tableName, $columns, $values, $condition) {
        $conn = connectDatabase();
    
        $setClauses = [];
        for ($i = 0; $i < count($columns); $i++) {
            $setClauses[] = "$columns[$i] = '" . addslashes($values[$i]) . "'";
        }
        $setString = implode(", ", $setClauses);
    
        $sql = "UPDATE `$tableName` SET $setString WHERE $condition";
    
        if ($conn->query($sql)) {
            return true; 
        } else {
            return false; 
        }
    
        $conn->close();
    }

    function deleteData($tableName, $condition) {
        $conn = connectDatabase();
    
        $sql = "DELETE FROM `$tableName` WHERE $condition";
    
        if ($conn->query($sql)) {
            return true; 
        } else {
            return false; 
        }
    
        $conn->close();
    }
    
    function convertToDefault($amount, $sourceCurrency, $targetCurrency) {
        $conn = connectDatabase();
        global $db_prefix;
        
        if ($sourceCurrency === $targetCurrency) {
            return $amount;
        }
    
        // Fetch source and target rates
        $sourceData = json_decode(getData($db_prefix.'currency', 'WHERE currency_code="'.$sourceCurrency.'"'), true);
        $targetData = json_decode(getData($db_prefix.'currency', 'WHERE currency_code="'.$targetCurrency.'"'), true);
    
        $sourceRate = floatval($sourceData['response'][0]['currency_rate']) ?: 1;
        $targetRate = floatval($targetData['response'][0]['currency_rate']) ?: 1;
    
        // Convert using universal formula
        return $amount * ($targetRate / $sourceRate);
    }
            
    function getDataChangeStats($table, $where = '', $dateColumn = 'created_at', $period = 'monthly') {
        if ($period === 'daily') {
            $current_start = date('Y-m-d');
            $prev_start = date('Y-m-d', strtotime('-1 day'));
            $next = date('Y-m-d', strtotime('+1 day'));
        } elseif ($period === 'monthly') {
            $current_start = date('Y-m-01');
            $prev_start = date('Y-m-01', strtotime('-1 month'));
            $next = date('Y-m-01', strtotime('+1 month'));
        } elseif ($period === 'yearly') {
            $current_start = date('Y-01-01');
            $prev_start = date('Y-01-01', strtotime('-1 year'));
            $next = date('Y-01-01', strtotime('+1 year'));
        } else {
            return ['status' => false, 'message' => 'Invalid period'];
        }
        
        if($where == ""){
            $ishaveand = "";
        }else{
            $ishaveand = "AND";
        }
    
        // Current
        $current_where = "$where $ishaveand `$dateColumn` >= '$current_start' AND `$dateColumn` < '$next'";
        $current = json_decode(getData($table, "WHERE $current_where"), true);
        $current_value = isset($current['response']['value']) ? (int)$current['response']['value'] : 0;
    
        // Previous
        $prev_end = $current_start;
        $prev_where = "$where $ishaveand `$dateColumn` >= '$prev_start' AND `$dateColumn` < '$prev_end'";
        $previous = json_decode(getData($table, "WHERE $prev_where"), true);
        $previous_value = isset($previous['response']['value']) ? (int)$previous['response']['value'] : 0;
    
        // Calculate
        $diff = $current_value - $previous_value;
        $percentage = ($previous_value > 0) ? round(($diff / $previous_value) * 100, 2) : 100;
        $direction = ($diff > 0) ? 'up' : (($diff < 0) ? 'down' : 'same');
    
        return [
            'status' => true,
            'current' => $current_value,
            'previous' => $previous_value,
            'change_percent' => $percentage,
            'direction' => $direction
        ];
    }

    function getChart($table, $dateColumn, $period = 'daily', $startDate = null, $endDate = null, $type = '') {
        $conn = connectDatabase();
        // Validate and sanitize inputs (basic)
        $allowedPeriods = ['today', 'daily', 'monthly', 'yearly', 'range'];
        if (!in_array($period, $allowedPeriods)) {
            return json_encode(['error' => 'Invalid period']);
        }
    
        // Prepare SQL parts
        $select = "";
        $groupBy = "";
        $where = "";
    
        // Build date filtering and grouping based on $period
        switch ($period) {
            case 'today':
                $today = date('Y-m-d');
                $where = "WHERE DATE($dateColumn) = '$today'";
                $select = "DATE($dateColumn) as label";
                $groupBy = "DATE($dateColumn)";
                break;
    
            case 'daily':
                // Last 30 days by default
                $startDate = $startDate ?? date('Y-m-d', strtotime('-29 days'));
                $endDate = $endDate ?? date('Y-m-d');
                $where = "WHERE DATE($dateColumn) BETWEEN '$startDate' AND '$endDate'";
                $select = "DATE($dateColumn) as label";
                $groupBy = "DATE($dateColumn)";
                break;
    
            case 'monthly':
                // Last 12 months by default
                $startDate = $startDate ?? date('Y-m-01', strtotime('-11 months'));
                $endDate = $endDate ?? date('Y-m-t');
                $where = "WHERE DATE($dateColumn) BETWEEN '$startDate' AND '$endDate'";
                $select = "DATE_FORMAT($dateColumn, '%Y-%m') as label";
                $groupBy = "DATE_FORMAT($dateColumn, '%Y-%m')";
                break;
    
            case 'yearly':
                // Last 5 years by default
                $startDate = $startDate ?? date('Y-01-01', strtotime('-4 years'));
                $endDate = $endDate ?? date('Y-12-31');
                $where = "WHERE DATE($dateColumn) BETWEEN '$startDate' AND '$endDate'";
                $select = "YEAR($dateColumn) as label";
                $groupBy = "YEAR($dateColumn)";
                break;
    
            case 'range':
                if (!$startDate || !$endDate) {
                    return json_encode(['error' => 'Start date and end date required for range']);
                }
                $where = "WHERE DATE($dateColumn) BETWEEN '$startDate' AND '$endDate'";
                $select = "DATE($dateColumn) as label";
                $groupBy = "DATE($dateColumn)";
                break;
        }
        
        if($type !== ""){
            if($where == ""){
                $where = ' WHERE '.$type;
            }else{
                $where .= ' AND '.$type;
            }
        }
        
        $labels = [];
        $data = [];
        
        $global_data_count = json_decode(getData($table, $where.' GROUP BY '.$groupBy.' ORDER BY '.$groupBy.' ASC', $select.', COUNT(*) as data FROM'), true);
        foreach($global_data_count['response'] as $row){
                $labels[] = $row['label'];
                $data[] = (int)$row['data'];
        }

        return json_encode(['labels' => $labels, 'data' => $data]);
    }
    
    function generateRandomFilename($extension) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        for ($i = 0; $i < 30; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString . "." . $extension;
    }
        
    function uploadImage($file, $max_file_size) {
        $upload_directory = __DIR__ . '/../pp-external/media/';
        
        // ─────────── VALIDATION ───────────
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return json_encode(['status' => false, 'message' => 'No file uploaded or upload failed.']);
        }
    
        if ($file['size'] > $max_file_size) {
            return json_encode(['status' => false, 'message' => 'File size exceeds maximum allowed.']);
        }
    
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_info          = pathinfo($file['name']);
        $file_extension     = strtolower($file_info['extension']);
    
        if (!in_array($file_extension, $allowed_extensions)) {
            return json_encode(['status' => false, 'message' => 'Only JPG, PNG, GIF, and WEBP files are allowed.']);
        }
    
        // ─────────── FILE NAME ───────────
        $random_filename = generateRandomFilename($file_extension);
        $full_path       = $upload_directory . $random_filename;
    
        // ─────────── TRY IMAGICK ───────────
        try {
            if (!extension_loaded('imagick')) {
                throw new Exception('Imagick extension not installed.');
            }
    
            $img = new Imagick($file['tmp_name']);
    
            $hasAlpha = $img->getImageAlphaChannel();
    
            if ($hasAlpha && Imagick::queryFormats('WEBP')) {
                $img->setImageFormat('webp');
                $img->setOption('webp:lossless', 'true');
                $img->setImageCompressionQuality(85);
                $random_filename = generateRandomFilename('webp');
            } elseif (!$hasAlpha && Imagick::queryFormats('JPEG')) {
                $img->setImageFormat('jpeg');
                $img->setImageCompression(Imagick::COMPRESSION_JPEG);
                $img->setImageCompressionQuality(75);
                $random_filename = generateRandomFilename('jpg');
            } else {
                throw new Exception('Required format not supported by Imagick.');
            }
    
            $full_path = $upload_directory . $random_filename;
    
            $img->stripImage();
            $img->writeImage($full_path);
            $img->clear();
            $img->destroy();
    
            return json_encode(['status' => true, 'file' => $random_filename]);
    
        } catch (Exception $e) {
            // ───── FALLBACK: MOVE FILE DIRECTLY ─────
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                return json_encode([
                    'status' => true,
                    'file'   => $random_filename,
                    'note'   => 'Imagick not used. File uploaded without processing.'
                ]);
            } else {
                return json_encode(['status' => false, 'message' => 'File upload failed without Imagick: ' . $e->getMessage()]);
            }
        }
    }
    
    function deleteImage($file) {
        // Define the local image directory path
        $upload_directory = __DIR__ . '/../pp-external/media/'; // Update path if different
    
        // Sanitize the filename to prevent directory traversal attacks
        $filename = basename($file);
        $full_path = $upload_directory . $filename;
    
        // Check if the file exists
        if (!file_exists($full_path)) {
            return json_encode(["status" => false, "message" => "File not found."]);
        }
    
        // Attempt to delete the file
        if (unlink($full_path)) {
            return json_encode(["status" => true, "message" => "File deleted successfully!"]);
        } else {
            return json_encode(["status" => false, "message" => "Error deleting file."]);
        }
    }

    
    function generatePDF($htmlContent, $paperSize = 'A4', $orientation = 'portrait') {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper($paperSize, $orientation);
        $dompdf->render();
    
        return $dompdf->output();
    }
    
    function parsePluginHeader($filePath) {
        $headers = [
            'Plugin Name', 'Plugin URI', 'Description', 'Version',
            'Author', 'Author URI', 'License', 'License URI',
            'Text Domain', 'Domain Path', 'Requires at least', 'Requires PHP'
        ];
    
        $pluginData = array_fill_keys($headers, '');
    
        if (!file_exists($filePath)) return $pluginData;
    
        // Isolate scope using include in function
        $plugin_meta = [];
        include $filePath;
    
        if (isset($plugin_meta) && is_array($plugin_meta)) {
            foreach ($headers as $key) {
                if (!empty($plugin_meta[$key])) {
                    $pluginData[$key] = $plugin_meta[$key];
                }
            }
        }
    
        return $pluginData;
    }
    
    function parseThemeHeader($filePath) {
        $headers = [
            'Theme Name', 'Theme URI', 'Description', 'Version',
            'Author', 'Author URI', 'License', 'License URI',
            'Text Domain', 'Domain Path', 'Requires at least', 'Requires PHP'
        ];
    
        // Initialize default structure
        $themeData = array_fill_keys($headers, '');
    
        if (!file_exists($filePath)) return $themeData;
    
        // Include file to access $theme_meta
        include $filePath;
    
        if (isset($theme_meta) && is_array($theme_meta)) {
            foreach ($headers as $key) {
                if (!empty($theme_meta[$key])) {
                    $themeData[$key] = $theme_meta[$key];
                }
            }
        }
    
        return $themeData;
    }

    function parse_readme_header($content) {
        $header = [];
    
        // Match plugin title from === Plugin Name ===
        if (preg_match('/^===\s*(.+?)\s*===/m', $content, $matches)) {
            $header['name'] = trim($matches[1]);
        }
    
        // Match other key-value lines (e.g. Contributors: John)
        if (preg_match_all('/^([^:\r\n]+):\s*(.+)$/m', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = strtolower(trim($match[1]));
                $value = trim($match[2]);
                $header[$key] = $value;
            }
        }
    
        return $header;
    }
        
    function parse_readme_sections($content) {
        $sections = [];
    
        // Normalize line endings
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
    
        // Split content into lines
        $lines = explode("\n", $content);
    
        $currentSection = 'header'; // before first section
        $sections[$currentSection] = '';
    
        foreach ($lines as $line) {
            // Detect section headers (like == Description ==, == Installation ==, etc.)
            if (preg_match('/^==\s*(.+?)\s*==$/', $line, $matches)) {
                $currentSection = strtolower(trim($matches[1]));
                $sections[$currentSection] = '';
                continue;
            }
    
            // Append line to current section
            $sections[$currentSection] .= $line . "\n";
        }
    
        // Trim whitespace from each section
        foreach ($sections as &$text) {
            $text = trim($text);
        }
    
        return $sections;
    }
    
    function parse_readme_changelog($changelogText) {
        $lines = explode("\n", trim($changelogText));
        $versions = [];
        $currentVersion = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Match version line like "= 1.0.0 ="
            if (preg_match('/^=\s*(.+?)\s*=+$/', $line, $matches)) {
                $currentVersion = $matches[1];
                $versions[$currentVersion] = [];
            } elseif (!empty($line) && $currentVersion) {
                // Each bullet point line (starts with "*")
                $line = ltrim($line, '* ');
                $versions[$currentVersion][] = $line;
            }
        }
        
        return $versions;
    }
        
    function deleteFolder($folderPath) {
        if (!is_dir($folderPath)) {
            return false;
        }
    
        $items = scandir($folderPath);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
    
            $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;
    
            if (is_dir($itemPath)) {
                deleteFolder($itemPath); // recursive delete
            } else {
                unlink($itemPath); // delete file
            }
        }
    
        return rmdir($folderPath); // finally delete the folder
    }

    $pp_hooks = [];
    
    function add_action($hook_name, $callback) {
        global $pp_hooks;
        $pp_hooks[$hook_name][] = $callback;
    }
    
    function do_action($hook_name, ...$args) {
        global $pp_hooks;
        if (!empty($pp_hooks[$hook_name])) {
            foreach ($pp_hooks[$hook_name] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    function pp_get_plugin_info($slug) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $slug = mysqli_real_escape_string($conn, $slug);
    
        $query = "SELECT plugin_name, plugin_slug, plugin_dir FROM " . $db_prefix . "plugins WHERE plugin_slug = '$slug'";
        $result = mysqli_query($conn, $query);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row; // <-- just return the associative array directly
        }
    
        return [];
    }
    
    function pp_get_plugin_setting($slug) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $slug = mysqli_real_escape_string($conn, $slug);
    
        $query = "SELECT plugin_array FROM ".$db_prefix."plugins WHERE plugin_slug = '$slug'";
        $result = mysqli_query($conn, $query);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return json_decode($row['plugin_array'], true);
        }
    
        return [];
    }
    
    function pp_set_plugin_setting($slug, $dataArray) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $slug = mysqli_real_escape_string($conn, $slug);
        $jsonData = mysqli_real_escape_string($conn, json_encode($dataArray));
    
        $query = "UPDATE ".$db_prefix."plugins SET plugin_array = '$jsonData' WHERE plugin_slug = '$slug'";
        return mysqli_query($conn, $query);
    }
    
    function pp_get_theme_setting($slug) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $slug = mysqli_real_escape_string($conn, $slug);
        
        $jsonPath = __DIR__ . '/../pp-content/themes/gateway/'.$slug.'/config.json';
    
        if (file_exists($jsonPath)) {
            $jsonData = file_get_contents($jsonPath);
        
            return json_decode($jsonData, true);
        } else {
            return [];
        }
    }
    
    function pp_set_theme_setting($slug, $dataArray) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $slug = mysqli_real_escape_string($conn, $slug);

        $jsonPath = __DIR__ . '/../pp-content/themes/gateway/'.$slug.'/config.json';
        
        // Encode PHP array to JSON string
        $jsonString = json_encode($dataArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
        // Save JSON to file
        if (file_put_contents($jsonPath, $jsonString)) {
            return true; // success
        } else {
            return false; // failed to write
        }
    }
    
    function pp_trigger_hook($hook_name, ...$args) {
        $conn = connectDatabase();
        global $db_prefix;
    
        $response = json_decode(getData($db_prefix.'plugins','WHERE status="active"'),true);
        if($response['status'] == true){
            foreach($response['response'] as $row){
                $slug = $row['plugin_slug'];
                $pluginFunctions = __DIR__ . "/../pp-content/plugins/{$row['plugin_dir']}/{$slug}/functions.php";
                if (file_exists($pluginFunctions)) {
                    include_once $pluginFunctions;
                }
            }
        }
    
        // trigger all callbacks added to this hook
        do_action($hook_name, ...$args);
    
        // also support your dynamic function style fallback
        if($response['status'] == true){
            foreach($response['response'] as $row){
                $slug = $row['plugin_slug'];
                $functionName = $hook_name . '_' . str_replace('-', '_', $slug);
                if (function_exists($functionName)) {
                    call_user_func_array($functionName, $args);
                }
            }
        }
    }

    function get_theme_path($maindr, $themes, $file) {
        return __DIR__ . "/../pp-content/themes/".$maindr."/".$themes ."/" . $file;
    }
    
    function get_theme_url($file = '') {
        return __DIR__ . "/../pp-content/themes/".$maindr."/".$themes. "/" . $file;
    }
    
    function theme_include($maindr, $themes, $file, $varValue, $varName = 'paymentid') {
        $$varName = $varValue;
        
        $path = get_theme_path($maindr, $themes, $file);
        if (file_exists($path)) {
            include $path;
        } else {
            echo "<!-- Theme file '$file' not found -->";
        }
    }

    function payment_gateway_include($slug, $payment_id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $res_plugins = json_decode(getData($db_prefix.'plugins', 'WHERE plugin_slug="'.$slug.'" AND status="active"'), true);
        
        if($res_plugins['status'] == true){
            $path = __DIR__ . "/../pp-content/plugins/".$res_plugins['response'][0]['plugin_dir']."/".$res_plugins['response'][0]['plugin_slug']. "/" . $res_plugins['response'][0]['plugin_slug'].'-class.php';
            if (file_exists($path)) {
                include $path;
                
                $function = str_replace('-', '_', $res_plugins['response'][0]['plugin_slug']) . '_checkout_page';
                if (function_exists($function)) {
                    call_user_func($function, $payment_id);
                }
            } else {
                echo "<!-- Theme file '$file' not found -->";
            }
        }else{
            echo "<!-- Theme file '$file' not found -->";
        }
    }






    function pp_get_site_url(){
        return 'https://'.$_SERVER['HTTP_HOST'];
    }

    function pp_get_settings() {
        $conn = connectDatabase();
        global $db_prefix;
        
        return json_decode(getData($db_prefix.'settings','WHERE id="1"'),true);
    }
    
    function pp_get_admin($admin_id = 0) {
        $conn = connectDatabase();
        global $db_prefix;
        
        if($admin_id == 0){
            $sql = "";
        }else{
            $sql = "WHERE id='".$admin_id."'";
        }
        
        return json_decode(getData($db_prefix.'admins',' '.$sql),true);
    }

    function pp_get_transation($id = null, $custom_sql = '') {
        $conn = connectDatabase();
        global $db_prefix;
        
        if (!empty($id)) {
           $id = escape_string($id);
           
           $custom_sql = 'WHERE pp_id="'.$id.'"';
        }
        
        return json_decode(getData($db_prefix.'transaction', $custom_sql),true);
    }
    
    function pp_get_invoice($id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        
        return json_decode(getData($db_prefix.'invoice','WHERE i_id="'.$id.'"'),true);
    }
    
    function pp_get_invoice_items($id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        
        return json_decode(getData($db_prefix.'invoice_items','WHERE i_id="'.$id.'"'),true);
    }

    function pp_get_payment_link($id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        
        return json_decode(getData($db_prefix.'payment_link','WHERE pl_id="'.$id.'"'),true);
    }
    
    function pp_get_payment_link_items($id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        
        return json_decode(getData($db_prefix.'payment_link_input','WHERE pl_id ="'.$id.'"'),true);
    }

    function pp_get_sms_data($id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        
        return json_decode(getData($db_prefix.'sms_data','WHERE id="'.$id.'"'),true);
    }

    function pp_get_faq() {
        $conn = connectDatabase();
        global $db_prefix;
        
        return json_decode(getData($db_prefix.'faq','WHERE status = "active" ORDER BY 1 DESC'),true);
    }
    
    /*function pp_verify_transaction($payment_id, $plugin_slug, $payment_method, $transaction_id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $payment_id = escape_string($payment_id);
        $plugin_slug = escape_string($plugin_slug);
        $payment_method = escape_string($payment_method);
        $transaction_id = escape_string($transaction_id);
        
        $transaction_details = json_decode(getData($db_prefix.'transaction','WHERE pp_id="'.$payment_id.'"'),true);
        if($transaction_details['status'] == true){
            $plugin_setting = pp_get_plugin_setting($plugin_slug);
            
            $transaction_currency = $plugin_setting['currency'];
            
            $transaction_amount = convertToDefault($transaction_details['response'][0]['transaction_amount'], $transaction_details['response'][0]['transaction_currency'], $plugin_setting['currency']);
            $transaction_fee = safeNumber($plugin_setting['fixed_charge']) + ($transaction_amount * (safeNumber($plugin_setting['percent_charge']) / 100));
            $total = $transaction_amount+$transaction_fee;
        }
        
        if($payment_method == "--"){
            return json_decode(getData($db_prefix.'sms_data','WHERE transaction_id="'.$transaction_id.'" AND status="approved" AND amount="'.number_format($total, 2, '.', '').'" OR transaction_id="'.$transaction_id.'" AND status="approved" AND amount="'.number_format($total, 2).'" OR transaction_id="'.$transaction_id.'" AND status="approved" AND amount="'.$total.'"'),true);
        }else{
           return json_decode(getData($db_prefix.'sms_data','WHERE payment_method="'.$payment_method.'" AND transaction_id="'.$transaction_id.'" AND status="approved" AND amount="'.number_format($total, 2, '.', '').'" OR payment_method="'.$payment_method.'" AND transaction_id="'.$transaction_id.'" AND status="approved" AND amount="'.number_format($total, 2).'" OR payment_method="'.$payment_method.'" AND transaction_id="'.$transaction_id.'" AND status="approved" AND amount="'.$total.'"'),true);
        }
    }*/
    
    
    function pp_verify_transaction($payment_id, $plugin_slug, $payment_method, $transaction_id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $payment_id = escape_string($payment_id);
        $plugin_slug = escape_string($plugin_slug);
        $payment_method = escape_string($payment_method);
        $transaction_id = escape_string($transaction_id);
        
        $transaction_details = json_decode(getData($db_prefix.'transaction','WHERE pp_id="'.$payment_id.'"'),true);
        if($transaction_details['status'] == true){
            $plugin_setting = pp_get_plugin_setting($plugin_slug);
            
            $transaction_currency = $plugin_setting['currency'];
            
            $transaction_amount = convertToDefault($transaction_details['response'][0]['transaction_amount'], $transaction_details['response'][0]['transaction_currency'], $plugin_setting['currency']);
            $transaction_fee = safeNumber($plugin_setting['fixed_charge']) + ($transaction_amount * (safeNumber($plugin_setting['percent_charge']) / 100));
            $total = $transaction_amount + $transaction_fee;
    
            // ✅ Tolerance logic (new)
            $global_setting_response = json_decode(getData($db_prefix.'settings', 'WHERE id="1"'), true);
            $settings = pp_get_theme_setting($global_setting_response['response'][0]['gateway_theme']);
            
            $tolerance = $settings['tolerance'] ?? '0';
            
            if($tolerance == "" || $tolerance == "--"){
                $tolerance = 0;
            }
            
            $tolerance = safeNumber($tolerance);
            
            $min_total = number_format($total, 2, '.', '');
            $max_total = number_format($total + $tolerance, 2, '.', '');
            
            $min_total_two = number_format($total, 2);
            $max_total_two = number_format($total + $tolerance, 2);
            
            $min_total_three = $total;
            $max_total_three = $total + $tolerance;
        }else{
            return json_decode(getData($db_prefix.'sms_data','WHERE transaction_id="45745756754457-fake"'),true);
        }
    
        // ✅ Match within range instead of exact
        if($payment_method == "--"){
            return json_decode(getData($db_prefix.'sms_data','WHERE transaction_id="'.$transaction_id.'" AND status="approved" AND amount >= "'.$min_total.'" AND amount <= "'.$max_total.'" OR transaction_id="'.$transaction_id.'" AND status="approved" AND amount >= "'.$min_total_two.'" AND amount <= "'.$max_total_two.'" OR transaction_id="'.$transaction_id.'" AND status="approved" AND amount >= "'.$min_total_three.'" AND amount <= "'.$max_total_three.'"'),true);
        }else{
            return json_decode(getData($db_prefix.'sms_data','WHERE payment_method="'.$payment_method.'" AND transaction_id="'.$transaction_id.'" AND status="approved" AND amount >= "'.$min_total.'" AND amount <= "'.$max_total.'"         OR  payment_method="'.$payment_method.'" AND transaction_id="'.$transaction_id.'" AND status="approved" AND amount >= "'.$min_total_two.'" AND amount <= "'.$max_total_two.'" OR payment_method="'.$payment_method.'" AND transaction_id="'.$transaction_id.'" AND status="approved" AND amount >= "'.$min_total_three.'" AND amount <= "'.$max_total_three.'"'),true);
        }
    }
        
    
    function pp_check_transaction_exits($transaction_id) {
        $conn = connectDatabase();
        global $db_prefix;

        $transaction_id = escape_string($transaction_id);
         
        return json_decode(getData($db_prefix.'transaction','WHERE payment_verify_id="'.$transaction_id.'"'),true);
    }
    
    function pp_set_transaction_status($payment_id, $status) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $payment_id = escape_string($payment_id);
        $status = escape_string($status);
        
        $transaction_details = json_decode(getData($db_prefix.'transaction','WHERE pp_id="'.$payment_id.'"'),true);
        if($transaction_details['status'] == true){
            $columns = ['transaction_status'];
            $values = [$status];
            
            $condition = "pp_id = '".$payment_id."'"; 
            updateData($db_prefix.'transaction', $columns, $values, $condition);
            
            return true;
        }else{
            return false;
        }
    }

    function pp_set_transaction_byslip($id, $payment_method_id, $payment_method, $payment_slip_file, $status) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        $payment_method_id = escape_string($payment_method_id);
        $payment_method = escape_string($payment_method);
        $status = escape_string($status);
        
        $transaction_details = json_decode(getData($db_prefix.'transaction','WHERE pp_id="'.$id.'"'),true);
        if($transaction_details['status'] == true){
            $plugin_setting = pp_get_plugin_setting($payment_method_id);
            
            $transaction_currency = $plugin_setting['currency'];
            
            $transaction_amount = convertToDefault($transaction_details['response'][0]['transaction_amount'], $transaction_details['response'][0]['transaction_currency'], $plugin_setting['currency']);
            $transaction_fee = safeNumber($plugin_setting['fixed_charge']) + ($transaction_amount * (safeNumber($plugin_setting['percent_charge']) / 100));

            $max_file_size = 10 * 1024 * 1024; 
            
            $transaction_slip = json_decode(uploadImage($payment_slip_file, $max_file_size), true);
            if($transaction_slip['status'] == true){
                $transaction_slip = 'https://'.$_SERVER['HTTP_HOST'].'/pp-external/media/'.$transaction_slip['file'];
             
                $columns = ['payment_method_id', 'payment_method', 'payment_verify_way', 'payment_verify_id', 'transaction_status', 'transaction_amount', 'transaction_fee', 'transaction_currency'];
                $values = [$payment_method_id, $payment_method, 'slip', $transaction_slip, $status, $transaction_amount, $transaction_fee, $transaction_currency];
        
                $condition = "pp_id = '".$id."'"; 
                updateData($db_prefix.'transaction', $columns, $values, $condition);
                 
                if (function_exists('pp_trigger_hook')) {
                    pp_trigger_hook('pp_transaction_ipn', $id);
                }
            }

            return true;
        }else{
            return false;
        }
    }

    function pp_set_transaction_byid($id, $payment_method_id, $payment_method, $sender, $transaction_id, $status, $sms_data = 0) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $id = escape_string($id);
        $payment_method_id = escape_string($payment_method_id);
        $payment_method = escape_string($payment_method);
        $sender = escape_string($sender);
        $transaction_id = escape_string($transaction_id);
        $status = escape_string($status);
        
        $transaction_details = json_decode(getData($db_prefix.'transaction','WHERE pp_id="'.$id.'"'),true);
        if($transaction_details['status'] == true){
            $plugin_setting = pp_get_plugin_setting($payment_method_id);
            
            $transaction_currency = $plugin_setting['currency'];
            
            $transaction_amount = convertToDefault($transaction_details['response'][0]['transaction_amount'], $transaction_details['response'][0]['transaction_currency'], $plugin_setting['currency']);
            $transaction_fee = safeNumber($plugin_setting['fixed_charge']) + ($transaction_amount * (safeNumber($plugin_setting['percent_charge']) / 100));

            $columns = ['payment_method_id', 'payment_method', 'payment_verify_way', 'payment_sender_number', 'payment_verify_id', 'transaction_status', 'transaction_amount', 'transaction_fee', 'transaction_currency'];
            $values = [$payment_method_id, $payment_method, 'id', $sender, $transaction_id, $status, $transaction_amount, $transaction_fee, $transaction_currency];
    
            $condition = "pp_id = '".$id."'"; 
            updateData($db_prefix.'transaction', $columns, $values, $condition);
            
            if($sms_data !== 0){
                $columns = ['status'];
                $values = ['used'];
        
                $condition = "id = '".$sms_data."'"; 
                updateData($db_prefix.'sms_data', $columns, $values, $condition);
            }
             
            if (function_exists('pp_trigger_hook')) {
                pp_trigger_hook('pp_transaction_ipn', $id);
            }
             
            return true;
        }else{
            return false;
        }
    }
    
    function get_invoice_link($invoice_id){
        $invoice = pp_get_invoice($invoice_id);
        
        if($invoice['status'] == true){
            return 'https://'.$_SERVER['HTTP_HOST'].'/invoice/'.$invoice_id;
        }else{
            return false;
        }
    }
    
    function pp_get_paymentlink($pp_id){
        $payment = pp_get_transation($pp_id);
        
        if($payment['status'] == true){
            return 'https://'.$_SERVER['HTTP_HOST'].'/payment/'.$pp_id;
        }else{
            return false;
        }
    }
        
    function pp_get_support_links() {
        $conn = connectDatabase();
        global $db_prefix;
    
        $res = json_decode(getData($db_prefix.'settings', ''), true);
    
        return [
            'facebook_messenger' => [
                'url' => $res['response'][0]['facebook_messenger'] ?? '',
                'image' => 'https://cdn.builderhall.com/assets/socialicon/messenger.png',
                'text' => 'Click here to chat via Messenger.'
            ],
            'support_email_address' => [
                'url' => 'mailto:' . ($res['response'][0]['support_email_address'] ?? ''),
                'image' => 'https://cdn.builderhall.com/assets/socialicon/gmail.png',
                'text' => 'Click here to send us an email.'
            ],
            'support_phone_number' => [
                'url' => 'tel:' . ($res['response'][0]['support_phone_number'] ?? ''),
                'image' => 'https://cdn.builderhall.com/assets/socialicon/phone.png',
                'text' => 'Click here to call us.'
            ],
            'whatsapp_number' => [
                'url' => $res['response'][0]['whatsapp_number'] ?? '',
                'image' => 'https://cdn.builderhall.com/assets/socialicon/whatsapp.png',
                'text' => 'Chat with us on WhatsApp.'
            ],
            'facebook_page' => [
                'url' => $res['response'][0]['facebook_page'] ?? '',
                'image' => 'https://cdn.builderhall.com/assets/socialicon/facebook.png',
                'text' => 'Visit our Facebook page.'
            ],
            'telegram' => [
                'url' => $res['response'][0]['telegram'] ?? '',
                'image' => 'https://cdn.builderhall.com/assets/socialicon/telegram.png',
                'text' => 'Join us on Telegram.'
            ],
            'support_website' => [
                'url' => $res['response'][0]['support_website'] ?? '',
                'image' => 'https://cdn.builderhall.com/assets/socialicon/web.png',
                'text' => 'Visit our website.'
            ],
            'youtube_channel' => [
                'url' => $res['response'][0]['youtube_channel'] ?? '',
                'image' => 'https://cdn.builderhall.com/assets/socialicon/youtube.png',
                'text' => 'Subscribe to our YouTube channel.'
            ]
        ];
    }

    
    function pp_get_payment_gateways($category, $payment_id) {
        $conn = connectDatabase();
        global $db_prefix;
        
        $res_plugins = json_decode(getData($db_prefix.'plugins', 'WHERE plugin_dir="payment-gateway" AND status="active"'), true);
        
        if($res_plugins['status'] == true){
            $data = [];
            $count = 0;
            
            foreach($res_plugins['response'] as $plugins){
                $setting = pp_get_settings();
                $plugin_setting = pp_get_plugin_setting($plugins['plugin_slug']);
                $transaction_details = pp_get_transation($payment_id);
                
                $amount = $transaction_details['response'][0]['transaction_amount']+$transaction_details['response'][0]['transaction_fee'];
                $currency = $transaction_details['response'][0]['transaction_currency'];
                
                if (isset($plugin_setting['currency'])) {
                    $amount = convertToDefault($amount, $currency, $plugin_setting['currency']);
                    
                    if($plugin_setting['category'] == $category){
                        if($plugin_setting['status'] == "enable"){
                            $min = $plugin_setting['min_amount'];
                            $max = $plugin_setting['max_amount'];
                    
                            if (($min == '' || $min == '--' || $amount > $min - 0.1) && ($max == '' || $max == '--' || $amount < $max + 0.1)) {
                                if (file_exists(__DIR__ . '/../pp-content/plugins/'.$plugins['plugin_dir'].'/'.$plugins['plugin_slug'].'/assets/icon.png')) {
                                    $count = $count+1;
                                    
                                    $plugin_setting = pp_get_plugin_setting($plugins['plugin_slug']);
                                    
                                    $data[] = [
                                        'plugin_name' => $plugin_setting['display_name'],
                                        'plugin_slug' => $plugins['plugin_slug'],
                                        'plugin_logo' => 'https://'.$_SERVER['HTTP_HOST'].'/pp-content/plugins/'.$plugins['plugin_dir'].'/'.$plugins['plugin_slug'].'/assets/icon.png'
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            
            if($count == 0){
                return [ "status" => false, "response" => $data];
            }else{
                return [ "status" => true, "response" => $data];
            }
        }else{
            return [ "status" => false, "response" => $data];
        }
    }

?>