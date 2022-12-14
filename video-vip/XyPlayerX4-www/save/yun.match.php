<?php
$YUN_MATCH=array (
  'ERROR_404' => '',
  'type_match' => 
  array (
    '!m3u8!i' => 'hls',
    '!\\/share\\/!i' => 'url',
  ),
  'url_replace' => 
  array (
    0 => '&tdsourcetag=s_pcqq_aiomsg',
  ),
  'title_replace' => 
  array (
    0 => '独播',
    1 => '全集',
    2 => '高清在线观看',
    3 => '英语版',
    4 => '英文版',
    5 => '国语版',
    6 => '原声版',
    7 => '普通话版',
    8 => '《',
    9 => '》',
    10 => '【',
    11 => '】',
    12 => '（',
    13 => '）',
    14 => '(',
    15 => ')',
    16 => ':',
    17 => 'VIP',
    18 => '|',
    19 => 'iQiyi',
    20 => 'Episode',
  ),
  'title_match' => 
  array (
    '!iqiyi.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!youku.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!qq.com!i' => 
    array (
      0 => '{VIDEO_INFO.*?"title"="(.*?)"}i',
    ),
    '!mgtv.com!i' => 
    array (
      0 => '{.*?"videoName":"(.*?)"}i',
    ),
    '!le.com!i' => 
    array (
      0 => '{irTitle.*?content="(.*?)"}i',
    ),
    '!sohu.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!pptv.com!i' => 
    array (
      0 => '{og:title.*?content="(.*?)"}i',
    ),
    '!tudou.com!i' => 
    array (
      0 => '{.*?td-playbase__info[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
      1 => '{.*?play-video-desc[\\s\\S]*?(<div[\\s\\S]*?</div>)}i',
    ),
    '!bilibili.com!i' => 
    array (
      0 => '{.*?media-wrapper[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
      1 => '{.*?ep-info-center[\\s\\S]*?(<div[\\s\\S]*?</div>)}i',
    ),
    '!huya.com!i' => 
    array (
      0 => '{.*?video-detail[\\s\\S]*?(<h2[\\s\\S]*?</h2>)}i',
      1 => '{.*?video-title-new-t[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
    ),
    '!douyu.com|miguvideo.com!i' => 
    array (
      0 => '{.*?video-title[\\s\\S]*?(<h1[\\s\\S]*?</h1>)}i',
      1 => '{.*?video-describe[\\s\\S]*?(<div[\\s\\S]*?</div>)}i',
    ),
    '!^.*$!i' => 
    array (
      0 => '{<title.*?>(.*?)(?:-|—|_|——|-).*?</title>}i',
    ),
  ),
  'name_match' => 
  array (
    '!qq.com!i' => 
    array (
      0 => '{(.*?)_(.*?)}i',
      1 => '{^(?:《|)(.*?)(?:》|)(?:第|)(\\d+)(?=话|集|期).*?}i',
      2 => '{(.*?)($)}i',
    ),
    '!mgtv.com!i' => 
    array (
      0 => '{^(.*)(?:：|:|\\s|$)[\\s\\S]*?(?:第|)(\\d+)(?:话|集|期)}i',
    ),
    '!iqiyi.com!i' => 
    array (
      0 => '/^(\\s*?).*?第(\\d+)集/i',
      1 => '/^(.*?)(\\d+)/i',
      2 => '/^(.*?)(?:-|—|_|——|-|$)/i',
    ),
    '!le.com|tudou.com!i' => 
    array (
      0 => '/^(.*?)(\\d+)/i',
    ),
    '!pptv.com!i' => 
    array (
      0 => '/^(.*?)第(\\d+)(?=话|集|期)/i',
      1 => '/^(.*?)(?:-|—|_|——|-|$)/i',
    ),
    '!bilibili.com!i' => 
    array (
      0 => '/^(.*?)：(?:第|)(\\d+)(?=话|集)/i',
    ),
    '!miguvideo.com!i' => 
    array (
      0 => '/^(?:《|)(.*?)(?:》|)(?:第|)(\\d+)(?=话|集)/i',
    ),
    '!^.*$!i' => 
    array (
      0 => '{^(.*?)(?:第|)(\\d+)(?:话|集|期)}i',
      1 => '{《(.*?)》.*?}',
      2 => '{^(.*?)\\s*(?:：|:|\\s)}i',
      3 => '{^(.*?)(?:第|)(\\d+)(?:集|期|话|)$}i',
      4 => '/^(.*?)$/i',
    ),
  ),
  'url_match' => 
  array (
    '!m.v.qq.com.*?cid=(.*?)&vid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1)/(?2).html',
    '!m.v.qq.com.*?cid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1).html',
    '!m.v.qq.com/cover/r/(.*?)cid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1).html',
    '!m.v.qq.com/cover/./(.*?)\\.html\\?vid=(.*?)(?:&|$)!i' => 'https://v.qq.com/x/cover/(?1)/(?2).html',
    '!m.v.qq.com/(.*)$!i' => 'https://v.qq.com/(?1)',
    '!m.fun.tv/mplay/\\?mid=(\\d+)&vid=(\\d+)(?:&|$)!i' => 'https://www.fun.tv/vplay/g-(?1).v-(?2)',
    '!http://(.*?mgtv.com/.*?html)!i' => 'https://(?1)',
    '!m.youku.com/video/id_(.*?)==.html!i' => 'https://v.youku.com/v_show/id_(?1)==.html',
    '!m.bilibili.com/(.*?)!i' => 'https://www.bilibili.com/(?1)',
    '!mgtv.com.*?/(\\d+).html!i' => 'https://pcweb.api.mgtv.com/video/info?vid=(?1)',
  ),
);
