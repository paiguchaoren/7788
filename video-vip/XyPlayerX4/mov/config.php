<?php
 $CONFIG =  array (
  'title' => '星源影视',
  'templets' => 'bilibili',
  'logo' => '',
  'cache' => 
  array (
    'type' => '1',
    'prefix' => 'mov_',
    'prot' => '6379',
    'ip' => '127.0.0.1',
    'pass' => '',
    'time' => '30m',
  ),
  'parse' => 
  array (
    0 => '$host/?wd=$title&part=$part&url=',
    1 => 'https://www.xymav.com/?url=',
    2 => 'https://z1.m1907.cn/?jx=',
    3 => 'https://jx.aidouer.net/?url=',
  ),
  'keywords' => '最新电影、最新电视剧、最新动漫、最新综艺',
  'description' => '星源影视为您提供2021年最新电影、最新电视剧、最新动漫、在线观看。',
  'playinfo' => '<div style="text-align:center">
<span  style="color:#ff0000;">温馨提示：不能播放请更换播放列表或者切换线路！</span>
<br>
 <span style="color:#1d953f">如果还是不行,请点击下方☞
 <a href="../../?v=$title" style="color:#ff0000" target="playBox">《这里》搜索</a>
 </div>',
  'footcode' => '',
  'copyright' => '<p>本站内容均来自于互联网资源实时采集</p><p>本网站仅用做学习交流</p>',
);
?>