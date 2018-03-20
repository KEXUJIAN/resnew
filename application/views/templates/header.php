<?php

use Res\Model\User;

if (isset($title)):
    $title = is_array($title) ? $title : [$title];
    $title = '库存系统 | ' . implode(' | ', $title);
endif;

$user = APP::getUser() ?? false;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? '库存系统' ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/jquery-toast-plugin/1.3.2/jquery.toast.min.css" rel="stylesheet">
    <?php if ($user): ?>
    <link href="https://cdn.bootcss.com/datatables/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet">
    <?php endif;?>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script src="https://cdn.bootcss.com/bootbox.js/4.4.0/bootbox.min.js"></script>
    <script src="https://cdn.bootcss.com/js-sha1/0.6.0/sha1.min.js"></script>
    <?php if ($user): ?>
    <script src="https://cdn.bootcss.com/datatables/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.bootcss.com/datatables/1.10.16/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.bootcss.com/blueimp-file-upload/9.19.2/js/vendor/jquery.ui.widget.min.js"></script>
    <script src="https://cdn.bootcss.com/blueimp-file-upload/9.19.2/js/jquery.iframe-transport.min.js"></script>
    <script src="https://cdn.bootcss.com/blueimp-file-upload/9.19.2/js/jquery.fileupload.min.js"></script>
    <script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>
    <script src="https://cdn.bootcss.com/select2/4.0.5/js/i18n/zh-CN.js"></script>
    <?php endif;?>
    <script src="/asset/app.6781cbdbe6418279eace.js"></script>
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
        .sidebar {
            position: fixed;
            width: 23%;
            padding: 15px;
            left: 15px;
        }
        .content-panel {
            padding: 10px;
            float: left;
            width: 100%;
        }
        .panel-assets {
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .ajax-table {
            width: 100% !important;
        }
        .data-table-action-wrapper {
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .data-table-action-wrapper .collapse {
            width: 100%;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .ajax-table .index-label {
            width: 100%;
            text-align: center;
        }
        .ajax-table .long-data {
            display: inline-block;
            max-width: 100px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .action-button {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .required::after {
            content: '*';
            color: red;
        }
        .badge-jump {
            position: relative;
            background-color: #337ab7;
            animation: jump 2s linear infinite;
        }
        @keyframes jump {
            0%, 30%, 100% {
                transform: initial;
            }
            10% {
                transform: translateY(-15px);
            }
        }
        .no-resize {
            resize: none !important;
        }
        .navbar-fixed-bottom .navbar-nav>li>a.service {
            background-color: #337ab7;
            color: #ffffff;
        }
        .navbar-fixed-bottom .navbar-nav>li>a.service:hover {
            background-color: #ffffff;
                color: #337ab7;
        }
    </style>
</head>
<body <?php if ($pageId ?? ''): echo 'id="' . $pageId . '"'; endif; ?>>
    <?php if ($user): ?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="<?=$titleNavClass ?? 'container'?>">
            <ul class="nav navbar-nav navbar-left">
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                        资源
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/assets/phone">测试机</a></li>
                        <li class="divider"></li>
                        <li><a class="dropdown-item" href="/assets/simcard">测试卡</a></li>
                    </ul>
                </li>
                <?php if (User::ROLE_MANAGER === $user->role()): ?>
                <li>
                    <a href="/admin/console">
                        <i class="fa fa-cog"></i>
                        管理后台
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a id="notification-bell" href="/user/profile/notification" data-url="/notification/count">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-jump"></span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user-circle"></i>
                        <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li id="name" data-value="javascript:;">
                            <a class="dropdown-item" href="javascript:;">欢迎! <strong><?=$user->username(); ?></strong></a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="dropdown-item" href="/user/profile">
                                <i class="fa fa-user"></i>
                                个人页
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="dropdown-item" href="/assets/inventory">
                                <i class="fa fa-cube"></i>
                                我的库存
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a id="logout" class="bg-danger" href="/user/logout">
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