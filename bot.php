<?php
// 🔥 MR PERVERT OSINT Telegram Bot 🔥
// Developed by: MR PERVERT @MrPervertOfficial

$botToken = '8256634624:AAFOitcwKcCk_Aw_G9Sb8Q2vb7tZWfQuNW8'; // Replace with your bot token

// ✨ GLASSY Welcome Message with Inline Buttons
$WELCOME_TEXT = "🔥 <b>MR PERVERT OSINT BOT</b> 🔥

<div style='background: linear-gradient(145deg, #1a1a2e, #16213e); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);'>
<div style='text-align: center; color: #00d4ff; font-size: 24px; font-weight: bold; text-shadow: 0 0 20px #00d4ff; margin-bottom: 15px;'>📱 PHONE TRACKER PRO</div>

<div style='color: #ffffff; font-size: 14px; line-height: 1.6;'>
🔹 <b>Complete Phone Details</b><br>
🔹 <b>Name • Father • Address</b><br>
🔹 <b>Aadhar • Email • Alt Numbers</b><br>
🔹 <b>Lightning Fast Results</b>
</div>

<div style='margin-top: 20px; color: #ff6b9d; font-size: 12px; opacity: 0.9;'>
👑 <i>Powered by MR PERVERT</i> 👑<br>
<span style='color: #00ff88;'>@MrPervertOfficial</span>
</div>
</div>";

// Phone Number API
$PHONE_API = 'https://source-code-api.vercel.app/?num=6395954711';

// File to store user input states
$USER_INPUT_STATES_FILE = 'user_states.json';

// 🔥 File Initialization
if (!file_exists($USER_INPUT_STATES_FILE)) {
    file_put_contents($USER_INPUT_STATES_FILE, json_encode([], JSON_PRETTY_PRINT));
    chmod($USER_INPUT_STATES_FILE, 0666);
}

// 🚀 Telegram API Helper
function tg($method, $params) {
    global $botToken;
    $url = "https://api.telegram.org/bot{$botToken}/{$method}";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// ⌨️ GLASSY INLINE KEYBOARD BUTTONS
function glassyMainKeyboard() {
    return [
        'inline_keyboard' => [
            [
                ['text' => '📱 Phone Tracker', 'callback_data' => 'phone_tracker'],
                ['text' => '🔍 Quick Search', 'callback_data' => 'quick_search']
            ],
            [
                ['text' => 'ℹ️ How to Use', 'callback_data' => 'help_menu'],
                ['text' => '⭐ Rate Bot', 'url' => 'https://t.me/MrPervertOfficial']
            ],
            [
                ['text' => '👑 About MR PERVERT', 'callback_data' => 'about_dev'],
                ['text' => '💎 Premium Tools', 'url' => 'https://t.me/MrPervertOfficial']
            ]
        ]
    ];
}

function inputKeyboard() {
    return [
        'inline_keyboard' => [
            [
                ['text' => '🔙 Back to Menu', 'callback_data' => 'back_menu'],
                ['text' => '❌ Cancel', 'callback_data' => 'cancel_op']
            ]
        ]
    ];
}

// 💾 User State Management (Enhanced)
function loadUserStates() {
    global $USER_INPUT_STATES_FILE;
    return file_exists($USER_INPUT_STATES_FILE) ? 
        json_decode(file_get_contents($USER_INPUT_STATES_FILE), true) ?: [] : [];
}

function saveUserStates($states) {
    global $USER_INPUT_STATES_FILE;
    file_put_contents($USER_INPUT_STATES_FILE, json_encode($states, JSON_PRETTY_PRINT));
}

function setUserState($userId, $state) {
    $states = loadUserStates();
    $states[$userId] = $state;
    saveUserStates($states);
}

function getUserState($userId) {
    $states = loadUserStates();
    return $states[$userId] ?? null;
}

function clearUserState($userId) {
    $states = loadUserStates();
    unset($states[$userId]);
    saveUserStates($states);
}

// 🌐 Enhanced API Call
function apiRequest($url, $timeout = 20) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (MR PERVERT GLASSY BOT)',
        CURLOPT_HTTPHEADER => ['Accept: application/json']
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false || $httpCode !== 200) {
        return ['error' => 'API_ERROR', 'message' => '🔄 Service busy, try again'];
    }
    
    $data = json_decode($response, true);
    return $data ?: ['error' => 'PARSE_ERROR'];
}

// ✨ Clean Response
function cleanResponse($text) {
    $patterns = [
        '/t\.me\/[^\s]+/i', '/@[^\s]+/i', '/https?:\/\/[^\s]+/i',
        '/credit[s]?:[^\\n]+/i', '/developer[^\\n]+/i'
    ];
    foreach ($patterns as $pattern) {
        $text = preg_replace($pattern, '', $text);
    }
    return trim(preg_replace('/\n{3,}/', "\n\n", $text));
}

