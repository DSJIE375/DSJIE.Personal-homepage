<?php
//获取用户真实IP
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $_SERVER['REMOTE_ADDR'] = $list[0];
    $ip_if = $_SERVER['REMOTE_ADDR'];
} else {
    $ip_if = $_SERVER['REMOTE_ADDR'];
}

//IP归属地获取 A （直接输出模式）
function get_ips($ip)
{
    echo Get_City($ip);
}

//IP归属地获取 B （返回值模式）
function Get_City($ip)
{
    // 腾讯位置服务API
    $key = '填写你的key'; // 可申请免费key：https://lbs.qq.com/service/webService/webServiceGuide/webServiceIp
    $API_URL = "https://apis.map.qq.com/ws/location/v1/ip?ip={$ip}&key={$key}";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['result']) && $data['status'] == 0) {
            $result = $data['result'];
            $ad_info = $result['ad_info'];
            
            $location = '';
            if (!empty($ad_info['province'])) {
                $location .= $ad_info['province'];
            }
            if (!empty($ad_info['city']) && $ad_info['city'] != $ad_info['province']) {
                $location .= ' · ' . $ad_info['city'];
            }
            if (!empty($ad_info['district'])) {
                $location .= ' · ' . $ad_info['district'];
            }
            
            return $location ?: '未知';
        }
    }
    
    return "未知";
}

$time = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
$gsd = Get_City($ip_if);
$log_file = "access_log.log";

// 记录更多访问信息
$page_visited = $_SERVER['REQUEST_URI'];  // 访问的页面
$request_method = $_SERVER['REQUEST_METHOD'];  // 请求方法
$user_agent = $_SERVER['HTTP_USER_AGENT'];  // 用户代理信息
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct Access';  // 来源页面

// 生成日志条目
$log_entry = "\n". $ip_if . "|" . $gsd . "|" . $time . "|" . $request_method . "|" . $page_visited . "|" . $user_agent . "|" . $referer . "\n";




if (!isset($_COOKIE["getIP"])) {
    $fp = fopen($log_file, "a");
    fputs($fp, $log_entry);
    fclose($fp);
    setcookie("getIP", $ip_if, time() + 10);
}





require_once __DIR__ . '/inc/data.php';
// DSJIE 个人主页 - PHP 版本
$siteTitle = 'DSJIE个人主页_导航页';
$lang = 'zh-CN';

