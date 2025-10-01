<?php
if (file_exists(__DIR__."/pp-config.php")) {
    if (file_exists(__DIR__.'/maintenance.lock')) {
        if (file_exists(__DIR__.'/pp-include/pp-maintenance.php')) {
            include(__DIR__."/pp-include/pp-maintenance.php");
        } else {
            die('System is under maintenance. Please try again later.');
        }
    } else {
        if (file_exists(__DIR__.'/pp-include/pp-controller.php')) {
            include(__DIR__."/pp-include/pp-controller.php");
        } else {
            echo 'System is under maintenance. Please try again later.';
            exit();
        }
        if (file_exists(__DIR__.'/pp-include/pp-model.php')) {
            include(__DIR__."/pp-include/pp-model.php");
        } else {
            echo 'System is under maintenance. Please try again later.';
            exit();
        }

        if (isset($_GET['webhook'])) {
            $webhook = escape_string($_GET['webhook']);

            if ($webhook == "") {
                echo 'System is under maintenance. Please try again later.';
                exit();
            } else {
                $response = json_decode(getData($db_prefix.'settings', 'WHERE webhook="'.$webhook.'"'), true);
                if ($response['status'] == true) {
                    $d_status = "Pairing";
                    if (isset($_POST['d_model']) && isset($_POST['d_brand']) && isset($_POST['d_version']) && isset($_POST['d_api_level'])) {
                        $d_model = escape_string($_POST['d_model']);
                        $d_brand = escape_string($_POST['d_brand']);
                        $d_version = escape_string($_POST['d_version']);
                        $d_api_level = escape_string($_POST['d_api_level']);

                        $response_device = json_decode(getData($db_prefix.'devices', 'WHERE d_model="'.$d_model.'" AND  d_brand="'.$d_brand.'" AND  d_version="'.$d_version.'" AND  d_api_level="'.$d_api_level.'" '), true);
                        if ($response_device['status'] == true) {

                            if (isset($_POST['connection_status'])) {
                                $d_status = escape_string($_POST['connection_status']);
                            } else {
                                $d_status = "Connected";
                            }

                            $columns = ['d_status',
                                'created_at'];
                            $values = [$d_status,
                                getCurrentDatetime('Y-m-d H:i:s')];
                            $condition = "id = '".$response_device['response'][0]['id']."'";

                            updateData($db_prefix.'devices', $columns, $values, $condition);
                        } else {
                            $columns = ['d_id',
                                'd_model',
                                'd_brand',
                                'd_version',
                                'd_api_level',
                                'd_status',
                                'created_at'];
                            $values = [rand(),
                                $d_model,
                                $d_brand,
                                $d_version,
                                $d_api_level,
                                'Connected',
                                getCurrentDatetime('Y-m-d H:i:s')];

                            insertData($db_prefix.'devices', $columns, $values);
                        }
                    }

                    $payload = file_get_contents('php://input');

                    $decoded = json_decode($payload, true);

                    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

                    if ($userAgent === 'mh-piprapay-api-key') {
                        $from = $decoded['from'] ?? ($_POST['from'] ?? '');
                        $text = $decoded['text'] ?? ($_POST['text'] ?? '');
                        $sentStamp = $decoded['sentStamp'] ?? ($_POST['sentStamp'] ?? '');
                        $receivedStamp = $decoded['receivedStamp'] ?? ($_POST['receivedStamp'] ?? '');
                        $sim = $decoded['sim'] ?? ($_POST['sim'] ?? '');

                        if ($sim == 1) {
                            $sim = "sim1";
                        } else {
                            if ($sim == 2) {
                                $sim = "sim2";
                            }
                        }

                        $mfs_providers = [
                            'NAGAD' => 'Nagad',
                            'Nagad' => 'Nagad',
                            '01708403334' => 'Nagad',
                            'bKash' => 'bKash',
                            '16216' => 'Rocket',
                            'upay' => 'Upay',
                            'tap.' => 'Tap',
                            '16269' => 'OkWallet',
                            'IBBL .' => 'Cellfin',
                            'IPAY' => 'Ipay',
                            'iPAY' => 'Ipay',
                            'PathaoPay' => 'Pathao Pay'
                        ];

                        // Match provider — STRICT by sender only (no text-based matching)
                        $matchFound = false;
                        $matchedFullName = '';

                        $sender = trim($from); // raw sender as received
                        $senderLower = mb_strtolower($sender, 'UTF-8'); // normalize for case-insensitive compare

                        // Normalize provider keys for comparison (case-insensitive)
                        foreach ($mfs_providers as $short => $full) {
                            // compare the sender to the provider-key using case-insensitive equality
                            if (strcasecmp($sender, $short) === 0) {
                                $matchFound = true;
                                $matchedFullName = $full;
                                break;
                            }

                            // additionally allow numeric sender matches where provider key might be numeric string
                            // (This is redundant with strcasecmp but keeps intent explicit)
                            if (is_numeric($short) && $sender === $short) {
                                $matchFound = true;
                                $matchedFullName = $full;
                                break;
                            }
                        }

                        // If you prefer tolerant matching for keys that include small formatting differences
                        // (example: some senders may come as 'bkash' or 'bKash' or 'BKASH'),
                        // the above strcasecmp already handles case-insensitivity.
                        // IMPORTANT: we DO NOT check $text here — no body-based matching.

                        if ($matchFound) {
                            $sms_status = "review";

                            $provider_formats = [
                                'bKash' => [
                                    [
                                        'type' => 'sms1',
                                        'format' => '/Cash In Tk (?<amount>[\d,]+\.\d{2}) from (?<mobile>\d+) successful\. Fee Tk (?<fee>[\d,]+\.\d{2})\. Balance Tk (?<balance>[\d,]+\.\d{2})\. TrxID (?<trxid>\w+) at (?<datetime>\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/'
                                    ],
                                    [
                                        'type' => 'sms2',
                                        'format' => '/You have received Tk (?<amount>[\d,]+\.\d{2}) from (?<mobile>\d+)\. Fee Tk (?<fee>[\d,]+\.\d{2})\. Balance Tk (?<balance>[\d,]+\.\d{2})\. TrxID (?<trxid>\w+) at (?<datetime>\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/'
                                    ],
                                    [
                                        'type' => 'sms3',
                                        'format' => '/You have received Tk (?<amount>[\d,]+\.\d{2}) from (?<mobile>\d+)\. Ref .*?Fee Tk (?<fee>[\d,]+\.\d{2})\. Balance Tk (?<balance>[\d,]+\.\d{2})\. TrxID (?<trxid>\w+) at (?<datetime>\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/'
                                    ],
                                    [
                                        'type' => 'sms4',
                                        'format' => '/You have received payment Tk (?<amount>[\d,]+\.\d{2}) from (?<mobile>\d+)\. Fee Tk (?<fee>[\d,]+\.\d{2})\. Balance Tk (?<balance>[\d,]+\.\d{2})\. TrxID (?<trxid>\w+) at (?<datetime>\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/'
                                    ],
                                    [
                                        'type' => 'sms5',
                                        'format' => '/You have received Tk (?<amount>[\d,]+\.\d{2}) from (?<mobile>\d+)\.Ref .*?TrxID (?<trxid>\w+) at (?<datetime>\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})/'
                                    ]
                                ],
                                'Nagad' => [
                                    [
                                        'type' => 'sms1',
                                        'format' => '/Cash In Received\.\nAmount: Tk (?<amount>[\d,]+\.\d{2})\nUddokta: (?<mobile>\d+)\nTxnID: (?<trxid>[A-Z0-9]+)\nBalance: (?<balance>[\d,]+\.\d{2})\n(?<date>\d{2}\/\d{2}\/\d{4}) (?<time>\d{2}:\d{2})/'
                                    ],
                                    [
                                        'type' => 'sms2',
                                        'format' => '/Money Received\.\nAmount: Tk (?<amount>[\d,]+\.\d{2})\nSender: (?<mobile>\d+)\nRef: (.+)\nTxnID: (?<trxid>\w+)\nBalance: Tk (?<balance>[\d,]+\.\d{2})\n(?<date>\d{2}\/\d{2}\/\d{4}) (?<time>\d{2}:\d{2})/'
                                    ]
                                ],
                                'Upay' => [
                                    [
                                        'type' => 'sms1',
                                        'format' => '/Cash In Received\.\nAmount: Tk (?<amount>[\d,]+\.\d{2})\nUddokta: (?<mobile>\d+)\nTxnID: (?<trxid>[A-Z0-9]+)\nBalance: (?<balance>[\d,]+\.\d{2})\n(?<date>\d{2}\/\d{2}\/\d{4}) (?<time>\d{2}:\d{2})/'
                                    ],
                                    [
                                        'type' => 'sms2',
                                        'format' => '/Tk\. (?<amount>[\d,]+\.\d{2}) has been received from (?<mobile>\d+)\. Ref-.*?Balance Tk\. (?<balance>[\d,]+\.\d{2})\. TrxID (?<trxid>\w+) at (?<datetime>\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})\./'
                                    ]
                                ],
                                'Rocket' => [
                                    // Rocket SMS1: A/C received
                                    [
                                        'type' => 'sms1',
                                        'format' => '/Tk(?<amount>[\d,]+(?:\.\d{1,2})?)\s+received\s+from\s+A\/C:(?:\*+)?(?<mobile>\d+)\s*Fee:Tk\.?(?<fee>[\d,]+(?:\.\d{1,2})?|0),?\s*Your\s*A\/C\s*Balance:\s*Tk(?<balance>[\d,]+(?:\.\d{1,2})?)\s*TxnId:(?<trxid>\d+)\s*Date:(?<datetime>\d{2}-[A-Z]{3}-\d{2}\s+\d{2}:\d{2}:\d{2}\s*(?:am|pm))/i'
                                    ],

                                    // Rocket SMS2: Card credited
                                    [
                                        'type' => 'sms2',
                                        'format' => '/Tk(?<amount>[\d,]+(?:\.\d{1,2})?)\s+credited\s+from\s+card\s+\**(?<mobile>\d+)\.?\s*Fee:Tk\.?(?<fee>[\d,]+(?:\.\d{1,2})?|0)\s*NetBal:Tk(?<balance>[\d,]+(?:\.\d{1,2})?)\s*TxnId:(?<trxid>\d+)\s*Date:(?<datetime>\d{2}-[A-Z]{3}-\d{2}\s+\d{2}:\d{2}:\d{2}\s*(?:am|pm))/i'
                                    ]
                                ]


                                //just basic add as you need.
                            ];

                            foreach ($provider_formats as &$formats) {
                                foreach ($formats as &$entry) {
                                    if (isset($entry['format'])) {
                                        $entry['format'] = str_replace('\n', '\s*', $entry['format']);
                                    }
                                }
                            }

                            $matched_data = [
                                'amount' => '0',
                                'mobile' => '--',
                                'trxid' => '--',
                                'balance' => '0',
                                'datetime' => $receivedStamp
                            ];

                            $parsed = false;

                            if (isset($provider_formats[$matchedFullName])) {
                                foreach ($provider_formats[$matchedFullName] as $formatData) {
                                    if (preg_match($formatData['format'], $text, $matches)) {
                                        $parsed = true;
                                        $matched_data['amount'] = str_replace(',', '', $matches['amount'] ?? '0');
                                        $matched_data['mobile'] = $matches['mobile'] ?? '--';
                                        $matched_data['trxid'] = $matches['trxid'] ?? '--';
                                        $matched_data['balance'] = str_replace(',', '', $matches['balance'] ?? '0');

                                        if (isset($matches['datetime'])) {
                                            $matched_data['datetime'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $matches['datetime'])));
                                        } elseif (isset($matches['date']) && isset($matches['time'])) {
                                            $matched_data['datetime'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $matches['date']) . ' ' . $matches['time']));
                                        }

                                        $sms_status = "approved";
                                        break;
                                    }
                                }
                            }

                            $columns = ['entry_type',
                                'sim',
                                'payment_method',
                                'mobile_number',
                                'transaction_id',
                                'amount',
                                'balance',
                                'message',
                                'status',
                                'created_at'];
                            $values = [
                                'automatic',
                                $sim,
                                $matchedFullName,
                                $matched_data['mobile'],
                                $matched_data['trxid'],
                                $matched_data['amount'],
                                $matched_data['balance'],
                                $text,
                                $sms_status,
                                $matched_data['datetime']
                            ];

                            insertData($db_prefix . 'sms_data', $columns, $values);
                        }
                    }


                    echo json_encode(['status' => "true", 'message' => "Device ".$d_status]);
                    exit();
                } else {
                    echo json_encode(['status' => "false", 'message' => "Invalid Webhook"]);
                    exit();
                }
            }
        } else {
            if (isset($_GET['cron'])) {
                if (function_exists('pp_trigger_hook')) {
                    pp_trigger_hook('pp_cron');
                }

                echo json_encode(['status' => "false", 'message' => "Direct access not allowed"]);
                exit();
            } else {
                if ($global_user_login == true) {
                    ?>
                    <script>
                        location.href = "https://<?php echo $_SERVER['HTTP_HOST'] ?>/admin/dashboard";
                    </script>
                    <?php
                } else {
                    ?>
                    <script>
                        location.href = "https://<?php echo $_SERVER['HTTP_HOST'] ?>/admin/login";
                    </script>
                    <?php
                }
            }
        }
    }
} else {
    ?>
    <script>
        location.href = "https://<?php echo $_SERVER['HTTP_HOST'] ?>/install/";
    </script>
    <?php
}
?>