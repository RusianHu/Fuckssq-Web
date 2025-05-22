<?php
// 引入安全工具类
require_once 'security_utils.php';

session_start();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // 禁用nginx缓冲

// 设置无限执行时间
set_time_limit(0);

// 检查是否为POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "data: " . json_encode(['error' => 'Invalid request method']) . "\n\n";
    exit;
}

// 获取请求数据
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['messages']) || empty($input['messages'])) {
    echo "data: " . json_encode(['error' => 'Messages are required']) . "\n\n";
    exit;
}

// 安全处理消息内容
if (is_array($input['messages'])) {
    foreach ($input['messages'] as &$message) {
        if (isset($message['content']) && is_string($message['content'])) {
            // 使用安全工具类过滤可能的恶意内容
            $message['content'] = SecurityUtils::sanitizeHtml($message['content']);
        }
    }
    unset($message); // 解除引用
}

// 获取会话ID并验证格式
$session_id = isset($input['session_id']) ? SecurityUtils::filterAlphanumeric($input['session_id']) : null;
if (!$session_id || !isset($_SESSION['ai_session']) || $_SESSION['ai_session']['id'] !== $session_id) {
    echo "data: " . json_encode(['error' => 'Invalid session']) . "\n\n";
    exit;
}

// 设置API参数并验证
$allowed_models = ['deepseek-chat', 'deepseek-reasoner'];
$model_input = isset($input['model']) ? trim($input['model']) : 'deepseek-reasoner';
// 验证模型名称是否在允许列表中
$model = in_array($model_input, $allowed_models) ? $model_input : 'deepseek-reasoner';
$stream = isset($input['stream']) && is_bool($input['stream']) ? $input['stream'] : true;

// 准备API请求数据
$apiData = [
    'model' => $model,
    'messages' => $input['messages'],
    'stream' => $stream,
    'temperature' => 0.7,
    'max_tokens' => 4000
];

// DeepSeek API密钥
$apiKeys = [
    'sk-11111111111111111111111111111111',  // 替换为你的实际API密钥
    'sk-22222222222222222222222222222222'  // 备用密钥
];

// 尝试所有API密钥
foreach ($apiKeys as $apiKey) {
    $ch = curl_init('https://api.deepseek.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    // 如果是流式输出
    if ($stream) {
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
            echo $data;
            flush();
            return strlen($data);
        });
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // 如果成功，结束循环
        if ($httpCode >= 200 && $httpCode < 300) {
            echo "data: [DONE]\n\n";
            exit;
        }
    } else {
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            echo "data: " . $result . "\n\n";
            echo "data: [DONE]\n\n";
            exit;
        }
    }
}

// 如果所有API密钥都失败
echo "data: " . json_encode(['error' => 'All API keys failed']) . "\n\n";
echo "data: [DONE]\n\n";
