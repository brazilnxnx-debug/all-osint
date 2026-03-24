<?php
// Multi Lookup Bot - Powered by Rohit Sharma
// Developer: @FroxtDevil

// Bot Configuration
$botToken = "8682466592:AAHdcb9ETwsrLFxEY9sUIAaUgYtleUNPl5k"; // Replace with your bot token
$apiURL = "https://api.telegram.org/bot$botToken/";
$ownerID = "8762385283";
$adminIDs = ["8762385283"];
$channelLink = "https://t.me/All_channel_links_please_join";
$channelUsername = "All_channel_links_please_join"; // Without @

// Database configuration
$dbFile = "bot_database.json";

// Initialize database
function initDB() {
    global $dbFile;
    if (!file_exists($dbFile)) {
        $defaultData = [
            'users' => [],
            'admins' => ["8682466592:AAHdcb9ETwsrLFxEY9sUIAaUgYtleUNPl5k"],
            'banned' => [],
            'settings' => [
                'require_join' => true
            ]
        ];
        file_put_contents($dbFile, json_encode($defaultData));
    }
    return json_decode(file_get_contents($dbFile), true);
}

function saveDB($data) {
    global $dbFile;
    file_put_contents($dbFile, json_encode($data));
}

// Function to check if user is admin
function isAdmin($userID) {
    global $ownerID, $adminIDs;
    if ($userID == $ownerID) return true;
    return in_array($userID, $adminIDs);
}

// Function to check if user is banned
function isBanned($userID) {
    $db = initDB();
    return in_array($userID, $db['banned']);
}

// Function to check channel membership
function checkChannelMembership($chatID, $userID) {
    global $apiURL, $channelUsername;
    
    $url = $apiURL . "getChatMember?chat_id=@$channelUsername&user_id=$userID";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data['ok'] && isset($data['result']['status'])) {
        $status = $data['result']['status'];
        return in_array($status, ['member', 'administrator', 'creator']);
    }
    return false;
}

