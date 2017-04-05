<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>港云外汇代理信息平台</title>
    <script src="/Public/Common/js/jquery-1.10.2.min.js"></script>

    <style>

        body,html{

            padding:0; margin: 0; height: 100%;

        }

        .navbar {

            background: black; min-height: 48px; filter: none; top: 0;  right: 0; left: 0; z-index: 1030;

            margin-bottom: 0; padding-left: 0;  padding-right: 0; -webkit-box-shadow: none; -moz-box-shadow: none;

            box-shadow: none; border: 0; -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0;

            border-bottom: 4px solid #2a4053;

        }

        #sidebar {

            width: 20%; float: left;  background: #f9f9f9; height: 100%; z-index: 700;

        }

        #sidebar .slimScrollDiv {

            float: left!important;

            width: 100%!important;

        }

        #sidebar * {

            overflow-x: hidden; overflow-y: visible;

            white-space: nowrap; text-overflow: ellipsis;

        }

        #sidebar #sidebar-content {

            float: left; width: 100%!important;

        }

        #sidebar ul#nav {

            list-style: none; margin: 15px 0; padding: 0;

        }

        #sidebar ul#nav li {

            display: block; margin: 0; padding: 0; border: 0; border-bottom: 1px solid #ebebeb;

        }

        #sidebar ul#nav li a {

            display: block; position: relative; margin: 0; border: 0; padding: 15px 15px;

            padding-left: 20px; color: #555; text-decoration: none; text-shadow: 0 1px 0 #fff;

            font-size: 13px; font-weight: 600;  text-transform: uppercase;

        }

        #sidebar ul#nav ul.sub-menu {

            list-style: none; clear: both; margin: 0; padding: 0;

            font-size: 13px; background: #f1f1f1; border-bottom: 1px solid #fff;

        }

        .navbar-right {

            float: right!important;

        }

        .navbar-nav>li {

            float: left;

            list-style: none;

        }

        .navbar .navbar-brand {

            padding: 0; line-height: 48px; color: #fff; text-shadow: 0 1px 0 #000;

            font-size: 18px; width: 230px; text-overflow: ellipsis; white-space: nowrap;

            overflow-x: hidden; text-align: left; padding-left: 0; max-width: none;

            margin-left: 0!important;  margin-right: 0;

        }

        a.navbar-brand {

            text-decoration: none;

        }

        .navbar .nav>li {

            line-height: 28px;

            border-right: 1px solid rgba(0,0,0,0.2);

        }

        .navbar .nav>li>a {

            color: #fff; font-size: 15px; text-shadow: 0 1px 0 #000; padding: 14px 18px; text-decoration: none;

        }

    </style>

</head>

<body>

<header class="navbar">

    <a class="navbar-brand" href="#">

       

        &nbsp;&nbsp;<strong>港云外汇代理信息平台</strong>

    </a>

    <ul class="nav navbar-nav navbar-right">

        <li>

            <a class="dropdown-toggle username" data-toggle="dropdown" href="javascript:void(0)">

                <?php echo $_SESSION['channel']['username']; ?>

            </a>

        </li>

        <li><a href="/user/channel/logout.html">退出</a></li>

    </ul>

</header>

<div id="sidebar" class="sidebar-fixed">

    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;"><div id="sidebar-content" style=" width: auto; overflow-x: hidden;overflow-y:visible;height: 100%;">
        <ul id="nav">
            <li>
                <a href="/User/Channel/index.html" target="content" class="ajax-url" src="/User/Channel/index.html">控制台</a>
            </li>
            <li class="">
				<a class="menu-group" href="javascript:void(0)">
                <span>客户管理</span>
                <span class="fa wx-sidenav-icon fa-chevron-right"></span>
            </a>
            <ul class="sub-menu" >
                <li>
                <a href="/User/Channel/custom.html" target="content" class="ajax-url"><i
                        class="fa fa-chevron-right"></i>&nbsp;客户列表</a>
                </li>
                 <li>
                <a href="/User/Channel/adduser.html" target="content" class="ajax-url"><i
                        class="fa fa-chevron-right"></i>&nbsp;添加客户</a>
                </li>
            </ul>
            </li>
            <li>
                <a href="/User/Channel/order.html" target="content" class="ajax-url" src="/User/Channel/order.html">交易记录</a>
            </li>
            <li>

                <a href="/User/Channel/invitelist.html" target="content" class="ajax-url" src="/User/Channel/invitelist.html">邀请码</a>

            </li>
            <li>

                <a href="/User/Channel/change_pass.html" target="content" class="ajax-url" src="/User/Channel/change_pass.html">修改密码</a>

            </li>

            

        

        </ul>

        <div class="fill-nav-space"></div></div><div class="slimScrollBar" style="width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 147.687804878049px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div>

    </div>

</div>

<iframe style="border: 0;width: 79%;height:100%;float: right;" name="content" src="/User/Channel/index.html">



</iframe>

</body>



<script>

    $(".menu-group").click(function(e){

        $(this).siblings(".sub-menu").toggle(300);

    });

</script>



</html>