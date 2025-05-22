<?php
// 引入安全工具类
require_once 'security_utils.php';

session_start();
header('Content-Type: application/json');

// 创建新会话 - 使用安全工具类生成安全令牌
$sessionId = SecurityUtils::generateSecureToken(32);
$session = [
    'id' => $sessionId,
    'created_at' => time(),
    'ip' => $_SERVER['REMOTE_ADDR'], // 记录IP地址以增强安全性
    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ?
                   SecurityUtils::sanitizeHtml($_SERVER['HTTP_USER_AGENT']) : '', // 记录用户代理
];

// 保存会话
$_SESSION['ai_session'] = $session;

// 返回会话信息
echo json_encode([
    'success' => true,
    'session' => $session
]);