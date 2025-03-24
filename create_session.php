<?php
session_start();
header('Content-Type: application/json');

// 生成随机会话ID
function generateSessionId($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// 创建新会话
$sessionId = generateSessionId();
$session = [
    'id' => $sessionId,
    'created_at' => time(),
];

// 保存会话
$_SESSION['ai_session'] = $session;

// 返回会话信息
echo json_encode([
    'success' => true,
    'session' => $session
]);