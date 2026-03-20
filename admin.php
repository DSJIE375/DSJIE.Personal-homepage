<?php
require_once __DIR__ . '/inc/data.php';

// 登录处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $password = $_POST['password'] ?? '';
    if (login($password)) {
        header('Location: admin.php');
        exit;
    }
    $error = '密码错误，请重试。';
}

// 登出
if (isset($_GET['logout'])) {
    logout();
    header('Location: admin.php');
    exit;
}

// 如果未登录，则显示登录页面
if (!isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>后台登录 - DSJIE</title>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="css/responsive.css" rel="stylesheet">
        <style>
            body {
                background: #f4f4f4;
            }

            .login-card {
                max-width: 420px;
                margin: 120px auto;
            }

            .login-card .card {
                border-radius: 16px;
                box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            }

            .login-card .btn-primary {
                background: #ff6700;
                border: 0;
            }
        </style>
    </head>

    <body>
        <div class="login-card">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">后台登录</h2>
                    <form method="post" action="admin.php">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label">管理员密码</label>
                            <input type="password" name="password" class="form-control" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">登录</button>
                    </form>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mt-3" role="alert"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <p class="mt-3 text-muted" style="font-size:13px;"></p>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
    exit;
}

// 登录之后处理数据
$data = loadData();
$messages = loadMessages();

// 保证 social_links 数组存在，方便后台编辑
$data['home']['social_links'] = $data['home']['social_links'] ?? [];

