<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? '' ?></title>
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/jquery-toast-plugin/1.3.2/jquery.toast.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script src="/asset/app.f3678996a21c4464153d.js"></script>
    <style type="text/css">
        body {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            overflow: hidden;
        }
        .res-container {
            position: absolute;
            top: 60px;
            bottom: 60px;
            left: 0;
            right: 0;
            overflow: auto;
        }
    </style>
</head>
<body>
    <?php if ($user = APP::$user ?? true): ?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <ul class="nav navbar-nav navbar-left">
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle navbar-text" data-toggle="dropdown">
                        资源
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:;">测试机</a></li>
                        <li class="divider"></li>
                        <li><a class="dropdown-item" href="javascript:;">测试卡</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user-circle"></i>
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li id="name" data-value="javascript:;">
                            <a class="dropdown-item" href="javascript:;">欢迎! <strong>哈哈</strong></a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="dropdown-item" href="javascript:;">
                                <i class="fa fa-home"></i>
                                个人资料
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="dropdown-item" href="javascript:;">
                                <i class="fa"></i>
                                我的
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a id="logout" href="javascript:;">
                                <i class="fa fa-power-off text-danger"></i> 退出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>
    <div class="res-container">