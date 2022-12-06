<?php 
if(function_exists("opcache_reset")){opcache_reset();} //清除PHP脚本缓存
require_once '../include/class.main.php';
if(file_exists('../save/config.php')){include '../save/config.php';}
session_start(); 
if(empty($_SESSION['hashstr']) || $_SESSION['hashstr']!==md5((isset($CONFIG["user"])?$CONFIG["user"]:"admin").(isset($CONFIG["pass"])?$CONFIG["pass"]:MD5("admin888")))){echo "<script>location.href='load.php?url=login.htm'</script>";exit();}
$username=$_SESSION['username'];






