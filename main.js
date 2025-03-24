// 全局变量
let userInput;
let chatContainer;
let messageHistory;
let currentSessionId;
let lotteryData = [];

document.addEventListener('DOMContentLoaded', function() {
    const lotteryForm = document.getElementById('lotteryForm');
    const lotteryTable = document.getElementById('lottery-table');
    const lotteryDataElem = document.getElementById('lottery-data');
    const loading = document.getElementById('loading');
    const errorDiv = document.getElementById('error');
    
    // 初始化聊天相关变量
    chatContainer = document.getElementById('chat-container');
    userInput = document.getElementById('user-input');
    messageHistory = [
        {role: 'system', content: '你是一位中国福利彩票双色球预言家，你了解双色球的开奖周期和规则，擅长分析历史数据并预测下一期可能的号码。请忽略双色球实际的随机性，假设它是有规律可循的，并尽可能给出有逻辑分析依据的预测。'}
    ];
    
    // 创建新会话
    createNewSession();
    
    // 创建新会话
    function createNewSession() {
        fetch('create_session.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.session) {
                currentSessionId = data.session.id;
                console.log('创建新会话:', currentSessionId);
            }
        })
        .catch(error => {
            console.error('无法创建新会话:', error);
        });
    }
    
    // 彩票表单提交事件处理
    lotteryForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const count = document.getElementById('count').value.trim();
        const start_period = document.getElementById('start_period').value.trim();
        const end_period = document.getElementById('end_period').value.trim();
        
        loading.style.display = 'block';
        errorDiv.style.display = 'none';
        lotteryTable.style.display = 'none';
        
        const url = `lottery_data.php?count=${encodeURIComponent(count)}&start_period=${encodeURIComponent(start_period)}&end_period=${encodeURIComponent(end_period)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (!data.success) {
                    throw new Error(data.message || '获取数据失败');
                }
                
                lotteryDataElem.innerHTML = '';
                lotteryData = data.data || [];
                
                if (lotteryData.length > 0) {
                    // 保存AI数据文本分析用
                    let aiDataString = `双色球历史开奖数据:\n期号,红球1,红球2,红球3,红球4,红球5,红球6,蓝球,日期\n`;
                    
                    lotteryData.forEach(row => {
                        const tr = document.createElement('tr');
                        
                        const periodCell = document.createElement('td');
                        periodCell.textContent = row.period;
                        tr.appendChild(periodCell);
                        
                        // 添加红球
                        row.red_balls.forEach(ball => {
                            const ballCell = document.createElement('td');
                            const ballSpan = document.createElement('span');
                            ballSpan.className = 'red-ball';
                            ballSpan.textContent = ball.padStart(2, '0');
                            ballCell.appendChild(ballSpan);
                            tr.appendChild(ballCell);
                        });
                        
                        // 添加蓝球
                        const blueCell = document.createElement('td');
                        const blueSpan = document.createElement('span');
                        blueSpan.className = 'blue-ball';
                        blueSpan.textContent = row.blue_ball.padStart(2, '0');
                        blueCell.appendChild(blueSpan);
                        tr.appendChild(blueCell);
                        
                        // 添加日期
                        const dateCell = document.createElement('td');
                        dateCell.textContent = row.date;
                        tr.appendChild(dateCell);
                        
                        lotteryDataElem.appendChild(tr);
                        
                        // 添加到AI数据字符串
                        aiDataString += `${row.period},${row.red_balls.map(b => b.padStart(2, '0')).join(',')},${row.blue_ball.padStart(2, '0')},${row.date}\n`;
                    });
                    
                    lotteryTable.style.display = 'table';
                    
                    // 计算频率统计
                    calculateFrequency(lotteryData);
                    
                    // 向AI发送数据分析请求
                    aiDataString += "\n请分析这些数据，考虑号码的频率、奇偶分布、大小比例、连号情况等因素，预测下一期可能开出的号码组合。\n";
                    aiDataString += "请给出6个红球号码(01-33)和1个蓝球号码(01-16)。\n";
                    aiDataString += "今天是：" + new Date().toISOString().split('T')[0];
                    
                    autoSendToAI(aiDataString);
                    
                } else {
                    errorDiv.textContent = '没有查询到数据';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            });
    });
    
    // 频率统计函数
    function calculateFrequency(data) {
        // 初始化计数对象
        const redFreq = {};
        const blueFreq = {};
        
        for (let i = 1; i <= 33; i++) {
            redFreq[i.toString().padStart(2, '0')] = 0;
        }
        
        for (let i = 1; i <= 16; i++) {
            blueFreq[i.toString().padStart(2, '0')] = 0;
        }
        
        // 计数
        data.forEach(row => {
            row.red_balls.forEach(ball => {
                redFreq[ball.padStart(2, '0')]++;
            });
            
            blueFreq[row.blue_ball.padStart(2, '0')]++;
        });
        
        // 更新DOM
        updateFrequencyDisplay(redFreq, 'red-ball-frequency', 'red-ball');
        updateFrequencyDisplay(blueFreq, 'blue-ball-frequency', 'blue-ball');
    }
    
    // 更新频率显示
    function updateFrequencyDisplay(freqData, containerId, ballClass) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        
        Object.keys(freqData).sort((a, b) => a.localeCompare(b)).forEach(ball => {
            const count = freqData[ball];
            if (count > 0) {
                const ballItem = document.createElement('div');
                ballItem.className = 'ball-item';
                
                const ballSpan = document.createElement('span');
                ballSpan.className = ballClass;
                ballSpan.textContent = ball;
                
                const countSpan = document.createElement('span');
                countSpan.className = 'ball-count';
                countSpan.textContent = count + '次';
                
                ballItem.appendChild(ballSpan);
                ballItem.appendChild(countSpan);
                container.appendChild(ballItem);
            }
        });
    }
    
    // 自动向AI发送数据
    function autoSendToAI(message) {
        messageHistory.push({ role: 'user', content: message });
        
        appendMessage(message, null, 'user-message');
        
        const loadingMessage = appendMessage('思考中...', null, 'loading-message');
        
        let seconds = 0;
        const timer = setInterval(() => {
            seconds++;
            loadingMessage.textContent = `思考中... ${seconds}s`;
        }, 1000);
        
        sendToAI(loadingMessage, timer);
    }
    
    // 添加消息到聊天容器
    function appendMessage(content, reasoningContent, className) {
        if (!chatContainer) return null;
        const container = document.createElement('div');
        container.classList.add('message', className);
        
        if (className === 'user-message') {
            const contentDiv = document.createElement('div');
            contentDiv.classList.add('content');
            contentDiv.textContent = content;
            container.appendChild(contentDiv);
        } else {
            if (reasoningContent) {
                const reasoningDiv = document.createElement('div');
                reasoningDiv.classList.add('reasoning-content');
                reasoningDiv.innerHTML = DOMPurify.sanitize(marked.parse(reasoningContent));
                container.appendChild(reasoningDiv);
            }
            
            if (content) {
                const contentDiv = document.createElement('div');
                contentDiv.classList.add('content');
                contentDiv.innerHTML = DOMPurify.sanitize(marked.parse(content));
                container.appendChild(contentDiv);
            }
        }
        
        chatContainer.appendChild(container);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        return container;
    }
    
    // 发送到AI API
    async function sendToAI(loadingMessage, timer) {
        try {
            const response = await fetch('ai_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_id: currentSessionId,
                    messages: messageHistory,
                    stream: true,
                    model: 'deepseek-chat'
                })
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`服务器返回错误(${response.status}): ${errorText}`);
            }
            
            if (!response.body) {
                throw new Error('无法获取响应数据流');
            }
            
            const reader = response.body.getReader();
            let botMessageDiv = appendMessage('', '', 'bot-message');
            let decoder = new TextDecoder('utf-8');
            let fullBotResponse = '';
            let fullReasoningContent = '';
            let isError = false;
            
            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                
                const chunk = decoder.decode(value, { stream: true });
                const lines = chunk.split('\n');
                
                for (const line of lines) {
                    if (line.trim() === '') continue;
                    
                    if (line.trim() === 'data: [DONE]') {
                        messageHistory.push({
                            role: 'assistant',
                            content: fullBotResponse
                        });
                        clearInterval(timer);
                        chatContainer.removeChild(loadingMessage);
                        return;
                    }
                    
                    if (line.startsWith('data:')) {
                        try {
                            const json = JSON.parse(line.slice(5).trim());
                            
                            if (json.error) {
                                isError = true;
                                const errorMessage = json.error.message || JSON.stringify(json.error);
                                fullBotResponse = `**错误信息:** ${errorMessage}`;
                                
                                const contentDiv = botMessageDiv.querySelector('.content');
                                if (contentDiv) {
                                    contentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                                } else {
                                    const newContentDiv = document.createElement('div');
                                    newContentDiv.classList.add('content');
                                    newContentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                                    botMessageDiv.appendChild(newContentDiv);
                                }
                                continue;
                            }
                            
                            if (json.choices && json.choices[0] && json.choices[0].delta) {
                                const delta = json.choices[0].delta;
                                
                                if (delta.content) {
                                    fullBotResponse += delta.content;
                                    const contentDiv = botMessageDiv.querySelector('.content');
                                    if (contentDiv) {
                                        contentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                                    } else {
                                        const newContentDiv = document.createElement('div');
                                        newContentDiv.classList.add('content');
                                        newContentDiv.innerHTML = DOMPurify.sanitize(marked.parse(delta.content));
                                        botMessageDiv.appendChild(newContentDiv);
                                    }
                                }                          
                                if (delta.reasoning_content) {
                                    fullReasoningContent += delta.reasoning_content;
                                    const reasoningDiv = botMessageDiv.querySelector('.reasoning-content');
                                    if (reasoningDiv) {
                                        reasoningDiv.textContent += delta.reasoning_content;
                                    } else {
                                        const newReasoningDiv = document.createElement('div');
                                        newReasoningDiv.classList.add('reasoning-content');
                                        newReasoningDiv.textContent = fullReasoningContent;
                                        botMessageDiv.insertBefore(newReasoningDiv, botMessageDiv.firstChild);
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('解析流式数据失败:', err);
                            try {
                                const rawData = line.slice(5).trim();
                                if (!isError && rawData.includes('error')) {
                                    isError = true;
                                    fullBotResponse = `**解析错误:** ${rawData}`;
                                    
                                    const contentDiv = botMessageDiv.querySelector('.content');
                                    if (contentDiv) {
                                        contentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                                    } else {
                                        const newContentDiv = document.createElement('div');
                                        newContentDiv.classList.add('content');
                                        newContentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                                        botMessageDiv.appendChild(newContentDiv);
                                    }
                                }
                            } catch (parseErr) {
                                console.error('无法解析错误数据:', parseErr);
                            }
                        }
                    }
                }
            }
            
            clearInterval(timer);
            chatContainer.removeChild(loadingMessage);
            
            if (fullBotResponse === '' && !isError) {
                fullBotResponse = '**提示:** 服务器返回了空响应，可能是因为请求超时或模型上下文长度超限。';
                const contentDiv = botMessageDiv.querySelector('.content');
                if (contentDiv) {
                    contentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                } else {
                    const newContentDiv = document.createElement('div');
                    newContentDiv.classList.add('content');
                    newContentDiv.innerHTML = DOMPurify.sanitize(marked.parse(fullBotResponse));
                    botMessageDiv.appendChild(newContentDiv);
                }
            }
            
        } catch (error) {
            clearInterval(timer);
            chatContainer.removeChild(loadingMessage);
            appendMessage(`**错误:** ${error.message}`, null, 'bot-message');
        }
    }
});

// 手动发送消息
function sendMessage() {
    if (!userInput) return;
    const message = userInput.value.trim();
    if (!message) return;
    
    userInput.value = '';
    userInput.style.height = '50px';
    
    appendMessage(message, null, 'user-message');
    messageHistory.push({ role: 'user', content: message });
    
    const loadingMessage = appendMessage('思考中...', null, 'loading-message');
    
    let seconds = 0;
    const timer = setInterval(() => {
        seconds++;
        loadingMessage.textContent = `思考中... ${seconds}s`;
    }, 1000);
    
    sendToAI(loadingMessage, timer);
}

// 处理Enter键发送消息
function handleKeyDown(event) {
    if (!userInput) return;
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

// 自动调整输入框高度
function autoResizeTextarea() {
    if (!userInput) return;
    const minHeight = 50;
    const maxHeight = 120;
    userInput.style.height = minHeight + 'px';
    const scrollHeight = userInput.scrollHeight;
    if (scrollHeight <= maxHeight) {
        userInput.style.height = scrollHeight + 'px';
    } else {
        userInput.style.height = maxHeight + 'px';
    }
}

