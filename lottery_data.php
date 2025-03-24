<?php
header('Content-Type: application/json');

// 设置错误处理
function returnError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// 获取参数
$count = isset($_GET['count']) ? intval($_GET['count']) : 100;
$start_period = isset($_GET['start_period']) ? trim($_GET['start_period']) : '';
$end_period = isset($_GET['end_period']) ? trim($_GET['end_period']) : '';

// 验证参数
if ($count <= 0 || $count > 1000) {
    returnError('期数参数无效，应在1-1000之间');
}

// 构建抓取URL
$url = "https://datachart.500.com/ssq/history/newinc/history.php?limit=$count&sort=0";

// 如果提供了期号范围，则添加
if (!empty($start_period) && !empty($end_period)) {
    $url .= "&start=$start_period&end=$end_period";
}

// 获取网页内容
$html = @file_get_contents($url);
if ($html === false) {
    returnError('无法获取数据，请检查网络连接');
}

// 提取数据
$pattern = '/<tr class=\"t_tr1\">.*?<td>(\\d+)<\\/td>\\s*<td class=\"t_cfont2\">(\\d+)<\\/td>\\s*<td class=\"t_cfont2\">(\\d+)<\\/td>\\s*<td class=\"t_cfont2\">(\\d+)<\\/td>\\s*<td class=\"t_cfont2\">(\\d+)<\\/td>\\s*<td class=\"t_cfont2\">(\\d+)<\\/td>\\s*<td class=\"t_cfont2\">(\\d+)<\\/td>\\s*<td class=\"t_cfont4\">(\\d+)<\\/td>.*?<td>(\\d{4}-\\d{2}-\\d{2})<\\/td>\\s*<\\/tr>/s';

preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

if (empty($matches)) {
    returnError('未找到匹配的数据');
}

// 格式化数据
$results = [];
foreach ($matches as $match) {
    $results[] = [
        'period' => $match[1],
        'red_balls' => [
            $match[2],
            $match[3],
            $match[4],
            $match[5],
            $match[6],
            $match[7]
        ],
        'blue_ball' => $match[8],
        'date' => $match[9]
    ];
}

// 返回结果
echo json_encode([
    'success' => true,
    'data' => $results
]);