// 📱 Phone Processing (Enhanced)
function processPhone($phone) {
    global $PHONE_API;
    $apiUrl = $PHONE_API . urlencode($phone);
    $response = apiRequest($apiUrl);
    
    if (isset($response['error'])) {
        return "❌ <b>Tracking Failed</b>\n\n📱 <code>{$phone}</code>\n\n⚠️ " . $response['message'];
    }
    
    if (!isset($response['success']) || !$response['success'] || empty($response['result'])) {
        return "📱 <b>No Data Found</b>\n\n<code>{$phone}</code>\n\n💭 Private number or no records";
    }
    
    $results = $response['result'];
    $output = "🌟 <b>MR PERVERT TRACKER RESULTS</b> 🌟\n\n";
    $output .= "📱 <b>Target Number:</b> <code>{$phone}</code>\n";
    $output .= "📊 <b>Found Records:</b> " . count($results) . "\n";
    $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    foreach ($results as $index => $record) {
        $output .= "📍 <b>Record #" . ($index + 1) . "</b>\n";
        $output .= " ├👤 <b>Name:</b> " . ($record['name'] ?? 'Hidden') . "\n";
        $output .= " ├👨 <b>Father:</b> " . ($record['father_name'] ?? 'N/A') . "\n";
        $output .= " ├🏠 <b>Address:</b> " . ($record['address'] ?? 'N/A') . "\n";
        $output .= " ├📡 <b>Circle:</b> " . ($record['circle'] ?? 'N/A') . "\n";
        $output .= " ├📱 <b>Alt Mobile:</b> " . ($record['alternative_mobile'] ?? 'N/A') . "\n";
        $output .= " ├🆔 <b>Aadhar:</b> " . ($record['aadhar_number'] ?? 'N/A') . "\n";
        $output .= " └📧 <b>Email:</b> " . ($record['email'] ?? 'N/A') . "\n\n";
        
        if ($index < count($results) - 1) {
            $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        }
    }
    
    $output .= "\n👑 <b>MR PERVERT OSINT</b> | @MrPervertOfficial";
    return cleanResponse($output);
}

// 💬 Send Message
function sendMsg($chatId, $text, $keyboard = null, $replyTo = null) {
    $params = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'HTML'];
    if ($replyTo) $params['reply_to_message_id'] = $replyTo;
    if ($keyboard) $params['reply_markup'] = json_encode($keyboard);
    return tg('sendMessage', $params);
}

// ✏️ Edit Message
function editMsg($chatId, $msgId, $text, $keyboard = null) {
    $params = [
        'chat_id' => $chatId,
        'message_id' => $msgId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    if ($keyboard) $params['reply_markup'] = json_encode($keyboard);
    return tg('editMessageText', $params);
}

// 🎯 MAIN BOT HANDLER
$update = json_decode(file_get_contents('php://input'), true);
if (!$update) exit;

if (isset($update['message'])) {
    $msg = $update['message'];
    $chatId = $msg['chat']['id'];
    $userId = $msg['from']['id'];
    $text = $msg['text'] ?? '';
    
    // /start command - GLASSY START SCREEN
    if (strpos($text, '/start') === 0) {
        sendMsg($chatId, $WELCOME_TEXT, glassyMainKeyboard());
        clearUserState($userId);
        exit;
    }
    
    $state = getUserState($userId);
    
    // Handle text input when waiting for phone
    if ($state == 'phone_input') {
        $phone = preg_replace('/[^0-9]/', '', $text);
        
        if (preg_match('/^[6-9]\d{9}$/', $phone)) {
            $loading = sendMsg($chatId, "🔄 <b>Tracking <code>{$phone}</code>...</b>\n⏳ Scanning databases (10-20s)", null, $msg['message_id']);
            
            if ($loading['ok']) {
                $resultMsgId = $loading['result']['message_id'];
                sleep(2); // Small delay for effect
                $result = processPhone($phone);
                editMsg($chatId, $resultMsgId, $result, glassyMainKeyboard());
            }
        } else {
            sendMsg($chatId, "❌ <b>Invalid Format!</b>\n\n📱 <b>Enter 10-digit Indian number</b>\n\n<i>Example: 9876543210</i>", inputKeyboard(), $msg['message_id']);
        }
        clearUserState($userId);
        exit;
    }
    
    // Default - show glassy menu
    sendMsg($chatId, $WELCOME_TEXT, glassyMainKeyboard());
    exit;
}

// Callback Query Handler (INLINE BUTTONS)
if (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $chatId = $callback['message']['chat']['id'];
    $userId = $callback['from']['id'];
    $msgId = $callback['message']['message_id'];
    $data = $callback['data'];
    
    // Answer callback
    tg('answerCallbackQuery', ['callback_query_id' => $callback['id']]);
    
    switch ($data) {
        case 'phone_tracker':
        case 'quick_search':
            setUserState($userId, 'phone_input');
            editMsg($chatId, $msgId, "📱 <b>Enter Phone Number</b>\n\n✅ <b>10-digit Indian mobile</b>\n\n<code>9876543210</code>\n\n<i>Send number to continue...</i>", inputKeyboard());
            break;
            
        case 'help_menu':
            $help = "🔥 <b>MR PERVERT GLASSY BOT HELP</b> 🔥\n\n📋 <b>Usage:</b>\n1️⃣ Click Phone Tracker\n2️⃣ Send 10-digit number\n3️⃣ Get full details instantly\n\n✅ <b>Features:</b>\n• Complete personal info\n• Address & location\n• Aadhar & documents\n• Multiple records\n\n⚡ <b>Super Fast Results</b>";
            editMsg($chatId, $msgId, $help, glassyMainKeyboard());
            break;
            
        case 'about_dev':
            $about = "👑 <b>MR PERVERT - OSINT KING</b> 👑\n\n💎 <b>Premium Features:</b>\n• Real-time tracking\n• Multiple databases\n• Clean & fast UI\n• Always updated\n\n📞 <b>Contact:</b> @MrPervertOfficial\n🌐 <b>Channel:</b> t.me/MrPervertOfficial\n\n💝 <b>Support Developer!</b>";
            editMsg($chatId, $msgId, $about, glassyMainKeyboard());
            break;
            
        case 'back_menu':
        case 'cancel_op':
            clearUserState($userId);
            editMsg($chatId, $msgId, $WELCOME_TEXT, glassyMainKeyboard());
            break;
    }
    exit;
}

// 🧪 Test
if (isset($_GET['test'])) {
    echo "🚀 MR PERVERT GLASSY BOT LIVE! ✨";
}
?>
