<?php
/**
 * 安全工具类
 * 
 * 这个类包含了一系列用于增强应用程序安全性的函数，
 * 包括输入验证、命令行参数转义等功能。
 *
 * 用法示例:
 * 
 * // 引入 SecurityUtils 类 (如果尚未自动加载)
 * // require_once 'path/to/security_utils.php';
 * 
 * // 1. 安全地转义命令行参数
 * $safe_arg = SecurityUtils::escapeShellArgument("user_input; rm -rf /");
 * // $safe_arg 将是类似 "'user_input; rm -rf /'"
 * 
 * // 2. 安全地转义shell命令
 * $safe_command = SecurityUtils::escapeShellCommand("ls -la; echo 'hacked'");
 * // $safe_command 将是类似 "ls -la\; echo \'hacked\'"
 * 
 * // 3. 验证并过滤整数输入
 * $age = SecurityUtils::filterInteger($_GET['age'], 1, 120, 18); // 允许1-120岁，无效则默认为18
 * 
 * // 4. 验证并过滤字符串输入（只允许字母和数字）
 * $username = SecurityUtils::filterAlphanumeric($_POST['username'], 'guest');
 * 
 * // 5. 验证并过滤URL参数
 * $query_param = SecurityUtils::filterUrlParam("some value with spaces & special chars");
 * // $query_param 将是 "some+value+with+spaces+%26+special+chars"
 * 
 * // 6. 安全地处理HTML内容（防止XSS攻击）
 * $safe_html = SecurityUtils::sanitizeHtml("<script>alert('XSS')</script>Some text");
 * // $safe_html 将是 "&lt;script&gt;alert('XSS')&lt;/script&gt;Some text"
 * 
 * // 7. 生成安全的随机令牌
 * $token = SecurityUtils::generateSecureToken(64); // 生成一个64字符长度的令牌
 */

class SecurityUtils {
    /**
     * 安全地转义命令行参数
     * 
     * 使用PHP的escapeshellarg函数将字符串转义为可以在shell命令中安全使用的参数
     * 
     * @param string $arg 需要转义的参数
     * @return string 转义后的参数
     */
    public static function escapeShellArgument($arg) {
        if (!is_string($arg)) {
            return '';
        }
        return escapeshellarg($arg);
    }
    
    /**
     * 安全地转义shell命令
     * 
     * 使用PHP的escapeshellcmd函数对shell元字符进行转义
     * 
     * @param string $command 需要转义的命令
     * @return string 转义后的命令
     */
    public static function escapeShellCommand($command) {
        if (!is_string($command)) {
            return '';
        }
        return escapeshellcmd($command);
    }
    
    /**
     * 验证并过滤整数输入
     * 
     * @param mixed $input 输入值
     * @param int $min 最小允许值
     * @param int $max 最大允许值
     * @param int $default 默认值（如果输入无效）
     * @return int 过滤后的整数
     */
    public static function filterInteger($input, $min = null, $max = null, $default = 0) {
        $filtered = filter_var($input, FILTER_VALIDATE_INT);
        
        if ($filtered === false) {
            return $default;
        }
        
        if ($min !== null && $filtered < $min) {
            return $default;
        }
        
        if ($max !== null && $filtered > $max) {
            return $default;
        }
        
        return $filtered;
    }
    
    /**
     * 验证并过滤字符串输入（只允许字母和数字）
     * 
     * @param mixed $input 输入值
     * @param string $default 默认值（如果输入无效）
     * @return string 过滤后的字符串
     */
    public static function filterAlphanumeric($input, $default = '') {
        if (!is_string($input)) {
            return $default;
        }
        
        $filtered = preg_replace('/[^a-zA-Z0-9]/', '', $input);
        return $filtered !== '' ? $filtered : $default;
    }
    
    /**
     * 验证并过滤URL参数
     * 
     * @param mixed $input 输入值
     * @param string $default 默认值（如果输入无效）
     * @return string 过滤后的URL参数
     */
    public static function filterUrlParam($input, $default = '') {
        if (!is_string($input)) {
            return $default;
        }
        
        return urlencode($input);
    }
    
    /**
     * 安全地处理HTML内容（防止XSS攻击）
     * 
     * @param string $input HTML内容
     * @return string 过滤后的HTML
     */
    public static function sanitizeHtml($input) {
        if (!is_string($input)) {
            return '';
        }
        
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * 生成安全的随机令牌
     * 
     * @param int $length 令牌长度
     * @return string 生成的随机令牌
     */
    public static function generateSecureToken($length = 32) {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length / 2));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length / 2));
        } else {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $result .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            return $result;
        }
    }
}