$data = loadData();
$home = $data['home'] ?? [];
$socialLinks = $home['social_links'] ?? [
    ['id' => 1, 'icon' => 'fa fa-comments', 'url' => '#'],
    ['id' => 2, 'icon' => 'fa fa-qq', 'url' => '#'],
    ['id' => 3, 'icon' => 'fa fa-location-arrow', 'url' => '#'],
    ['id' => 4, 'icon' => 'fa fa-weixin', 'url' => '#'],
    ['id' => 5, 'icon' => 'fa fa-envelope', 'url' => '#'],
];
$sent = $_GET['sent'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <!--og:image-->
    <meta property="og:title" content="DSJIE个人主页_导航页">
    <meta property="og:description" content="欢迎来到DSJIE.的个人主页_导航页">
    <meta property="og:image" content="https://dsjie375.cn/images/dsjie.png"> <!-- 确保链接可访问 -->
    <meta property="og:url" content="https://dsjie375.cn/">
    
    <meta charset="utf-8">
    <title><?php echo $siteTitle; ?></title>
    <!-- Stylesheets -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@700&display=swap" rel="stylesheet">
    <!--Favicon-->
    <link rel="shortcut icon" href="/images/dsjie.png" type="image/x-icon">
    <link rel="icon" href="/images/dsjie.png" type="image/x-icon">
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="js/respond.js"></script><![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="https://img-love.kikiw.cn/jsxg/fengye/cursor6.js" type="text/javascript"></script>
</head>

<body>
    <script src="ip.php"></script>
    <div class="page-wrapper default-version">

        <!-- Preloader -->
        <div class="preloader"></div>

        <div class="page-background">
            <!-- <div class="image-1"><img src="/images/dsjie.png" alt=""></div>
            <div class="image-7"><img src="/images/dsjie.png" alt=""></div> -->
            <canvas id="canvas"></canvas>
        </div>

        <style>
            body {
                font-family: 'Noto Serif SC', serif;
                font-weight: 400;
            }

            .header ul li a {
                background: #ededed;
            }

            .header {
                background: #ededed;
            }

            .card-home .author h3 {
                color: #fff;
            }

            .card-home .author .designation {
                color: #fff;
            }

            .card-home .text {
                color: #fff;
            }

            .card-outer .card-wrapper {
                border-radius: 25px;
            }

            .header {
                border-radius: 20px;
            }

            

            .card-outer .card-inner-box.offsetleft .header {
                border-radius: 20px;
            }

            .header ul li.active a {
                background: #ff6700;
                color: #fff;
                border-radius: 20px;
            }

            .card-resume .title {
                margin-top: 0px;
            }

            .card-services .title {
                margin-top: 0px;
            }

            .card-projects .title {
                margin-top: 0px;
            }

            .card-blog .title {
                margin-top: 0px;
            }

            .card-contact .title {
                margin-top: 0px;
            }

            .projects-block .image {
                border-radius: 10px;
            }

            .contact-form .form-group textarea {
                border-radius: 10px;
            }
            

            .news-block .image:before {
                border-radius: 10px;
            }

            .news-block .image img {
                border-radius: 10px;
                /* height: 30VH; */
            }

            .news-block .lower-content {
                border-radius: 0px 0px 10px 10px;
                padding: 30px;
                border: 1px solid rgba(208, 206, 206, 0.4);
                border-top: 0px;
                box-shadow: 0 3px 12px #ebedf0ad;
            }

            .home-google-map .google-map {
                height: 0px;
            }

            .author-info .author h3 {
                font-weight: 500;
                color: #fff;
            }

            .author-info .author .designation {
                font-family: 'Poppins', sans-serif;
                font-size: 15px;
                margin-bottom: 30px;
                color: #fff;
            }

            .social-icon-two li a {
                color: #ffffff;
            }

            .btn-style-two {
                color: #fff;
            }

            .social-icon-two li a {
                line-height: 42px;
                border: 1px solid #dbdbdb;
            }

            .news-block .date {
                background-color: #2229;
            }

            .huise {
                color: #ccc;
            }

            .services-block .text {
                margin-bottom: 10px;
                height: 3rem;
            }

            .services-block.col-md-6 {
                cursor: pointer;
            }

            .btn-style-one,
            .btn-style-two {
                padding: 8px 30px;
            }

            .default-version .card-outer .card-wrapper {
                box-shadow: 0 0 13px 2px rgb(156 154 154 / 27%);
            }

            @media only screen and (max-width: 1199px) {
                .default-version .card-outer .card-wrapper {
                    box-shadow: none;
                }
            }

            .projects-block .image {
                border-radius: 15px;
                box-shadow: 0px 0px 10px 0 rgb(176 169 169 / 29%);
                margin: 10px;
            }

            .shadow-blur {
                position: relative;
            }

            .shadow-blur::before {
                content: "";
                display: block;
                background: inherit;
                filter: blur(0.5rem);
                position: absolute;
                width: 100%;
                height: 100%;
                top: 5px;
                left: 5px;
                z-index: -1;
                opacity: 0.4;
                transform-origin: 0 0;
                border-radius: inherit;
                transform: scale(1, 1);
            }

            .ltps {
                /*background-image: linear-gradient(45deg, #0081ff, #1cbbb4);*/
                background: #ff6700;
                border-radius: 1rem;
                color: #fff;
                border-radius: 0.5rem;
                padding: 1.5rem 1rem;
                font-size: 1.3rem;
                font-family: 'Noto Serif SC', serif;
                font-weight: 700;
                display: flex;
                align-items: center;
                margin-bottom: 20px;
            }


            .ltps .hen {
                width: 4px;
                height: 20px;
                background: #fff;
                display: inline-block;
                border-radius: 1rem;
                margin-right: 0.6rem;
            }

            .keyPoint{
                background-color: #d2f1f0;
                color: #1cbbb4;
                padding: 2px 10px;
                border-radius: 20px;
                font-style: normal;
                font-size: 14px;
            }

            .keyPoint_t{
                background-color: #fde6d2;
                color: #f37b1d;
                padding: 2px 10px;
                border-radius: 20px;
                font-style: normal;
                font-size: 14px;
            }


        </style>


        <!-- Mobile menu -->
        <div class="mobile-menu">
            <div class="container">
                <!--Nav Outer-->
                <div class="nav-outer clearfix">
                    <div class="logo"><a href="index.html"><img src="/images/dsjie.png" alt=""></a></div>
                    <!-- Main Menu -->
                    <nav class="main-menu">
                        <div class="navbar-header">
                            <!-- Toggle Button -->
                            <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target=".navbar-collapse">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                        <div class="navbar-collapse collapse scroll-nav clearfix">
                            <ul class="navigation clearfix">

                                <li class="current"><a href="#home">主页</a></li>
                                <li><a href="#about">关于</a></li>
                                <li><a href="#resume">足迹</a></li>
                                <li><a href="#services">学习</a></li>
                                <li><a href="#work">相册</a></li>
                                <li><a href="#blog">站点</a></li>
                                <li><a href="#contact">留言</a></li>

                            </ul>
                        </div>
                    </nav>
                    <!-- Main Menu End-->
                </div>
                <!--Nav Outer End-->
            </div>
        </div>

        <div class="card-outer">
            <div class="scroll-box">
                <div class="container" data-animation-in="fadeInLeft" data-animation-out="fadeOutLeft">
                    <div class="card-wrapper">
                        <div class="author-info"
                            style="background: radial-gradient(#ff6700, transparent);border-radius: 25px;background-size: 145%;">
                            <div class="image"><img src="<?php echo htmlspecialchars($home['avatar'] ?? '/images/dsjie-logo.svg'); ?>" alt=""></div>
                            <div class="author">
                                <h3>DSJIE</h3>
                                <div class="designation" style="font-family: 'Noto Serif SC', serif;font-weight: 400;">
                                    
                                    <div class="typing-title">
                                        
                                    </div>
                                    <span class="typed-title"></span>
                                </div>
                            </div>
                            <div class="link-btn">
                                <a href="mailto:dsjie375@qq.com"
                                    class="theme-btn btn-style-one">联系DSJIE</a>
                            </div>
                            <ul class="social-icon-two">
                                <?php foreach ($socialLinks as $link): ?>
                                    <li><a href="<?php echo htmlspecialchars($link['url'] ?? '#'); ?>"><span class="<?php echo htmlspecialchars($link['icon'] ?? ''); ?>"></span></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="card-inner-box">
                            <header class="header wow fadeInLeft" data-wow-duration="1.5s">
                                <ul class="main-menu">
                                    <li class="active home"><a class="logo" href="#home"><img src="<?php echo htmlspecialchars($home['avatar'] ?? '/images/dsjie-logo.svg'); ?>" alt=""></a></li>
                                    <li><a href="#about"><i class="flaticon-social"></i>关于</a></li>
                                    <li><a href="#resume"><i class="flaticon-curriculum"></i>足迹</a></li>
                                    <li><a href="#services"><i class="flaticon-layers-1"></i>学习</a></li>
                                    <li><a href="#work"><i class="flaticon-tools"></i>相册</a></li>
                                    <li><a href="#blog"><i class="flaticon-blog"></i>站点</a></li>
                                    <li><a href="#contact"><i class="flaticon-send-mail"></i>留言</a></li>
                                </ul>
                            </header>

                            <!-- card item -->
                            <div class="card-home card-item active" id="home"
                                style="background: radial-gradient(#ff6700, transparent);border-radius: 25px;">

                                <div class="card-inner wow fadeInUp" data-wow-duration="2s">
                                    <div class="image"><img src="<?php echo htmlspecialchars($home['avatar'] ?? 'https://dsjie375.cn/images/dsjie-logo.svg'); ?>" alt="">
                                    </div>
                                    <div class="author">
                                        <h3>DSJIE</h3>
                                        <div class="designation"
                                            style="font-family: 'Noto Serif SC', serif;font-weight: 400;">

                                            <div class="typing-title">
                                                <p> <strong><?php echo htmlspecialchars($home['quote_top'] ); ?></strong></p>
                                                <p> <strong><?php echo htmlspecialchars($home['quote_bottom'] ); ?></strong></p>
                                            </div>
                                            <span class="typed-title"></span>
                                        </div>
                                    </div>
                                    <div class="text"><?php echo htmlspecialchars($home['intro'] ?? 'You are my dream of gain and loss, and I am dispensable to you.'); ?></div>
                                    <div class="link-btn">
                                        <a href="mailto:dsjie375@qq.com"
                                            class="theme-btn btn-style-one">联系我</a>
                                        <a href="https://www.dsjie375.cn/" class="theme-btn btn-style_two btn-style-one">博客主页</a>
                                    </div>
                                    <ul class="social-icon-two">
                                        <?php foreach ($socialLinks as $link): ?>
                                            <li><a href="<?php echo htmlspecialchars($link['url'] ?? '#'); ?>"><span class="<?php echo htmlspecialchars($link['icon'] ?? ''); ?>"></span></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <br>

                                    <a href="https://beian.miit.gov.cn/#/Integrated/index" target="_blank"
                                        style="color: #ffffff;">桂ICP备 2025071767号</a>
                                    <br><br>
                                </div>

                            </div>
                            <!-- end item -->

                            <!-- card item -->
                            <div class="card-about card-item" id="about"
                                style="background: radial-gradient(#ff6700, transparent);">
                                <div class="card-inner">
                                    <h4 class="title">关于 DSJIE</h4>

                                    <div class="ltps shadow-blur">
                                        <span class="hen"></span>
                                        技能：前端三件套 html（凑合用）、css（凑合用）、javascript （鸡毛蒜皮）
                                    </div>

                                    <div class="info-tags">
                                        <?php foreach (($data['about']['info'] ?? []) as $item): ?>
                                            <span class="tag"><strong><?php echo htmlspecialchars($item['label']); ?>:</strong> <?php echo htmlspecialchars($item['value']); ?></span>
                                        <?php endforeach; ?>
                                        
                                    </div>

                                    <div class="about-intro">
                                        <?php echo nl2br(htmlspecialchars($data['about']['intro'] ?? '这里是介绍区块，你可以在后台写一些自己的简介。')); ?>
                                    </div>

                                    <h4 class="title">技能</h4>
                                    <div class="skills-row">
                                        <?php foreach (($data['about']['skills'] ?? []) as $skill): ?>
                                            <div class="skill-block">
                                                <div class="inner-box">
                                                    <div class="graph-outer">
                                                        <input type="text" class="dial" data-fgColor="#ff6700"
                                                            data-bgColor="#f5f5f5" data-width="90" data-height="90"
                                                            data-linecap="normal" value="<?php echo (int)($skill['value'] ?? 0); ?>">
                                                        <div class="inner-text count-box"><span class="count-text"
                                                                data-stop="<?php echo (int)($skill['value'] ?? 0); ?>" data-speed="2000"></span>%</div>
                                                    </div>
                                                    <h3><?php echo htmlspecialchars($skill['name'] ?? ''); ?></h3>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                </div>
                            </div>
                            <!-- end item -->

                            <!-- card item -->
                            <div class="card-resume card-item" id="resume"
                                style="background: radial-gradient(#ff6700, transparent);">
                                <div class="card-inner">
                                    <h4 class="title">足迹</h4>
                                    <div class="row clearfix">
                                        <!--Column-->
                                        <div class="timeline-column col-md-6 col-sm-12 col-xs-12">
                                            <div class="inner">
                                                <div class="col-header">
                                                    <div class="icon-box">
                                                        <div class="icon-inner">
                                                            <div class="icon"><span class="flaticon-book"></span></div>
                                                        </div>
                                                    </div>
                                                    <h2>学历</h2>
                                                </div>

                                                <!--Timeline Block-->
                                                <?php foreach (($data['resume']['education'] ?? []) as $item): ?>
                                                    <div class="timeline-block">
                                                        <div class="inner-box">
                                                            <h4><?php echo htmlspecialchars($item['title'] ?? ''); ?></h4>
                                                            <div class="date"><span><?php echo htmlspecialchars($item['date'] ?? ''); ?></span> / <?php echo htmlspecialchars($item['text'] ?? ''); ?></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>


                                            </div>
                                        </div>

                                        <!--Column-->
                                        <div class="timeline-column col-md-6 col-sm-12 col-xs-12">
                                            <div class="inner">
                                                <div class="col-header">
                                                    <div class="icon-box">
                                                        <div class="icon-inner">
                                                            <div class="icon"><span class="flaticon-case"></span></div>
                                                        </div>
                                                    </div>
                                                    <h2>经验</h2>
                                                </div>

                                                <!--Timeline Block-->
                                                <?php foreach (($data['resume']['experience'] ?? []) as $item): ?>
                                                    <div class="timeline-block">
                                                        <div class="inner-box">
                                                            <h4><?php echo htmlspecialchars($item['title'] ?? ''); ?></h4>
                                                            <div class="date"><span><?php echo htmlspecialchars($item['date'] ?? ''); ?></span> / <?php echo htmlspecialchars($item['text'] ?? ''); ?></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- end item -->
                            <!-- card item -->

                            <script>
                                function phpyufa() {
                                    location.href = 'https://www.runoob.com/';
                                }

                                

                            </script>


                            <div class="card-services card-item" id="services"
                                style="background: radial-gradient(#ff6700, transparent);">
                                <div class="card-inner">
                                    <h4 class="title">经验</h4>
                                    <div class="row">
                                        <div class="services-block col-md-6" onclick="phpyufa()">
                                            <div class="inner-box">
                                                <div class="icon-box"><span class="icon flaticon-pen-tool"></span></div>
                                                <h4><a href="#">记单词？</a></h4>
                                                <div class="text">单词是记不住的，词语是不会背的，用菜鸟教程是肯定的。</div>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- end item -->
                            <!-- card item -->
                            <style>
                                h4,
                                .header ul li.active a {
                                    font-family: 'Noto Serif SC', serif;
                                    font-weight: 400;
                                }

                                .btn-style-one {
                                    background: #ff670000;
                                    border: 1px solid #ffffff;
                                }
                            </style>
                            <div class="card-projects card-item" id="work"
                                style="background: radial-gradient(#ff6700, transparent);">
                                <div class="card-inner">
                                    <h4 class="title">相册</h4>
                                    <div class="row">
                                        <?php if (!empty($data['gallery'])): ?>
                                            <?php foreach ($data['gallery'] as $item): ?>
                                                <div class="projects-block col-md-6">
                                                    <div class="inner-box">
                                                        <figure class="image">
                                                            <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                                                alt="<?php echo htmlspecialchars($item['title']); ?>" style="object-fit: cover;height: 350px;">
                                                            <div class="overlay">
                                                                <a class="lightbox-image option-btn" title="<?php echo htmlspecialchars($item['title']); ?>"
                                                                    data-fancybox-group="example-gallery"
                                                                    href="<?php echo htmlspecialchars($item['link'] ?: $item['image']); ?>">
                                                                    <i class="fa fa-search"></i>
                                                                </a>
                                                            </div>
                                                        </figure>
                                                        <div class="caption-title">
                                                            <h3><a href="<?php echo htmlspecialchars($item['link'] ?: '#'); ?>"><?php echo htmlspecialchars($item['title']); ?></a></h3>
                                                            <span><?php echo htmlspecialchars($item['description']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12" style="padding: 40px; text-align: center; color: rgba(255,255,255,0.8);">
                                                暂无相册内容，请在后台添加。
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- end item -->
                            <!-- card item -->
                            <div class="card-blog card-item" id="blog"
                                style="background: radial-gradient(#ff6700, transparent);">
                                <div class="card-inner">
                                    <h4 class="title">站点</h4>

                                    <?php if (!empty($data['site_links'])): ?>
                                        <?php foreach ($data['site_links'] as $link): ?>
                                            <div class="news-block">
                                                <div class="inner-box">
                                                    <div class="image">
                                                        <img src="<?php echo htmlspecialchars($link['image']); ?>" alt="<?php echo htmlspecialchars($link['title']); ?>">
                                                        <div class="overlay">
                                                            <a class="link-btn" href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank">
                                                                <i class="fa fa-link"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="lower-content">
                                                        <h4><a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank"><?php echo htmlspecialchars($link['title']); ?></a></h4>
                                                        <div class="post-meta">by DSJIE. / <span><?php echo htmlspecialchars($link['meta']); ?></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="news-block">
                                            <div class="inner-box" style="text-align: center; padding: 40px;">
                                                <div style="color: rgba(255,255,255,0.8);">暂无站点内容，请在后台添加。</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- end item -->
                            <!-- card item -->
                            <div class="card-contact card-item" id="contact"
                                style="background: radial-gradient(#ff6700, transparent);">
                                <div class="card-inner">
                                    <h3 class="title">留言</h3>
                                    <h4>详细信息</h4>
                                    <div class="row clearfix">
                                        <div class="col-md-6">
                                            <ul class="list-style-two">
                                                <?php
                                                $contactDetails = $data['contact']['details'] ?? [];
                                                if (!empty($contactDetails)):
                                                    foreach ($contactDetails as $detail):
                                                        $icon = htmlspecialchars($detail['icon'] ?? 'flaticon-signs');
                                                        $value = $detail['value'] ?? '';
                                                ?>
                                                    <li><span class="icon <?php echo $icon; ?>"></span><?php echo nl2br(str_replace('&lt;br&gt;', '<br>', htmlspecialchars($value))); ?></li>
                                                <?php
                                                    endforeach;
                                                else:
                                                ?>
                                                    <li style="color: rgba(255,255,255,0.8);">暂无详细信息，请在后台添加。</li>
                                                <?php endif; ?>
                                                <li><span class="icon flaticon-signs"></span>您的IP/地址：<br><?php echo $ip_if; ?><br><?php echo $gsd ?></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="map-section">
                                                <div class="home-google-map">
                                                    <div class="google-map" id="contact-google-map"
                                                        data-map-lat="40.700843" data-map-lng="-74.004012"
                                                        data-icon-path="images/icons/map-marker.png"
                                                        data-map-title="Chester" data-map-zoom="11">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4>留言信息</h4>
                                    <?php if ($sent === '1'): ?>
                                        <div style="padding:10px 15px; background: rgba(76, 175, 80, 0.15); border: 1px solid rgba(76, 175, 80, 0.35); border-radius: 8px; margin-bottom: 15px;">留言已提交成功，感谢你的反馈！</div>
                                    <?php elseif ($sent === '0'): ?>
                                        <div style="padding:10px 15px; background: rgba(244, 67, 54, 0.15); border: 1px solid rgba(244, 67, 54, 0.35); border-radius: 8px; margin-bottom: 15px;">提交失败，请确认必填项已填写。</div>
                                    <?php endif; ?>
                                    <!-- Contact Form -->
                                    <div class="contact-form">
                                        <!--Comment Form-->
                                        <form method="post" action="sendemail.php" id="contact-form">
                                            <div class="row clearfix">

                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="username" placeholder="姓名" required>
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <input type="email" name="email" placeholder="邮箱" required>
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="subject" placeholder="主题" required>
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="phone" placeholder="电话" required>
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <input type="text" name="address" placeholder="地址" required>
                                                </div>

                                                <div class="col-lg-12 col-md-12 form-group">
                                                    <textarea name="message" placeholder="你要说的...如果看见可能会通过留言的邮箱回复哟~"></textarea>
                                                </div>

                                                <div class="col-lg-12 col-md-12 form-group">
                                                    <button class="theme-btn btn-style-two huise" type="submit"
                                                        name="submit-form">留言 </button>
                                                </div>

                                            </div>
                                        </form>

                                    </div>
                                    <!--End Contact Form -->

                                </div>
                            </div>
                            <!-- end item -->

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!--End pagewrapper-->


    <script src="js/jquery.js"></script>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/owl.js"></script>
    <script src="js/wow.js"></script>
    <script src="js/appear.js"></script>
    <script src="js/jquery.fancybox.js"></script>
    <script src="js/element-in-view.js"></script>
    <script src="js/knob.js"></script>
    <script src="js/validate.js"></script>
    <script src="js/mousemoveparallax.js"></script>
    <script src="js/pagenav.js"></script>
    <script src="js/jquery-type.js"></script>
    <script src="js/jquery.nicescroll.min.js"></script>
    <script src="js/particle-alone.js"></script>
    <script src="js/script.js"></script>

    <!--Google Map APi Key-->
    <script src="https://ditu.google.cn/maps/api/js?key=AIzaSyATY4Rxc8jNvDpsK8ZetC7JyN4PFVYGCGM"></script>
    <script src="js/gmaps.js"></script>
    <script src="js/map-script.js"></script>
    <!--End Google Map APi-->
<script>
    console.log("%DSJIE.| 2748859465", "color:#fff;background:#000;padding:8px 15px;font-weight: 700;border-radius:15px");
    console.log("%c 个人主页_导航页  | Powered by DSJIE.", "color:#fff;background:linear-gradient(to right, hsl(206.57deg 100% 61.11%) 0%, hsl(57deg 100% 85.15%) 100%);padding:8px 15px;border-radius:15px");

    console.log("%c DSJIE.主页: https://dsjie375.cn", "color:#fff;background:linear-gradient(to right, hsl(206.57deg 100% 61.11%) 0%, hsl(57deg 100% 85.15%) 100%);padding:8px 15px;border-radius:15px");

    console.log("%c 免费开源  禁止抄袭 禁止售卖 感谢配合 GitHub开源", "color:#fadfa3;background:#333;padding:8px 15px;");
    console.log(
        "%c 开源地址: https://github.com/DSJIE375/DSJIE.Personal-homepage",
        "color: #fff; background-image:linear-gradient(to right, #1FA2FF 0%, #fdfdfd  21%, #fafafa  100%); padding: 8px 15px; border-radius: 5px;"
    );
</script>
</body>

</html>
