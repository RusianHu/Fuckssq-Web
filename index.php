<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>老司机双色球预测</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>老司机双色球预测⛹️</h1>
            <p>D指导分析历史数据</p>
        </header>
        
        <main>
            <section class="query-form">
                <h2>历史数据查询</h2>
                <form id="lotteryForm">
                    <div class="form-group">
                        <label for="count">查询期数:</label>
                        <select id="count" name="count">
                            <option value="10">最近10期</option>
                            <option value="30">最近30期</option>
                            <option value="50">最近50期</option>
                            <option value="100" selected>最近100期</option>
                            <option value="1000">最近1000期</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="start_period">起始期号 (可选):</label>
                        <input type="text" id="start_period" name="start_period" placeholder="例如: 25030">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_period">结束期号 (可选):</label>
                        <input type="text" id="end_period" name="end_period" placeholder="例如: 24982">
                    </div>
                    
                    <button type="submit" class="btn-submit">查询数据</button>
                </form>
            </section>
            
            <section class="results">
                <h2>查询结果</h2>
                <div id="loading" style="display: none;">加载中...</div>
                <div id="error" class="error" style="display: none;"></div>
                
                <div id="data-container">
                    <table id="lottery-table" style="display: none;">
                        <thead>
                            <tr>
                                <th>期号</th>
                                <th colspan="6">红球号码</th>
                                <th>蓝球</th>
                                <th>开奖日期</th>
                            </tr>
                        </thead>
                        <tbody id="lottery-data">
                        </tbody>
                    </table>
                </div>
            </section>
                        
            <section class="statistics">
                <h2>数据统计</h2>
                <div class="stat-container">
                    <div class="stat-card">
                        <h3>红球出现频率</h3>
                        <div id="red-ball-frequency" class="ball-frequency">
                            <!-- 动态生成 -->
                        </div>
                    </div>
                    <div class="stat-card">
                        <h3>蓝球出现频率</h3>
                        <div id="blue-ball-frequency" class="ball-frequency">
                            <!-- 动态生成 -->
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <div id="red-chart" class="chart"></div>
                    <div id="blue-chart" class="chart"></div>
                </div>
            </section>
			
            <!-- AI咨询区域 -->
            <section class="ai-consultant">
                <h2>AI智能预测</h2>
                <div id="chat-container"></div>
                <div id="input-container">
                    <textarea id="user-input" placeholder="输入您的问题..." onkeydown="handleKeyDown(event)" oninput="autoResizeTextarea()"></textarea>
                    <button id="send-button" onclick="sendMessage()">发送</button>
                </div>
            </section>
			
        </main>
        
        <footer>
            <p>© <?php echo date('Y'); ?> 老司机双色球预测 | 仅供娱乐，不构成投注建议</p>
        </footer>
    </div>
    
    <script src="main.js"></script>
</body>
</html>
