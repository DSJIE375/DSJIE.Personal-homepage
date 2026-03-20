<?php
// 日志解释器页面 - log_explainer.php
header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>访问日志解释器</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .content {
            padding: 25px 30px;
        }
        
        .section {
            margin-bottom: 30px;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .section h2 {
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }
        
        .info-card h3 {
            color: #495057;
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-card h3 i {
            color: #667eea;
        }
        
        .info-card p {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .log-display {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            overflow-x: auto;
        }
        
        .log-entry {
            background: white;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            position: relative;
        }
        
        .log-entry:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .log-fields {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .field {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 13px;
            color: #495057;
        }
        
        .field-name {
            font-weight: bold;
            color: #667eea;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background: #667eea;
            color: white;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
        }
        
        td {
            padding: 10px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .device-icon {
            display: inline-block;
            width: 20px;
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin: 0 2px;
        }
        
        .badge-desktop { background: #cfe2ff; color: #084298; }
        .badge-mobile { background: #d1e7dd; color: #0a3622; }
        .badge-bot { background: #f8d7da; color: #842029; }
        .badge-unknown { background: #e2e3e5; color: #41464b; }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        
        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> 访问日志</h1>
            <p>分析您的 access_log.log 文件内容</p>
        </div>
        
        <div class="content">
            <?php
            // 日志文件路径
            $log_file = "../access_log.log";
            
            // 检查日志文件是否存在
            if (!file_exists($log_file)) {
                echo '<div class="empty-state">';
                echo '<i class="fas fa-file-excel"></i>';
                echo '<h3>未找到日志文件</h3>';
                echo '<p>当前目录下没有找到 access_log.log 文件。</p>';
                echo '<p>请确保日志文件与当前页面在同一目录下。</p>';
                echo '</div>';
            } else {
                // 读取日志文件
                $log_content = file_get_contents($log_file);
                $log_lines = explode("\n", trim($log_content));
                
                // 反转数组，使最新的日志在最前面
                $log_lines = array_reverse($log_lines);
                
                // 统计信息
                $total_entries = 0;
                $unique_ips = [];
                $device_counts = [
                    'desktop' => 0,
                    'mobile' => 0,
                    'bot' => 0,
                    'unknown' => 0
                ];
                $method_counts = [
                    'GET' => 0,
                    'POST' => 0,
                    'OTHER' => 0
                ];
                
                // 处理每一行日志
                $parsed_logs = [];
                
                foreach ($log_lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    $total_entries++;
                    
                    // 解析日志行
                    $parts = explode("|", trim($line));
                    
                    if (count($parts) >= 7) {
                        // 新格式: IP地址|地理位置|时间|请求方法|访问页面|用户代理|来源页面
                        $ip = $parts[0];
                        $location = $parts[1];
                        $time = $parts[2];
                        $method = $parts[3];
                        $page = $parts[4];
                        $user_agent = isset($parts[5]) ? $parts[5] : '';
                        $referer = isset($parts[6]) ? $parts[6] : '';
                    } else {
                        // 旧格式: 时间|请求方法|访问页面|用户代理|来源页面
                        $ip = '未知';
                        $location = '未知';
                        $time = isset($parts[0]) ? $parts[0] : '';
                        $method = isset($parts[1]) ? $parts[1] : '';
                        $page = isset($parts[2]) ? $parts[2] : '';
                        $user_agent = isset($parts[3]) ? $parts[3] : '';
                        $referer = isset($parts[4]) ? $parts[4] : '';
                    }
                    
                    // 记录唯一IP
                    if ($ip !== '未知' && !in_array($ip, $unique_ips)) {
                        $unique_ips[] = $ip;
                    }
                    
                    // 统计请求方法
                    if ($method === 'GET') {
                        $method_counts['GET']++;
                    } elseif ($method === 'POST') {
                        $method_counts['POST']++;
                    } else {
                        $method_counts['OTHER']++;
                    }
                    
                    // 分析设备类型
                    $device_type = 'unknown';
                    $device_icon = 'fas fa-question-circle';
                    
                    if (stripos($user_agent, 'Mobile') !== false || 
                        stripos($user_agent, 'Android') !== false || 
                        stripos($user_agent, 'iPhone') !== false) {
                        $device_type = 'mobile';
                        $device_icon = 'fas fa-mobile-alt';
                        $device_counts['mobile']++;
                    } elseif (stripos($user_agent, 'bot') !== false || 
                              stripos($user_agent, 'crawler') !== false || 
                              stripos($user_agent, 'spider') !== false) {
                        $device_type = 'bot';
                        $device_icon = 'fas fa-robot';
                        $device_counts['bot']++;
                    } elseif (!empty($user_agent)) {
                        $device_type = 'desktop';
                        $device_icon = 'fas fa-desktop';
                        $device_counts['desktop']++;
                    } else {
                        $device_counts['unknown']++;
                    }
                    
                    // 解析浏览器信息
                    $browser = '未知';
                    if (stripos($user_agent, 'Chrome') !== false && stripos($user_agent, 'Edg') === false) {
                        $browser = 'Chrome';
                    } elseif (stripos($user_agent, 'Edg') !== false) {
                        $browser = 'Microsoft Edge';
                    } elseif (stripos($user_agent, 'Firefox') !== false) {
                        $browser = 'Firefox';
                    } elseif (stripos($user_agent, 'Safari') !== false && stripos($user_agent, 'Chrome') === false) {
                        $browser = 'Safari';
                    } elseif (stripos($user_agent, 'MSIE') !== false || stripos($user_agent, 'Trident') !== false) {
                        $browser = 'Internet Explorer';
                    } elseif (stripos($user_agent, 'Opera') !== false) {
                        $browser = 'Opera';
                    }
                    
                    // 解析操作系统
                    $os = '未知';
                    if (stripos($user_agent, 'Windows') !== false) {
                        $os = 'Windows';
                    } elseif (stripos($user_agent, 'Mac OS') !== false || stripos($user_agent, 'Macintosh') !== false) {
                        $os = 'macOS';
                    } elseif (stripos($user_agent, 'Linux') !== false && stripos($user_agent, 'Android') === false) {
                        $os = 'Linux';
                    } elseif (stripos($user_agent, 'Android') !== false) {
                        $os = 'Android';
                    } elseif (stripos($user_agent, 'iPhone') !== false || stripos($user_agent, 'iPad') !== false) {
                        $os = 'iOS';
                    }
                    
                    // 保存解析后的日志
                    $parsed_logs[] = [
                        'ip' => $ip,
                        'location' => $location,
                        'time' => $time,
                        'method' => $method,
                        'page' => $page,
                        'user_agent' => $user_agent,
                        'referer' => $referer,
                        'device_type' => $device_type,
                        'device_icon' => $device_icon,
                        'browser' => $browser,
                        'os' => $os
                    ];
                }
                
                // 显示统计信息
                echo '<div class="section">';
                echo '<h2><i class="fas fa-chart-bar"></i> 访问统计</h2>';
                echo '<div class="info-grid">';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-list-ol"></i> 总访问次数</h3>';
                echo '<p>' . $total_entries . ' 次</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-user-friends"></i> 独立访客数</h3>';
                echo '<p>' . count($unique_ips) . ' 个</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-laptop"></i> 桌面设备</h3>';
                echo '<p>' . $device_counts['desktop'] . ' 次 (' . round($device_counts['desktop'] / max($total_entries, 1) * 100, 1) . '%)</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-mobile-alt"></i> 移动设备</h3>';
                echo '<p>' . $device_counts['mobile'] . ' 次 (' . round($device_counts['mobile'] / max($total_entries, 1) * 100, 1) . '%)</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-robot"></i> 机器人/爬虫</h3>';
                echo '<p>' . $device_counts['bot'] . ' 次 (' . round($device_counts['bot'] / max($total_entries, 1) * 100, 1) . '%)</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-question-circle"></i> 未知设备</h3>';
                echo '<p>' . $device_counts['unknown'] . ' 次 (' . round($device_counts['unknown'] / max($total_entries, 1) * 100, 1) . '%)</p>';
                echo '</div>';
                
                echo '</div>';
                
                // 请求方法统计
                echo '<div style="margin-top: 20px;">';
                echo '<h3 style="color: #495057; margin-bottom: 10px;">请求方法统计</h3>';
                echo '<div style="display: flex; gap: 10px;">';
                echo '<span class="badge" style="background: #d1e7dd; color: #0a3622;">GET: ' . $method_counts['GET'] . ' 次</span>';
                echo '<span class="badge" style="background: #cfe2ff; color: #084298;">POST: ' . $method_counts['POST'] . ' 次</span>';
                if ($method_counts['OTHER'] > 0) {
                    echo '<span class="badge" style="background: #f8d7da; color: #842029;">其他: ' . $method_counts['OTHER'] . ' 次</span>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // 显示日志解释
                echo '<div class="section">';
                echo '<h2><i class="fas fa-info-circle"></i> 字段含义解释</h2>';
                echo '<div class="info-grid">';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-network-wired"></i> IP地址</h3>';
                echo '<p>访问者的IP地址，用于标识网络上的设备。格式如: 192.168.1.1</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-map-marker-alt"></i> 地理位置</h3>';
                echo '<p>根据IP地址解析出的地理位置信息，如城市、国家等</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-clock"></i> 访问时间</h3>';
                echo '<p>访问发生的具体时间，格式: 年-月-日 时:分:秒</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-exchange-alt"></i> 请求方法</h3>';
                echo '<p>HTTP请求方法，常见的有GET(获取数据)和POST(提交数据)</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-file"></i> 访问页面</h3>';
                echo '<p>用户请求的具体页面或资源路径，如/index.php或/product/123</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-user-secret"></i> 用户代理</h3>';
                echo '<p>客户端浏览器/设备的标识信息，包含浏览器类型、版本、操作系统等</p>';
                echo '</div>';
                
                echo '<div class="info-card">';
                echo '<h3><i class="fas fa-external-link-alt"></i> 来源页面</h3>';
                echo '<p>用户是从哪个页面跳转过来的，如果是直接输入网址访问则显示"Direct Access"</p>';
                echo '</div>';
                
                echo '</div>';
                echo '</div>';
                
                // 显示日志详情
                echo '<div class="section">';
                echo '<h2><i class="fas fa-history"></i> 访问日志详情</h2>';
                echo '<p>共找到 ' . $total_entries . ' 条访问记录，按时间倒序排列（最新的在最前面）</p>';
                
                if ($total_entries > 0) {
                    echo '<div class="btn-group">';
                    echo '<button class="btn" onclick="toggleAllDetails()"><i class="fas fa-eye"></i> 展开/收起所有详情</button>';
                    echo '<a href="' . $log_file . '" class="btn btn-secondary" download><i class="fas fa-download"></i> 下载原始日志</a>';
                    echo '</div>';
                    
                    echo '<div class="table-container">';
                    echo '<table>';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>时间</th>';
                    echo '<th>IP地址</th>';
                    echo '<th>位置</th>';
                    echo '<th>设备</th>';
                    echo '<th>浏览器/系统</th>';
                    echo '<th>访问页面</th>';
                    echo '<th>操作</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    foreach ($parsed_logs as $index => $log) {
                        $badge_class = 'badge-' . $log['device_type'];
                        
                        echo '<tr>';
                        echo '<td>' . $log['time'] . '</td>';
                        echo '<td>' . $log['ip'] . '</td>';
                        echo '<td>' . $log['location'] . '</td>';
                        echo '<td><span class="badge ' . $badge_class . '"><i class="' . $log['device_icon'] . '"></i> ' . ucfirst($log['device_type']) . '</span></td>';
                        echo '<td>' . $log['browser'] . ' / ' . $log['os'] . '</td>';
                        echo '<td><span style="font-family: monospace; font-size: 12px;">' . htmlspecialchars(substr($log['page'], 0, 30)) . (strlen($log['page']) > 30 ? '...' : '') . '</span></td>';
                        echo '<td><button class="btn" style="padding: 5px 10px; font-size: 12px;" onclick="toggleDetails(' . $index . ')"><i class="fas fa-info-circle"></i> 详情</button></td>';
                        echo '</tr>';
                        
                        // 详情行
                        echo '<tr id="details-' . $index . '" style="display: none; background: #f8f9fa;">';
                        echo '<td colspan="7">';
                        echo '<div style="padding: 15px;">';
                        echo '<h4 style="margin-bottom: 10px; color: #495057;">日志详情 #' . ($index + 1) . '</h4>';
                        echo '<div class="log-fields">';
                        echo '<div class="field"><span class="field-name">IP地址:</span> ' . $log['ip'] . '</div>';
                        echo '<div class="field"><span class="field-name">地理位置:</span> ' . $log['location'] . '</div>';
                        echo '<div class="field"><span class="field-name">访问时间:</span> ' . $log['time'] . '</div>';
                        echo '<div class="field"><span class="field-name">请求方法:</span> ' . $log['method'] . '</div>';
                        echo '<div class="field"><span class="field-name">访问页面:</span> ' . htmlspecialchars($log['page']) . '</div>';
                        echo '<div class="field"><span class="field-name">来源页面:</span> ' . ($log['referer'] == 'Direct Access' ? '<span style="color: #6c757d;">直接访问</span>' : htmlspecialchars($log['referer'])) . '</div>';
                        echo '</div>';
                        
                        echo '<div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 5px; font-family: monospace; font-size: 12px; word-break: break-all;">';
                        echo '<strong>用户代理:</strong><br>';
                        echo htmlspecialchars($log['user_agent']);
                        echo '</div>';
                        
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="empty-state">';
                    echo '<i class="fas fa-file-excel"></i>';
                    echo '<h3>日志文件为空</h3>';
                    echo '<p>日志文件中没有找到任何访问记录。</p>';
                    echo '</div>';
                }
                
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="footer">
            <p><i class="fas fa-code"></i> 访问日志解释器 | 基于PHP日志系统生成 | 最后更新: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p style="margin-top: 5px; font-size: 12px;">日志格式: IP地址 | 地理位置 | 时间 | 请求方法 | 访问页面 | 用户代理 | 来源页面</p>
        </div>
    </div>
    
    <script>
        // 切换单条日志的详细信息显示/隐藏
        function toggleDetails(index) {
            const detailsRow = document.getElementById('details-' + index);
            if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
                detailsRow.style.display = 'table-row';
            } else {
                detailsRow.style.display = 'none';
            }
        }
        
        // 切换所有日志的详细信息显示/隐藏
        function toggleAllDetails() {
            const detailsRows = document.querySelectorAll('tr[id^="details-"]');
            const allHidden = Array.from(detailsRows).every(row => row.style.display === 'none' || row.style.display === '');
            
            detailsRows.forEach(row => {
                row.style.display = allHidden ? 'table-row' : 'none';
            });
            
            const button = document.querySelector('button[onclick="toggleAllDetails()"]');
            button.innerHTML = allHidden ? 
                '<i class="fas fa-eye-slash"></i> 收起所有详情' : 
                '<i class="fas fa-eye"></i> 展开所有详情';
        }
        
        // 页面加载完成后执行
        document.addEventListener('DOMContentLoaded', function() {
            // 自动高亮最近的5条日志
            const rows = document.querySelectorAll('tbody tr');
            for (let i = 0; i < Math.min(5, rows.length); i++) {
                if (!rows[i].id.startsWith('details-')) {
                    rows[i].style.backgroundColor = '#f0f7ff';
                }
            }
        });
    </script>
</body>
</html>