// Function to send message
function sendMessage($chatID, $text, $keyboard = null) {
    global $apiURL;
    
    $data = [
        'chat_id' => $chatID,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    
    $url = $apiURL . "sendMessage";
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

// Function to edit message
function editMessage($chatID, $messageID, $text, $keyboard = null) {
    global $apiURL;
    
    $data = [
        'chat_id' => $chatID,
        'message_id' => $messageID,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    
    $url = $apiURL . "editMessageText";
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

// Function to get main keyboard
function getMainKeyboard() {
    return [
        'inline_keyboard' => [
            [
                ['text' => '📱 Phone Number', 'callback_data' => 'lookup_phone'],
                ['text' => '🚗 Vehicle Number', 'callback_data' => 'lookup_vehicle']
            ],
            [
                ['text' => '🆔 Aadhaar Card', 'callback_data' => 'lookup_aadhaar'],
                ['text' => '📮 Pincode', 'callback_data' => 'lookup_pincode']
            ],
            [
                ['text' => '🏢 GST Number', 'callback_data' => 'lookup_gst'],
                ['text' => '💸 UPI ID', 'callback_data' => 'lookup_upi']
            ],
            [
                ['text' => '🏦 IFSC Code', 'callback_data' => 'lookup_ifsc'],
                ['text' => '🪪 Pan Card', 'callback_data' => 'lookup_pan']
            ],
            [
                ['text' => '🌐 IP Lookup', 'callback_data' => 'lookup_ip'],
                ['text' => '🌤️ Weather Lookup', 'callback_data' => 'lookup_weather']
            ],
            [
                ['text' => '👨‍👩‍👧‍👦 Family', 'callback_data' => 'lookup_family'],
                ['text' => '📷 Instagram', 'callback_data' => 'lookup_instagram']
            ],
            [
                ['text' => '📡 TG Lookup', 'callback_data' => 'lookup_tg'],
                ['text' => '📧 Email', 'callback_data' => 'lookup_email']
            ],
            [
                ['text' => '🔢 IMEI', 'callback_data' => 'lookup_imei'],
                ['text' => '🍛 RASHAN', 'callback_data' => 'lookup_rashan']
            ],
            [
                ['text' => '❓ Help', 'callback_data' => 'help'],
                ['text' => '📊 Statistics', 'callback_data' => 'stats']
            ]
        ]
    ];
}

// Function to get admin panel keyboard
function getAdminKeyboard() {
    return [
        'inline_keyboard' => [
            [
                ['text' => '➕ Add Inline Button', 'callback_data' => 'admin_add_button'],
                ['text' => '🔗 Add Button API', 'callback_data' => 'admin_add_api']
            ],
            [
                ['text' => '📢 Add Channel', 'callback_data' => 'admin_add_channel'],
                ['text' => '👥 Add Admin', 'callback_data' => 'admin_add_admin']
            ],
            [
                ['text' => '📣 Broadcast', 'callback_data' => 'admin_broadcast'],
                ['text' => '🚫 Ban User', 'callback_data' => 'admin_ban']
            ],
            [
                ['text' => '✅ Unban User', 'callback_data' => 'admin_unban'],
                ['text' => '👥 All Users', 'callback_data' => 'admin_users']
            ],
            [
                ['text' => '◀️ Back to Main', 'callback_data' => 'back_to_main']
            ]
        ]
    ];
}

// Function to get example message
function getExampleMessage($type) {
    $examples = [
        'phone' => "• 📱 Phone: `9876543210`\n• 🆔 Aadhaar: `123456789012`\n• 🏢 GST: `27ABCDE1234F1Z5`\n• 💸 UPI: `username@bank`\n• 🏦 IFSC: `SBIN0001234`\n• 📮 Pincode: `110001`\n• 🚗 Vehicle: `UP32QP0001`\n• 🪪 Pan Card: `ABCDE1234F`\n• 🌐 IP: `8.8.8.8`\n• 📧 Email: `user@example.com`\n• 🔢 IMEI: `123456789012345`",
        'weather' => "• 🌤️ Weather: `Mumbai` or `Mumbai,IN`\n• Example: `London` or `New York,US`",
        'instagram' => "• 📷 Instagram: `username`\n• Example: `instagram` or `cristiano`",
        'tg' => "• 📡 TG Lookup: `username`\n• Example: `@username` or `username`",
        'family' => "• 👨‍👩‍👧‍👦 Family: `Family Name`\n• Example: `Singh` or `Sharma`",
        'rashan' => "• 🍛 RASHAN: `Ration Card Number`\n• Example: `123456789012`"
    ];
    
    $baseText = "🔍 *Send Input*\n━━━━━━━━━━━━━━━━━━━━\n\n";
    $baseText .= "Please send the required information:\n\n";
    $baseText .= "📋 *Examples:*\n";
    $baseText .= $examples[$type] ?? $examples['phone'];
    $baseText .= "\n━━━━━━━━━━━━━━━━━━━━\n";
    $baseText .= "Made by @LovekushGupta6969\n";
    $baseText .= "Powered By Lovekush Gupta.";
    
    return $baseText;
}

// Function to perform lookup based on type and input
function performLookup($type, $input) {
    // API placeholders - Replace with actual APIs
    $apis = [
        'phone' => 'https://api.example.com/phone/' . $input,
        'aadhaar' => 'https://api.example.com/aadhaar/' . $input,
        'gst' => 'https://api.example.com/gst/' . $input,
        'upi' => 'https://api.example.com/upi/' . $input,
        'ifsc' => 'https://api.example.com/ifsc/' . $input,
        'pincode' => 'https://api.example.com/pincode/' . $input,
        'vehicle' => 'https://api.example.com/vehicle/' . $input,
        'pan' => 'https://api.example.com/pan/' . $input,
        'ip' => 'https://api.example.com/ip/' . $input,
        'weather' => 'https://api.example.com/weather/' . $input,
        'instagram' => 'https://api.example.com/instagram/' . $input,
        'tg' => 'https://tg-2-num-api-org.vercel.app/api/search?userid=' . $input,
        'email' => 'https://api.example.com/email/' . $input,
        'imei' => 'https://api.example.com/imei/' . $input,
        'rashan' => 'https://api.example.com/rashan/' . $input,
        'family' => 'https://api.example.com/family/' . $input
    ];
    
    // Return placeholder response (replace with actual API integration)
    $response = "🔍 *{$type} Lookup Result*\n━━━━━━━━━━━━━━━━━━━━\n\n";
    $response .= "📝 *Input:* `{$input}`\n\n";
    $response .= "⚠️ *Note:* API integration is pending.\n";
    $response .= "API Endpoint: {$apis[$type]}\n\n";
    $response .= "━━━━━━━━━━━━━━━━━━━━\n";
    $response .= "Made by @FroxtDevil\n";
    $response .= "Powered By Rohit Sharma.";
    
    return $response;
}

// Function to get welcome message
function getWelcomeMessage() {
    $text = "🤖 *Welcome To Multi Lookup Bot*\n━━━━━━━━━━━━━━━━━━━━\n\n";
    $text .= "🔍 *Features:*\n";
    $text .= "• 📱 Phone Number Lookup\n";
    $text .= "• 🆔 Aadhaar Card Lookup\n";
    $text .= "• 🏢 GST Number Lookup\n";
    $text .= "• 💸 UPI ID Lookup\n";
    $text .= "• 🏦 IFSC Code Lookup\n";
    $text .= "• 📮 Pincode Lookup\n";
    $text .= "• 🚗 Vehicle RC Lookup\n";
    $text .= "• 🪪 Pan Card Lookup\n";
    $text .= "• 🌐 IP Address Lookup\n";
    $text .= "• 🌤️ Weather Lookup\n";
    $text .= "• 👨‍👩‍👧‍👦 Family Details Lookup\n";
    $text .= "• 📷 Instagram Profile Lookup\n";
    $text .= "• 📡 Telegram Profile Lookup\n";
    $text .= "• 📧 Email Address Lookup\n";
    $text .= "• 🔢 IMEI Number Lookup\n";
    $text .= "• 🍛 RASHAN Card Lookup\n";
    $text .= "\n━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "Made by @FroxtDevil\n";
    $text .= "Powered By Rohit Sharma.";
    
    return $text;
}

// Main webhook handler
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message'])) {
    $message = $update['message'];
    $chatID = $message['chat']['id'];
    $userID = $message['from']['id'];
    $text = $message['text'] ?? '';
    
    // Register user
    $db = initDB();
    if (!in_array($userID, $db['users'])) {
        $db['users'][] = $userID;
        saveDB($db);
    }
    
    // Check if user is banned
    if (isBanned($userID)) {
        sendMessage($chatID, "🚫 You are banned from using this bot.\nContact: @FroxtDevil");
        exit;
    }
    
    // Check channel membership
    if ($db['settings']['require_join'] && !checkChannelMembership($chatID, $userID)) {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📢 Join Channel', 'url' => $channelLink]],
                [['text' => '✅ Joined', 'callback_data' => 'check_join']]
            ]
        ];
        sendMessage($chatID, "🔒 *Please join our channel to use this bot!*\n\nChannel: $channelLink", $keyboard);
        exit;
    }
    
    // Handle commands
    if ($text == '/start') {
        sendMessage($chatID, getWelcomeMessage(), getMainKeyboard());
    }
    elseif ($text == '/admin' && isAdmin($userID)) {
        sendMessage($chatID, "🔐 *Admin Panel*\n━━━━━━━━━━━━━━━━━━━━\n\nWelcome Admin! Use the buttons below to manage the bot.", getAdminKeyboard());
    }
    elseif (isset($update['message']['text']) && isset($update['message']['reply_to_message'])) {
        // Handle admin broadcast
        if (isAdmin($userID) && isset($update['message']['reply_to_message']['text']) && strpos($update['message']['reply_to_message']['text'], 'Broadcast Mode') !== false) {
            $db = initDB();
            $success = 0;
            $failed = 0;
            
            foreach ($db['users'] as $user) {
                if (sendMessage($user, $text)) {
                    $success++;
                } else {
                    $failed++;
                }
                usleep(50000);
            }
            
            sendMessage($chatID, "📢 *Broadcast Complete*\n✅ Success: $success\n❌ Failed: $failed");
        }
    }
    else {
        // Handle lookup input
        if (isset($update['message']['reply_to_message'])) {
            $replyText = $update['message']['reply_to_message']['text'];
            if (strpos($replyText, 'Send Input') !== false) {
                // Extract lookup type from previous message
                $lookupType = 'phone'; // Default
                if (strpos($replyText, 'Phone') !== false) $lookupType = 'phone';
                elseif (strpos($replyText, 'Vehicle') !== false) $lookupType = 'vehicle';
                elseif (strpos($replyText, 'Aadhaar') !== false) $lookupType = 'aadhaar';
                elseif (strpos($replyText, 'Pincode') !== false) $lookupType = 'pincode';
                elseif (strpos($replyText, 'GST') !== false) $lookupType = 'gst';
                elseif (strpos($replyText, 'UPI') !== false) $lookupType = 'upi';
                elseif (strpos($replyText, 'IFSC') !== false) $lookupType = 'ifsc';
                elseif (strpos($replyText, 'Pan') !== false) $lookupType = 'pan';
                elseif (strpos($replyText, 'IP') !== false) $lookupType = 'ip';
                elseif (strpos($replyText, 'Weather') !== false) $lookupType = 'weather';
                elseif (strpos($replyText, 'Instagram') !== false) $lookupType = 'instagram';
                elseif (strpos($replyText, 'TG') !== false) $lookupType = 'tg';
                elseif (strpos($replyText, 'Email') !== false) $lookupType = 'email';
                elseif (strpos($replyText, 'IMEI') !== false) $lookupType = 'imei';
                elseif (strpos($replyText, 'RASHAN') !== false) $lookupType = 'rashan';
                elseif (strpos($replyText, 'Family') !== false) $lookupType = 'family';
                
                $result = performLookup($lookupType, $text);
                sendMessage($chatID, $result);
            }
        }
    }
}
elseif (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $chatID = $callback['message']['chat']['id'];
    $userID = $callback['from']['id'];
    $messageID = $callback['message']['message_id'];
    $data = $callback['data'];
    
    // Check if user is banned
    if (isBanned($userID)) {
        sendMessage($chatID, "🚫 You are banned from using this bot.\nContact: @FroxtDevil");
        exit;
    }
    
    // Check channel membership
    $db = initDB();
    if ($db['settings']['require_join'] && !checkChannelMembership($chatID, $userID) && $data != 'check_join') {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '📢 Join Channel', 'url' => $channelLink]],
                [['text' => '✅ Joined', 'callback_data' => 'check_join']]
            ]
        ];
        editMessage($chatID, $messageID, "🔒 *Please join our channel to use this bot!*\n\nChannel: $channelLink", $keyboard);
        exit;
    }
    
    // Handle callbacks
    if (strpos($data, 'lookup_') === 0) {
        $type = str_replace('lookup_', '', $data);
        $exampleMsg = getExampleMessage($type);
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '◀️ Back', 'callback_data' => 'back_to_main']]
            ]
        ];
        
        editMessage($chatID, $messageID, $exampleMsg, $keyboard);
    }
    elseif ($data == 'help') {
        $helpText = "❓ *Help & Support*\n━━━━━━━━━━━━━━━━━━━━\n\n";
        $helpText .= "📌 *How to Use:*\n";
        $helpText .= "1️⃣ Select any lookup option from the menu\n";
        $helpText .= "2️⃣ Send the required information\n";
        $helpText .= "3️⃣ Get instant results!\n\n";
        $helpText .= "📞 *Support:* @FroxtDevil\n";
        $helpText .= "📢 *Channel:* $channelLink\n\n";
        $helpText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $helpText .= "Made by @FroxtDevil\n";
        $helpText .= "Powered By Rohit Sharma.";
        
        editMessage($chatID, $messageID, $helpText, getMainKeyboard());
    }
    elseif ($data == 'stats') {
        $db = initDB();
        $totalUsers = count($db['users']);
        $bannedUsers = count($db['banned']);
        $activeUsers = $totalUsers - $bannedUsers;
        
        $statsText = "📊 *Bot Statistics*\n━━━━━━━━━━━━━━━━━━━━\n\n";
        $statsText .= "👥 Total Users: `$totalUsers`\n";
        $statsText .= "✅ Active Users: `$activeUsers`\n";
        $statsText .= "🚫 Banned Users: `$bannedUsers`\n";
        $statsText .= "👑 Owner: @FroxtDevil\n\n";
        $statsText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $statsText .= "Made by @FroxtDevil\n";
        $statsText .= "Powered By Rohit Sharma.";
        
        editMessage($chatID, $messageID, $statsText, getMainKeyboard());
    }
    elseif ($data == 'back_to_main') {
        editMessage($chatID, $messageID, getWelcomeMessage(), getMainKeyboard());
    }
    elseif ($data == 'check_join') {
        if (checkChannelMembership($chatID, $userID)) {
            editMessage($chatID, $messageID, getWelcomeMessage(), getMainKeyboard());
        } else {
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '📢 Join Channel', 'url' => $channelLink]],
                    [['text' => '✅ Joined', 'callback_data' => 'check_join']]
                ]
            ];
            editMessage($chatID, $messageID, "❌ *You haven't joined the channel yet!*\n\nPlease join and click 'Joined' button.", $keyboard);
        }
    }
    // Admin panel callbacks
    elseif (strpos($data, 'admin_') === 0 && isAdmin($userID)) {
        $action = str_replace('admin_', '', $data);
        
        switch($action) {
            case 'add_button':
                sendMessage($chatID, "📝 *Add Inline Button*\n━━━━━━━━━━━━━━━━━━━━\n\nSend button details in format:\n`button_text|callback_data`\n\nExample: `New Button|lookup_new`");
                break;
                
            case 'add_api':
                sendMessage($chatID, "🔗 *Add Button API*\n━━━━━━━━━━━━━━━━━━━━\n\nSend API details in format:\n`button_name|api_endpoint|method`\n\nExample: `phone|https://api.example.com/phone/|GET`");
                break;
                
            case 'add_channel':
                sendMessage($chatID, "📢 *Add Channel*\n━━━━━━━━━━━━━━━━━━━━\n\nSend channel username/link to add:\nExample: `@channelusername`");
                break;
                
            case 'add_admin':
                sendMessage($chatID, "👥 *Add Admin*\n━━━━━━━━━━━━━━━━━━━━\n\nSend user ID to add as admin:\nExample: `1234567890`");
                break;
                
            case 'broadcast':
                $keyboard = [
                    'force_reply' => true
                ];
                sendMessage($chatID, "📣 *Broadcast Mode*\n━━━━━━━━━━━━━━━━━━━━\n\nSend your broadcast message.\nReply to this message with your broadcast content.", $keyboard);
                break;
                
            case 'ban':
                sendMessage($chatID, "🚫 *Ban User*\n━━━━━━━━━━━━━━━━━━━━\n\nSend user ID to ban:\nExample: `1234567890`");
                break;
                
            case 'unban':
                sendMessage($chatID, "✅ *Unban User*\n━━━━━━━━━━━━━━━━━━━━\n\nSend user ID to unban:\nExample: `1234567890`");
                break;
                
            case 'users':
                $db = initDB();
                $usersList = "👥 *All Users*\n━━━━━━━━━━━━━━━━━━━━\n\n";
                foreach ($db['users'] as $index => $user) {
                    $status = in_array($user, $db['banned']) ? "🚫 Banned" : "✅ Active";
                    $usersList .= "`" . ($index + 1) . ".` User ID: `$user` - $status\n";
                    if ($index == 49) {
                        $usersList .= "\n*Showing first 50 users only*";
                        break;
                    }
                }
                $usersList .= "\n━━━━━━━━━━━━━━━━━━━━\nTotal: " . count($db['users']) . " users";
                sendMessage($chatID, $usersList);
                break;
        }
    }
    
    // Answer callback query
    $url = $apiURL . "answerCallbackQuery?callback_query_id=" . $callback['id'];
    file_get_contents($url);
}
?>