// 统计信息，用于顶部统计面板
$stats = [
    'site_links' => count($data['site_links'] ?? []),
    'social_links' => count($data['home']['social_links'] ?? []),
    'contact_details' => count($data['contact']['details'] ?? []),
    'about_info' => count($data['about']['info'] ?? []),
    'skills' => count($data['about']['skills'] ?? []),
    'gallery' => count($data['gallery'] ?? []),
    'education' => count($data['resume']['education'] ?? []),
    'experience' => count($data['resume']['experience'] ?? []),
    'messages' => count($messages),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 站点链接管理
    if ($action === 'add_site' || $action === 'edit_site') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $title = sanitize($_POST['title'] ?? '');
        $url = sanitize($_POST['url'] ?? '');
        $image = sanitize($_POST['image'] ?? '');
        $meta = sanitize($_POST['meta'] ?? '');

        if ($action === 'add_site') {
            $data['site_links'][] = [
                'id' => nextId($data['site_links']),
                'title' => $title,
                'url' => $url,
                'image' => $image,
                'meta' => $meta,
            ];
        } else {
            foreach ($data['site_links'] as &$site) {
                if ((int) $site['id'] === $id) {
                    $site['title'] = $title;
                    $site['url'] = $url;
                    $site['image'] = $image;
                    $site['meta'] = $meta;
                }
            }
            unset($site);
        }
    }

    if ($action === 'delete_site' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $data['site_links'] = array_values(array_filter($data['site_links'], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    // 相册管理
    if ($action === 'add_gallery' || $action === 'edit_gallery') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $image = sanitize($_POST['image'] ?? '');
        $link = sanitize($_POST['link'] ?? '');

        if ($action === 'add_gallery') {
            $data['gallery'][] = [
                'id' => nextId($data['gallery']),
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'link' => $link,
            ];
        } else {
            foreach ($data['gallery'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['title'] = $title;
                    $item['description'] = $description;
                    $item['image'] = $image;
                    $item['link'] = $link;
                }
            }
            unset($item);
        }
    }

    if ($action === 'delete_gallery' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $data['gallery'] = array_values(array_filter($data['gallery'], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    // 关于信息管理
    if ($action === 'add_about_info' || $action === 'edit_about_info') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $label = sanitize($_POST['label'] ?? '');
        $value = sanitize($_POST['value'] ?? '');

        if ($action === 'add_about_info') {
            $data['about']['info'][] = [
                'id' => nextId($data['about']['info']),
                'label' => $label,
                'value' => $value,
            ];
        } else {
            foreach ($data['about']['info'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['label'] = $label;
                    $item['value'] = $value;
                }
            }
            unset($item);
        }
    }

    if ($action === 'delete_about_info' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $data['about']['info'] = array_values(array_filter($data['about']['info'], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    // 关于介绍管理
    if ($action === 'edit_about_intro') {
        $data['about']['intro'] = sanitize($_POST['intro'] ?? '');
    }

    // 技能管理
    if ($action === 'add_skill' || $action === 'edit_skill') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = sanitize($_POST['name'] ?? '');
        $value = (int) ($_POST['value'] ?? 0);

        if ($action === 'add_skill') {
            $data['about']['skills'][] = [
                'id' => nextId($data['about']['skills']),
                'name' => $name,
                'value' => $value,
            ];
        } else {
            foreach ($data['about']['skills'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['name'] = $name;
                    $item['value'] = $value;
                }
            }
            unset($item);
        }
    }

    if ($action === 'delete_skill' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $data['about']['skills'] = array_values(array_filter($data['about']['skills'], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    // 足迹管理（education / experience）
    if ($action === 'add_resume' || $action === 'edit_resume') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $type = ($_POST['type'] ?? 'education') === 'experience' ? 'experience' : 'education';
        $title = sanitize($_POST['title'] ?? '');
        $date = sanitize($_POST['date'] ?? '');
        $position = sanitize($_POST['position'] ?? '');
        $text = sanitize($_POST['text'] ?? '');

        if ($action === 'add_resume') {
            $item = [
                'id' => nextId($data['resume'][$type]),
                'title' => $title,
                'date' => $date,
                'text' => $text,
            ];
            if ($type === 'experience') {
                $item['position'] = $position;
            }
            $data['resume'][$type][] = $item;
        } else {
            foreach ($data['resume'][$type] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['title'] = $title;
                    $item['date'] = $date;
                    $item['text'] = $text;
                    if ($type === 'experience') {
                        $item['position'] = $position;
                    }
                }
            }
            unset($item);
        }
    }

    if ($action === 'delete_resume' && isset($_POST['id']) && isset($_POST['type'])) {
        $type = ($_POST['type'] === 'experience') ? 'experience' : 'education';
        $id = (int) $_POST['id'];
        $data['resume'][$type] = array_values(array_filter($data['resume'][$type], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    // 社交链接管理（首页底部社交图标）
    if ($action === 'add_social' || $action === 'edit_social') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $icon = sanitize($_POST['icon'] ?? '');
        $url = sanitize($_POST['url'] ?? '');

        if ($action === 'add_social') {
            $data['home']['social_links'][] = [
                'id' => nextId($data['home']['social_links']),
                'icon' => $icon,
                'url' => $url,
            ];
        } else {
            foreach ($data['home']['social_links'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['icon'] = $icon;
                    $item['url'] = $url;
                }
            }
            unset($item);
        }
    }

    // 详细信息管理（联系我们 -> 详细信息）
    if ($action === 'add_contact_detail' || $action === 'edit_contact_detail') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $icon = sanitize($_POST['icon'] ?? '');
        $value = sanitize($_POST['value'] ?? '');

        if ($action === 'add_contact_detail') {
            $data['contact']['details'][] = [
                'id' => nextId($data['contact']['details']),
                'icon' => $icon,
                'value' => $value,
            ];
        } else {
            foreach ($data['contact']['details'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['icon'] = $icon;
                    $item['value'] = $value;
                }
            }
            unset($item);
        }
    }

    if ($action === 'delete_contact_detail' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $data['contact']['details'] = array_values(array_filter($data['contact']['details'], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    if ($action === 'delete_social' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $data['home']['social_links'] = array_values(array_filter($data['home']['social_links'], function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    // 留言管理
    if ($action === 'delete_message' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $messages = array_values(array_filter($messages, function ($item) use ($id) {
            return (int) $item['id'] !== $id;
        }));
    }

    if ($action === 'edit_message' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message_text = sanitize($_POST['message'] ?? '');

        foreach ($messages as &$msg) {
            if ((int) $msg['id'] === $id) {
                $msg['name'] = $name;
                $msg['email'] = $email;
                $msg['phone'] = $phone;
                $msg['address'] = $address;
                $msg['subject'] = $subject;
                $msg['message'] = $message_text;
            }
        }
        unset($msg);
    }

    // 首页设置（头像 / 引导语 / 介绍文案）
    if ($action === 'edit_home') {
        $data['home']['avatar'] = sanitize($_POST['avatar'] ?? '');
        $data['home']['quote_top'] = sanitize($_POST['quote_top'] ?? '');
        $data['home']['quote_bottom'] = sanitize($_POST['quote_bottom'] ?? '');
        $data['home']['intro'] = sanitize($_POST['intro'] ?? '');
    }

    saveData($data);
    saveMessages($messages);
    header('Location: admin.php');
    exit;
}

// 后台页面显示
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <title>后台管理 - DSJIE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
        }

        .topbar {
            background: #ff6700;
            color: #fff;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin: 0 auto ;
            max-width: 1040px;
            border-radius: 14px;
        }

        .topbar a {
            color: #fff;
            text-decoration: none;
        }

        .content {
            padding: 20px;
            max-width: 1080px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .box {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10);
            overflow-x: auto;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .box h1 {
            margin-top: 0;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: .02em;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding-bottom: 10px;
        }

        .topbar {
            background: linear-gradient(135deg, #ff6700, #ff9b2e);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        }

        .topbar a {
            color: #fff;
            text-decoration: none;
        }

        .content {
            padding: 20px;
            max-width: 1080px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .flex {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .flex>div {
            flex: 1 1 220px;
            min-width: 220px;
        }

        .stat-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .stat-card {
            flex: 1 1 200px;
            min-width: 180px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 14px;
            padding: 18px 16px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #ff6700;
        }

        .stat-label {
            margin-top: 6px;
            color: #666;
            font-size: 0.95rem;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid rgba(0, 0, 0, 0.16);
            border-radius: 10px;
            background: #fbfbfb;
            box-sizing: border-box;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: rgba(255, 103, 0, 0.7);
            box-shadow: 0 0 0 3px rgba(255, 103, 0, 0.13);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        table {
            border-radius: 12px;
            overflow: hidden;
        }

        table thead {
            background: #fff7eb;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        .btn-primary {
            background: #ff6700;
            border: 0;
        }

        .btn-primary:hover {
            background: #ff8b3a;
        }

        .btn-danger {
            background: #e04b4b;
            border: 0;
        }

        .btn-danger:hover {
            background: #ff5c5c;
        }

        @media (max-width: 768px) {
            .topbar {
                padding: 12px 16px;
            }

            

            .flex>div {
                min-width: 100%;
            }
        }

        .admin-nav {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10);
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div><strong>DSJIE 后台管理</strong></div>
        <div><a href="?logout=1" class="btn btn-light btn-sm">退出登录</a></div>
    </div>
    <div class="content">
        <div class="admin-layout">
            <aside class="admin-nav">
                <div class="nav-title">管理导航</div>
                <a href="#" class="nav-link active" data-target="section-stats">统计</a>
                <a href="#" class="nav-link" data-target="section-home">首页</a>
                <a href="#" class="nav-link" data-target="section-social">社交链接</a>
                <a href="#" class="nav-link" data-target="section-contact">详细信息</a>
                <a href="#" class="nav-link" data-target="section-about">关于</a>
                <a href="#" class="nav-link" data-target="section-skills">技能</a>
                <a href="#" class="nav-link" data-target="section-resume">足迹</a>
                <a href="#" class="nav-link" data-target="section-gallery">相册</a>
                <a href="#" class="nav-link" data-target="section-site">站点</a>
                <a href="#" class="nav-link" data-target="section-messages">留言</a>
                <a href="/ipfw">访问统计</a>
            </aside>
            <main class="admin-main">
                <div class="box section" id="section-stats">
                    <h1>统计总览</h1>
                    <div class="stat-cards">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['site_links']; ?></div>
                            <div class="stat-label">站点链接</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['social_links']; ?></div>
                            <div class="stat-label">社交链接</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['contact_details']; ?></div>
                            <div class="stat-label">详细信息</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['about_info']; ?></div>
                            <div class="stat-label">关于信息</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['skills']; ?></div>
                            <div class="stat-label">技能</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['gallery']; ?></div>
                            <div class="stat-label">相册</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['education']; ?></div>
                            <div class="stat-label">教育</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['experience']; ?></div>
                            <div class="stat-label">工作</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo (int) $stats['messages']; ?></div>
                            <div class="stat-label">留言</div>
                        </div>
                    </div>
                </div>

                <div class="box section" id="section-home">
                    <h1>首页设置</h1>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="edit_home">
                        <div class="flex">
                            <div>
                                <label>头像 URL</label>
                                <input name="avatar"
                                    value="<?php echo htmlspecialchars($data['home']['avatar'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label>引导语（上段）</label>
                                <input name="quote_top"
                                    value="<?php echo htmlspecialchars($data['home']['quote_top'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="flex">
                            <div>
                                <label>引导语（下段）</label>
                                <input name="quote_bottom"
                                    value="<?php echo htmlspecialchars($data['home']['quote_bottom'] ?? ''); ?>"
                                    required>
                            </div>
                            <div>
                                <label>主页介绍</label>
                                <textarea name="intro" rows="2"
                                    required><?php echo htmlspecialchars($data['home']['intro'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">保存首页设置</button>
                    </form>
                </div>

                <div class="box section" id="section-social">
                    <h1>社交链接管理</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>预览</th>
                                <th>图标类（FontAwesome）</th>
                                <th>链接地址</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['home']['social_links'] as $item): ?>
                                <tr>
                                    <td><?php echo (int) $item['id']; ?></td>
                                    <td><span class="<?php echo htmlspecialchars($item['icon']); ?>"
                                            style="font-size:18px;"></span></td>
                                    <td><?php echo htmlspecialchars($item['icon']); ?></td>
                                    <td><?php echo htmlspecialchars($item['url']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editSocial(<?php echo (int) $item['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_social">
                                            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        <h3>新增社交链接</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="add_social">
                            <div class="flex">
                                <div>
                                    <label>FontAwesome 图标类</label>
                                    <input id="social-icon-input" name="icon" placeholder="fa fa-weixin" required>
                                    <div style="margin-top:8px;"><span id="social-icon-preview" class="fa fa-weixin"
                                            style="font-size:18px;"></span> 图标预览</div>
                                </div>
                                <div>
                                    <label>链接地址</label>
                                    <input name="url" placeholder="https://" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">添加链接</button>
                        </form>
                    </div>
                </div>

                <div class="box section" id="section-contact">
                    <h1>详细信息管理（联系信息）</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>图标类</th>
                                <th>内容</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['contact']['details'] as $item): ?>
                                <tr>
                                    <td><?php echo (int) $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['icon']); ?></td>
                                    <td><?php echo nl2br(str_replace('&lt;br&gt;', '<br>', htmlspecialchars($item['value']))); ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editContactDetail(<?php echo (int) $item['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_contact_detail">
                                            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        <h3>新增详细信息</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="add_contact_detail">
                            <div class="flex">
                                <div>
                                    <label>FontAwesome / Flaticon 图标类</label>
                                    <input name="icon" placeholder="flaticon-signs" required>
                                </div>
                                <div>
                                    <label>内容（支持换行）</label>
                                    <input name="value" placeholder="例如：广西壮族自治区 玉林市<br>*** ***" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">添加</button>
                        </form>
                    </div>
                </div>

                <div class="box section" id="section-about">
                    <h1>关于信息管理</h1>

                    <div style="margin-bottom: 20px;">
                        <h3>关于介绍</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="edit_about_intro">
                            <textarea name="intro" rows="3" style="width:100%;"><?php echo htmlspecialchars($data['about']['intro'] ?? ''); ?></textarea>
                            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">保存介绍</button>
                        </form>
                    </div>

                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标签</th>
                                <th>内容</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['about']['info'] as $item): ?>
                                <tr>
                                    <td><?php echo (int) $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['label']); ?></td>
                                    <td><?php echo htmlspecialchars($item['value']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editAboutInfo(<?php echo (int) $item['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_about_info">
                                            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        <h3>新增关于信息</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="add_about_info">
                            <div class="flex">
                                <div>
                                    <label>标签</label>
                                    <input name="label" required>
                                </div>
                                <div>
                                    <label>内容</label>
                                    <input name="value" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">添加信息</button>
                        </form>
                    </div>
                </div>

                <div class="box section" id="section-skills">
                    <h1>技能管理</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>技能名称</th>
                                <th>熟练度</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['about']['skills'] as $item): ?>
                                <tr>
                                    <td><?php echo (int) $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo (int) $item['value']; ?>%</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editSkill(<?php echo (int) $item['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_skill">
                                            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        <h3>新增技能</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="add_skill">
                            <div class="flex">
                                <div>
                                    <label>技能名称</label>
                                    <input name="name" required>
                                </div>
                                <div>
                                    <label>熟练度（0-100）</label>
                                    <input name="value" type="number" min="0" max="100" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">添加技能</button>
                        </form>
                    </div>
                </div>

                <div class="box section" id="section-resume">
                    <h1>足迹管理</h1>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#education">教育经历</a></li>
                        <li><a data-toggle="tab" href="#experience">工作经历</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="education" class="tab-pane fade in active">
                            <h3>教育经历</h3>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>学校/机构</th>
                                        <th>时间</th>
                                        <th>描述</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['resume']['education'] as $item): ?>
                                        <tr>
                                            <td><?php echo (int) $item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                                            <td><?php echo htmlspecialchars($item['date']); ?></td>
                                            <td><?php echo htmlspecialchars($item['text']); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="#"
                                                    onclick="editResume(<?php echo (int) $item['id']; ?>, 'education')">编辑</a>
                                                <form method="post" action="" style="display:inline;"
                                                    onsubmit="return confirm('确定删除？');">
                                                    <input type="hidden" name="action" value="delete_resume">
                                                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                                    <input type="hidden" name="type" value="education">
                                                    <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="margin-top: 20px;">
                                <h3>新增教育经历</h3>
                                <form method="post" action="">
                                    <input type="hidden" name="action" value="add_resume">
                                    <input type="hidden" name="type" value="education">
                                    <div class="flex">
                                        <div>
                                            <label>学校/机构</label>
                                            <input name="title" required>
                                        </div>
                                        <div>
                                            <label>时间</label>
                                            <input name="date" required>
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <div>
                                            <label>描述</label>
                                            <textarea name="text" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">添加经历</button>
                                </form>
                            </div>
                        </div>
                        <div id="experience" class="tab-pane fade">
                            <h3>工作经历</h3>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>公司/机构</th>
                                        <th>时间</th>
                                        <th>职位</th>
                                        <th>描述</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['resume']['experience'] as $item): ?>
                                        <tr>
                                            <td><?php echo (int) $item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                                            <td><?php echo htmlspecialchars($item['date']); ?></td>
                                            <td><?php echo htmlspecialchars($item['position']); ?></td>
                                            <td><?php echo htmlspecialchars($item['text']); ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="#"
                                                    onclick="editResume(<?php echo (int) $item['id']; ?>, 'experience')">编辑</a>
                                                <form method="post" action="" style="display:inline;"
                                                    onsubmit="return confirm('确定删除？');">
                                                    <input type="hidden" name="action" value="delete_resume">
                                                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                                    <input type="hidden" name="type" value="experience">
                                                    <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="margin-top: 20px;">
                                <h3>新增工作经历</h3>
                                <form method="post" action="">
                                    <input type="hidden" name="action" value="add_resume">
                                    <input type="hidden" name="type" value="experience">
                                    <div class="flex">
                                        <div>
                                            <label>公司/机构</label>
                                            <input name="title" required>
                                        </div>
                                        <div>
                                            <label>时间</label>
                                            <input name="date" required>
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <div>
                                            <label>职位</label>
                                            <input name="position" required>
                                        </div>
                                        <div>
                                            <label>描述</label>
                                            <textarea name="text" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">添加经历</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box section" id="section-gallery">
                    <h1>相册管理</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>描述</th>
                                <th>图片</th>
                                <th>链接</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['gallery'] as $item): ?>
                                <tr>
                                    <td><?php echo (int) $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                    <td><?php echo htmlspecialchars($item['image']); ?></td>
                                    <td><?php echo htmlspecialchars($item['link']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editGallery(<?php echo (int) $item['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_gallery">
                                            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        <h3>新增相册项</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="add_gallery">
                            <div class="flex">
                                <div>
                                    <label>标题</label>
                                    <input name="title" required>
                                </div>
                                <div>
                                    <label>描述</label>
                                    <input name="description" required>
                                </div>
                            </div>
                            <div class="flex">
                                <div>
                                    <label>图片 URL</label>
                                    <input name="image" required placeholder="/images/pic.jpg">
                                </div>
                                <div>
                                    <label>点击链接</label>
                                    <input name="link" placeholder="/images/pic.jpg">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">添加相册</button>
                        </form>
                    </div>
                </div>

                <div class="box section" id="section-site">
                    <h1>站点 / 链接管理</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>URL</th>
                                <th>图片</th>
                                <th>来源</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['site_links'] as $site): ?>
                                <tr>
                                    <td><?php echo (int) $site['id']; ?></td>
                                    <td><?php echo htmlspecialchars($site['title']); ?></td>
                                    <td><?php echo htmlspecialchars($site['url']); ?></td>
                                    <td><?php echo htmlspecialchars($site['image']); ?></td>
                                    <td><?php echo htmlspecialchars($site['meta']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editSite(<?php echo (int) $site['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_site">
                                            <input type="hidden" name="id" value="<?php echo (int) $site['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        <h3>新增站点链接</h3>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="add_site">
                            <div class="flex">
                                <div>
                                    <label>标题</label>
                                    <input name="title" required>
                                </div>
                                <div>
                                    <label>URL</label>
                                    <input name="url" required placeholder="https://example.com">
                                </div>
                            </div>
                            <div class="flex">
                                <div>
                                    <label>图片（可留空）</label>
                                    <input name="image" placeholder="/images/example.png">
                                </div>
                                <div>
                                    <label>来源说明</label>
                                    <input name="meta" placeholder="WP博客">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">添加链接</button>
                        </form>
                    </div>
                </div>

                <div class="box section" id="section-messages">
                    <h1>留言管理</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>时间</th>
                                <th>姓名</th>
                                <th>邮箱</th>
                                <th>电话</th>
                                <th>地址</th>
                                <th>主题</th>
                                <th>内容</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td><?php echo (int) $msg['id']; ?></td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d H:i', $msg['time'] ?? time())); ?></td>
                                    <td><?php echo htmlspecialchars($msg['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($msg['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($msg['phone'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($msg['address'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($msg['subject'] ?? ''); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($msg['message'] ?? '')); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="#"
                                            onclick="editMessage(<?php echo (int) $msg['id']; ?>)">编辑</a>
                                        <form method="post" action="" style="display:inline;"
                                            onsubmit="return confirm('确定删除？');">
                                            <input type="hidden" name="action" value="delete_message">
                                            <input type="hidden" name="id" value="<?php echo (int) $msg['id']; ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">删除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- 引入 jQuery 和 Bootstrap JS，以支持选项卡等交互功能 -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
        const siteLinks = <?php echo json_encode($data['site_links'], JSON_UNESCAPED_UNICODE); ?>;
        const galleryItems = <?php echo json_encode($data['gallery'], JSON_UNESCAPED_UNICODE); ?>;
        const contactDetails = <?php echo json_encode($data['contact']['details'], JSON_UNESCAPED_UNICODE); ?>;
        const aboutInfo = <?php echo json_encode($data['about']['info'], JSON_UNESCAPED_UNICODE); ?>;
        const skills = <?php echo json_encode($data['about']['skills'], JSON_UNESCAPED_UNICODE); ?>;
        const education = <?php echo json_encode($data['resume']['education'], JSON_UNESCAPED_UNICODE); ?>;
        const experience = <?php echo json_encode($data['resume']['experience'], JSON_UNESCAPED_UNICODE); ?>;
        const messages = <?php echo json_encode($messages, JSON_UNESCAPED_UNICODE); ?>;

        function setActiveSection(id) {
            document.querySelectorAll('.admin-main .section').forEach(el => {
                el.style.display = el.id === id ? 'block' : 'none';
            });
            document.querySelectorAll('.admin-nav .nav-link').forEach(link => {
                link.classList.toggle('active', link.dataset.target === id);
            });
        }

        document.querySelectorAll('.admin-nav .nav-link').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const target = link.dataset.target;
                if (target) {
                    setActiveSection(target);
                    history.replaceState(null, '', `#${target}`);
                }
            });
        });

        // 初始显示：根据 hash 或默认 section-stats
        const initialHash = window.location.hash.replace('#', '') || 'section-stats';
        setActiveSection(initialHash);

        // 原有编辑函数保持不变

        function editSite(id) {
            const item = siteLinks.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_site">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="title" value="${item.title}">
            <input type="hidden" name="url" value="${item.url}">
            <input type="hidden" name="image" value="${item.image}">
            <input type="hidden" name="meta" value="${item.meta}">
        `;
            document.body.appendChild(form);
            const title = prompt('标题', item.title);
            if (title === null) return;
            const url = prompt('URL', item.url);
            if (url === null) return;
            const image = prompt('图片 URL', item.image);
            if (image === null) return;
            const meta = prompt('来源说明', item.meta);
            if (meta === null) return;
            form.querySelector('input[name="title"]').value = title;
            form.querySelector('input[name="url"]').value = url;
            form.querySelector('input[name="image"]').value = image;
            form.querySelector('input[name="meta"]').value = meta;
            form.submit();
        }

        function editGallery(id) {
            const item = galleryItems.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_gallery">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="title" value="${item.title}">
            <input type="hidden" name="description" value="${item.description}">
            <input type="hidden" name="image" value="${item.image}">
            <input type="hidden" name="link" value="${item.link}">
        `;
            document.body.appendChild(form);
            const title = prompt('标题', item.title);
            if (title === null) return;
            const desc = prompt('描述', item.description);
            if (desc === null) return;
            const image = prompt('图片 URL', item.image);
            if (image === null) return;
            const link = prompt('点击链接', item.link);
            if (link === null) return;
            form.querySelector('input[name="title"]').value = title;
            form.querySelector('input[name="description"]').value = desc;
            form.querySelector('input[name="image"]').value = image;
            form.querySelector('input[name="link"]').value = link;
            form.submit();
        }

        function editAboutInfo(id) {
            const item = aboutInfo.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_about_info">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="label" value="${item.label}">
            <input type="hidden" name="value" value="${item.value}">
        `;
            document.body.appendChild(form);
            const label = prompt('标签', item.label);
            if (label === null) return;
            const value = prompt('内容', item.value);
            if (value === null) return;
            form.querySelector('input[name="label"]').value = label;
            form.querySelector('input[name="value"]').value = value;
            form.submit();
        }

        function editContactDetail(id) {
            const item = contactDetails.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_contact_detail">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="icon" value="${item.icon}">
            <input type="hidden" name="value" value="${item.value}">
        `;
            document.body.appendChild(form);
            const icon = prompt('图标类 (FontAwesome/Flaticon)', item.icon || '');
            if (icon === null) return;
            const value = prompt('内容（可使用<br>换行）', item.value || '');
            if (value === null) return;
            form.querySelector('input[name="icon"]').value = icon;
            form.querySelector('input[name="value"]').value = value;
            form.submit();
        }

        function editSkill(id) {
            const item = skills.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_skill">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="name" value="${item.name}">
            <input type="hidden" name="value" value="${item.value}">
        `;
            document.body.appendChild(form);
            const name = prompt('技能名称', item.name);
            if (name === null) return;
            const value = prompt('熟练度（0-100）', item.value);
            if (value === null) return;
            form.querySelector('input[name="name"]').value = name;
            form.querySelector('input[name="value"]').value = value;
            form.submit();
        }

        function editResume(id, type) {
            const items = type === 'education' ? education : experience;
            const item = items.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_resume">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="type" value="${type}">
            <input type="hidden" name="title" value="${item.title}">
            <input type="hidden" name="date" value="${item.date}">
            <input type="hidden" name="text" value="${item.text}">
            <input type="hidden" name="position" value="${item.position || ''}">
        `;
            document.body.appendChild(form);
            const title = prompt('标题', item.title);
            if (title === null) return;
            const date = prompt('时间', item.date);
            if (date === null) return;
            const text = prompt('描述', item.text);
            if (text === null) return;
            let position = '';
            if (type === 'experience') {
                position = prompt('职位', item.position || '');
                if (position === null) return;
            }
            form.querySelector('input[name="title"]').value = title;
            form.querySelector('input[name="date"]').value = date;
            form.querySelector('input[name="text"]').value = text;
            if (type === 'experience') {
                form.querySelector('input[name="position"]').value = position;
            }
            form.submit();
        }

        function editSocial(id) {
            const item = socialLinks.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_social">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="icon" value="${item.icon}">
            <input type="hidden" name="url" value="${item.url}">
        `;
            document.body.appendChild(form);
            const icon = prompt('FontAwesome 图标类 (如 fa fa-weixin)', item.icon);
            if (icon === null) return;
            const url = prompt('链接地址', item.url);
            if (url === null) return;
            form.querySelector('input[name="icon"]').value = icon;
            form.querySelector('input[name="url"]').value = url;
            form.submit();
        }

        function editMessage(id) {
            const item = messages.find(i => i.id == id);
            if (!item) return;
            const form = document.createElement('form');
            form.method = 'post';
            form.style.display = 'none';
            form.innerHTML = `
            <input type="hidden" name="action" value="edit_message">
            <input type="hidden" name="id" value="${item.id}">
            <input type="hidden" name="name" value="${item.name}">
            <input type="hidden" name="email" value="${item.email}">
            <input type="hidden" name="phone" value="${item.phone || ''}">
            <input type="hidden" name="address" value="${item.address || ''}">
            <input type="hidden" name="subject" value="${item.subject}">
            <input type="hidden" name="message" value="${item.message}">
        `;
            document.body.appendChild(form);
            const name = prompt('姓名', item.name);
            if (name === null) return;
            const email = prompt('邮箱', item.email);
            if (email === null) return;
            const phone = prompt('电话', item.phone || '');
            if (phone === null) return;
            const address = prompt('地址', item.address || '');
            if (address === null) return;
            const subject = prompt('主题', item.subject);
            if (subject === null) return;
            const message = prompt('内容', item.message);
            if (message === null) return;
            form.querySelector('input[name="name"]').value = name;
            form.querySelector('input[name="email"]').value = email;
            form.querySelector('input[name="phone"]').value = phone;
            form.querySelector('input[name="address"]').value = address;
            form.querySelector('input[name="subject"]').value = subject;
            form.querySelector('input[name="message"]').value = message;
            form.submit();
        }

        // 社交图标输入预览
        const socialIconInput = document.getElementById('social-icon-input');
        const socialIconPreview = document.getElementById('social-icon-preview');
        if (socialIconInput && socialIconPreview) {
            socialIconInput.addEventListener('input', () => {
                socialIconPreview.className = socialIconInput.value.trim() || 'fa fa-weixin';
            });
        }
    </script>
</body>

</html>