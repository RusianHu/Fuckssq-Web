# 老司机双色球预测系统

![双色球预测](https://img.shields.io/badge/双色球-预测系统-red)
![版本](https://img.shields.io/badge/版本-1.0-blue)
![语言](https://img.shields.io/badge/语言-PHP/JavaScript-yellow)

## 📋 项目介绍

老司机双色球预测系统是一个基于历史数据分析和AI人工智能的双色球彩票预测网站。本系统通过收集历史开奖数据，结合高级数据分析算法和人工智能技术，为用户提供双色球号码的智能预测服务。

> ⚠️ **免责声明**：本系统仅供娱乐和研究，不构成任何投注建议。彩票有风险，投注需谨慎。

## ✨ 主要功能

- **📊 历史数据查询**：支持查询最近10-1000期的双色球开奖数据
- **🔍 期号范围搜索**：可按指定期号范围查询历史数据
- **📈 数据统计分析**：展示红球和蓝球出现频率的统计图表
- **🤖 AI智能预测**：基于DeepSeek AI的智能号码预测与分析
- **💬 交互式咨询**：用户可与AI助手进行实时对话，获取个性化预测

## 🛠️ 技术架构

- **前端**：HTML5、CSS3、JavaScript
- **后端**：PHP
- **AI接口**：DeepSeek AI API
- **数据源**：福彩开奖数据

## 🔧 安装部署

### 系统要求

- PHP 7.0+
- Web服务器（Apache/Nginx）
- HTTPS支持（用于安全API调用）

### 安装步骤

1. 克隆本仓库到您的Web服务器目录：

```bash
git clone https://github.com/yourusername/lottery-prediction.git
```

2. 确保Web服务器用户对项目目录有读写权限

3. 配置Web服务器指向项目目录

4. 访问网站首页，开始使用

## 🚀 使用指南

### 查询历史数据

1. 在"历史数据查询"区域，选择要查询的期数（10-1000期）
2. 可选：设置起始和结束期号
3. 点击"查询数据"按钮
4. 查看结果表格和频率统计

### 获取AI预测

1. 在历史数据加载后，系统会自动向AI请求预测分析
2. 在"AI智能预测"区域查看预测结果
3. 您也可以在聊天框中输入具体问题，如：
   - "分析最近热门号码"
   - "预测下一期的红球组合"
   - "蓝球走势如何？"

## ⚙️ 配置说明

### API密钥配置

在`ai_api.php`文件中配置您的DeepSeek AI API密钥：

```php
$apiKeys = [
    'your-api-key-here',  // 替换为您的实际API密钥
    'your-backup-key-here'  // 备用密钥
];
```

## 🔍 项目结构

```
├── index.php           # 主页面
├── style.css           # 样式表
├── main.js             # 前端脚本
├── ai_api.php          # AI接口
├── lottery_data.php    # 彩票数据获取
└── create_session.php  # 会话管理
```

## 📝 更新日志

### 版本 1.0
- 初始版本发布
- 支持历史数据查询和显示
- 支持AI预测功能
- 频率统计图表展示

## 📜 许可证

本项目采用 MIT 许可证 - 详情请查看 [LICENSE](LICENSE) 文件

## 👨‍💻 贡献

欢迎提交问题和改进建议！您可以通过以下方式参与：

1. Fork 本仓库
2. 创建您的特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交您的更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启一个 Pull Request

---

❤️ 希望这个工具能为您带来一些乐趣和思考！