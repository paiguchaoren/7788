<?php 
//加载核心类
 require_once dirname(__FILE__).'/../include/class.main.php';

 //加载云配置文件
 require_once dirname(__FILE__).'/../save/config.php'; 
 require_once dirname(__FILE__).'/../save/yun.config.php'; 
 require_once dirname(__FILE__).'/../save/yun.match.php';
 require_once dirname(__FILE__).'/../save/yun.data.php';


 
//var_dump( YUN::getvideo("https://v.qq.com/x/cover/mzc0020002ka95z/d0036o17h0n.html"));
 
//echo (json_encode(YUN::parse("123",4)) );

class YUN
{
	public static function parse($val,$type=1)	
	{    
		global $YUN_MATCH;	 

	 switch ($type){	
             
              case 0 :   //使用本地库
                 
                 return self::getvod($val,false);		
             
		 case 1 : //地址播放视频				 		    
                  $val=str_replace($YUN_MATCH["url_replace"],"",urldecode($val)); //url地址过滤		
		return self::getvideo($val);						 
		 case 2:	//标题播放视频		 
		    return self::getvideo($val,true);						
		case 3 :	//	搜索资源 使用ID   		
		   return self::getvideobyid($val["flag"],$val["id"],$val["part"]);
		  
		case 4 :   //搜索资源 使用标题
             
                   return self::getnames(rawurldecode($val));  

		default:	
		   return array('success'=>0,'code'=>0,'m'=>'input error!');
		
	  }	
		
   }
//检测链接跳转
 public static function getjmp($val,&$url,&$name,&$num)
 {
     global  $YUN_DATA;
     foreach ($YUN_DATA["url_jmp"] as $value) {
         if(preg_match('{'.$value['url'].'}i', $val)){ 
             $url=$value['href'];$name=$value['title'];$num=$value['part'];  
             return true;
         }
     }
    return false;
 }
   
 //根据url或视频名称取本地视频信息
 
 public static function getvod($word,$lswrod=false)
 {	
     global $CONFIG,$YUN_MATCH; 
     if(!$lswrod){   
    //视频地址替换,用于移动转换为pc版本
     foreach ($YUN_MATCH["url_match"] as $val => $value) 
    {     	 	 
	 if (preg_match($val,$word,$matches))
           {		  		 		 		         
		  for ($i=1;$i<sizeof($matches);$i++)
		    {		  	    
		     $value=str_replace('(?'.(string)$i.')',$matches[$i] ,$value);		   
		     }	       
		   $word=$value;
                  break;	
            }
     } 
    //简化url,去掉了头尾（scheme,query）便于模糊查询
    $word=parse_url($word);$word=@$word['host'].@$word['path'];
    }
    $videoinfo=array('success'=>0,'code'=>0);
    $part=0;
    foreach ($CONFIG['vod'] as $key => $val) 
   {
         if($val['off']==='0'){continue;}   
         //匹配到数据
       if(strstr($val[$lswrod?'name':'url'], $word) !== false)
       {
              $video=explode("#",$val['url']); 
              foreach ($video as $k => $v) 
            {
                $url=explode("$",$v); 
                if(strstr($url[1], $word) !== false)
                    {
                      $part= $k+1;
                      $vod=explode("#",$val['data']);
                      $url=explode("$", $vod[$k])[1];
                      $info[]=array('flag'=>'yun','flag_name'=>'云播','site'=>$k,'part'=>sizeof($vod),'video'=>$vod,);
                       //输出数据			 
		      $videoinfo['success']=1; 
		      $videoinfo['code']=200;
		      $videoinfo['title']=$val['name'];
		      $videoinfo['part']=$part;
		      $videoinfo['url']=$url;	
		      $videoinfo['type']='video';			 
	              $videoinfo['info']=$info;    
                       return $videoinfo;  
                  }
              }
         }
     }
       $videoinfo['m']="未找到资源!";
       return $videoinfo;	
 }
 
 
//根据url或视频名称取视频信息
 public static function getvideo($val,$lswrod=false)
 {	  
	global $YUN_MATCH,$YUN_CONFIG,$CONFIG; 
 
	$num=1;$name="";$url="";

        $api=$CONFIG["resource"];
        
	$videoinfo=array('success'=>0,'code'=>0);
        
        //取标题及集数信息
        
    	if($lswrod){
                 $name=$val["name"];             
                 $num=$val["part"]?:1;
         }else{
              if ($YUN_CONFIG["url_filter"]!="" && !preg_match('/'.$YUN_CONFIG["url_filter"]."/i",$val)){ $videoinfo['code']=0;$videoinfo['m']="url error!";return $videoinfo;  }
   	 
              if(!self::getjmp($val,$url,$name,$num)){if(!self::getname($val,$name,$num)){$videoinfo['code']=0;$videoinfo['m']="getname error!";return $videoinfo;}}
         }	  

	 //404判断,使用精确匹配  
          if( $YUN_MATCH["ERROR_404"]!="" &&  self::findstrs($name,$YUN_MATCH["ERROR_404"])){$videoinfo['code']=404;$videoinfo['m']="404 NOT FOUND";return $videoinfo;}
	
          
      /*  多线程搜索资源站，获取视频ID   */
      $urls=array();  
      foreach ($api as $key=> $val) 
      {
        if(!$val['off']){continue;}
        $parm=$val['type']==='1'?"/wd/":"?wd=";
        $urls[]=$val['url'].$parm.rawurlencode($name);
        $froms[]=$key;   
      }
      
    
      
      $data=HttpMulti::Run($urls);
      
      
       //exit($name);
          
   /*  多线程取视频列表信息，根据视频ID  */ 
    $urls=array();   
    foreach ($data as $key=>$val) 
    {
        $i=$froms[$key]; //源站序号
        $id=self::getvid($val);
        if($val && $id!==""){
            $urls[]=$api[$i]['url']."?ac=videolist&ids=".self::getvid($val); 
        }else{ 
            $urls[]="";
        } 
    }
    $data=HttpMulti::Run($urls);
          
    foreach ($data as  $key=>$html) 
   {
       $from=$froms[$key]; //源站序号
       if($html)
       {     
            $xml = simplexml_load_string($html);  
          
             foreach($xml->list->video->dl->dd as $video)	    
	   {   
	     $flag=(string)$video[flag];  
            
             $flag_name=sizeof($xml->list->video->dl->dd )==1?$api[$from]['name']:$flag;
             
             $vod=explode("#",(string)$video);
             
             /*     自动修复影片数据       */
             $vlist=explode("$",$vod[0]); 
             if(sizeof($vlist)<3)
              {
                 foreach($vod as &$mov){if(sizeof($vlist)==1){$mov="正片$".$mov."$".$f;}else{$mov=$mov."$".$f;}}
               }   
             /* 自动修复影片数据    代码结束    */
               
		 if($YUN_CONFIG["flag_filter"]=="" ||preg_match('/'.$YUN_CONFIG["flag_filter"]."/i",$flag)){$info[]=array('flag'=>$flag,'flag_name'=> $flag_name,'site'=>$i,'part'=>sizeof($vod),'video'=>$vod,);}             			 
 
             }	
        }   
              if(sizeof($info)>0 && $CONFIG["play"]['off']['allyun']==='0'){ break;} 
    }    
    
  
  //检查集数或期数，如果未匹配则返回假
    
     //匹配期数
     $matches=array();		
     $vods = preg_match('!#'.(string)$num.'.*?\$(.*?)(?=\$|#)!i', $video, $matches) ? trim($matches[1]) : '';
     if ($vods!=''){$videoinfo['url']=$vods;$videoinfo['play']=$flag; }

   if (sizeof($info[0]['video'])>=$num)
   {		  		 
	//结果先按集数降序排列再按资源站先后顺序排列。        
	foreach ( $info as $key => $row )
		{		   
           $num1[$key] = $row ['part'];
	   $num2[$key] = $row ['site'];		   
        } 
        array_multisort($num1,SORT_DESC ,$num2,SORT_ASC,$info);	
		    
		//检查集数，如果未匹配集数，设置为最后一个视频
		if(!$vods){$max=sizeof($info[0]['video']);if($max<$num){$num=$max;};}		 
		$vod=@$info[0]['video'][$num-1];$vod=explode('$', $vod); 
		//类型转换
		$type=$info[0]['flag'];		
		foreach($YUN_MATCH["type_match"] as $key => $val ){			
		    if (preg_match($key,$type,$matches)){			 
			    $type=$val;break;			 
		   }else if(preg_match($key,@$vod[1],$matches)){				 
			     $type=$val;break;				 
		    }				
		}
		 
		 //输出数据			 
		 $videoinfo['success']=1; 
		 $videoinfo['code']=200;
		 $videoinfo['title']=$name;
		 $videoinfo['part']=$num;
		 $videoinfo['url']=$url?$url:$vod[1];	
		 $videoinfo['type']=$type;			 
	         $videoinfo['info']=$info;
		 
    }else{
		
		 $videoinfo['m']="未找到资源!";
	}
	   
  return $videoinfo;	
 
 }
//根据资源站序号及视频ID取视频信息
 public static function getvideobyid($flag,$id)
 {	 
      global  $YUN_MACTH,$YUN_CONFIG,$CONFIG;
      if($flag=='888'){
             $vid=$CONFIG['vod'][$id];
             if($vid['data']!==""){
                  $video=explode("#",$vid['data']);
                   $videoinfo['type']='video';	
             }else{
                 $video=explode("#",$vid['url']);
                 $videoinfo['type']='jx';	
             }
             
             $info[]=array('flag'=>'vod','flag_name'=>$vid['name'],'site'=>0,'part'=>sizeof($video),'video'=>$video,);
             $url=explode("$",$video[0])[1];
                 //输出数据			 
		 $videoinfo['success']=1; 
		 $videoinfo['code']=200;
		 $videoinfo['title']=$vid['name'];
		 $videoinfo['part']=1;
		 $videoinfo['url']=$url;		 
	         $videoinfo['info']=$info;      
                 return $videoinfo;
      }

     $api=$CONFIG["resource"][$flag]['url'];
     $api_name=$CONFIG["resource"][$flag]['name'];
     
     $data=self::curl($api."?ac=videolist&ids=".$id);
     if($data)  
     {
         $xml = simplexml_load_string($data);
        
         if($xml)  
         {
            $img=(string)$xml->list->video->pic; 
            $name=(string)$xml->list->video->name;  
            
            
            foreach($xml->list->video->dl->dd as  $video)	    
	    {        
	     $flag=(string)$video[flag]; 
   
             $flag_name=sizeof($xml->list->video->dl->dd)==1?$api_name:$flag;
  
             
             $vod=explode("#",trim((string)$video));
              
             /*  自动修复影片数据   */
            
             $vlist=explode("$",$vod[0]); 
             if(sizeof($vlist)<3)
              {
                 foreach($vod as &$mov){if(sizeof($vlist)==1){$mov="正片$".$mov."$".$f;}else{$mov=$mov."$".$f;}}
               }   
               
             /* 自动修复影片数据    代码结束    */	 
               
                 if($YUN_CONFIG["flag_filter"]=="" || preg_match('/'.$YUN_CONFIG["flag_filter"]."/i",$flag)){$info[]=array('flag'=>$flag,'flag_name'=>$flag_name,'part'=>sizeof($vod),'video'=>$vod);}
	    }  
             //结果按集数降序排列。        
	     foreach ($info as $key => $row ){$num1[$key] = $row ['part'];} array_multisort($num1,SORT_DESC ,$info);
             $vod=$info[0]['video'][0];$vod=explode('$', $vod); $url=$vod[1];
             
             //类型转换
	      $type=$info[0]['flag'];		
	      foreach($YUN_MACTH["type_match"] as $key => $val )
              {			
		    if (preg_match($key,$type)){			 
			    $type=$val;break;			 
		   }else if(preg_match($key,$vod[1])){				 
			     $type=$val;break;				 
		    }				
		}
		//输出数据	
		 $videoinfo['success']=1; 
		 $videoinfo['code']=200;
		 $videoinfo['url']=$url;
                 $videoinfo['pic']=$img;	 
		 $videoinfo['title']=$name;
		 $videoinfo['part']=$part+1;	
		 $videoinfo['type']=$type;	         	 
	         $videoinfo['info']=$info;
         }
         
 
     }
          return $videoinfo; 
 
 }
 //视频搜索
  public static function getnames($name)
 {
    global $YUN_MACTH,$YUN_CONFIG,$CONFIG;
    $api=$CONFIG["resource"];
    $videoinfo=array('success'=>0,'code'=>0,'title'=>$name);
   
  //搜索本地库
    foreach($CONFIG['vod'] as $key=>$val)
    {
        if(strstr($val['name'], $name) !== false)
        {   
          $info[]=array('flag'=>'888','flag_name'=>$val['name'],'from'=>"云播",'type'=>"vod",'id'=>$key,'title'=>$val['name'],'img:'=>'null');
        }
     }
 
     $urls=array();
    //组合网址
    foreach ($api as $key=> $val) 
    {
        if(!$val['off']){continue;}
        $parm=$val['type']==='1'?"/wd/":"?wd=";
        $urls[]=$val['url'].$parm.rawurlencode($name);
        $froms[]=$key;   
    }
   
    //多线程获取网页数据
    $data=HttpMulti::Run($urls);
    
  if($CONFIG["play"]['off']['allsearch']==='1')
  {
    /*  */
   //处理多页
    //$urls=[];
      foreach ($data as  $key=>$html) 
   {
          $xml = simplexml_load_string($html); 
          $pagecount=$xml->list["pagecount"];
         for ($index = 2; $index <=$pagecount; $index++) {
              
             
             
             if($api[$froms[$key]]['type']){
                 
                      $urls[]=$api[$froms[$key]]['url']."/wd/".rawurlencode($name)."/pg/".$index;
             }else{
                      $urls[]=$api[$froms[$key]]['url']."?wd=".rawurlencode($name)."&pg=".$index;
             }
             $froms[]=$froms[$key];
         }
   }
  
   //多线程获取网页数据
   $data=HttpMulti::Run($urls);
   
 }
    foreach ($data as  $i=>$html) 
   {

       if($html)
       {     
            $xml = simplexml_load_string($html);  
            foreach($xml->list->video as $video)	    
	    {   	
			 $id=(string)$video->id;
                         $flag=(string)$video->dt;
			 $title=strip_tags((string)$video->name);
			 $type=(string)$video->type;   
                         $flag_name=sizeof($xml->list->video)>1?$flag:$api[$froms[$i]]['name']; 
			 //分类过滤
			  if($YUN_CONFIG["type_filter"]==='' || !preg_match('!'.$YUN_CONFIG["type_filter"].'!i', $type))
                        {
                         //搜索资源过滤
                          if($YUN_CONFIG["flag_filter"]==='' || !preg_match('!'.$YUN_CONFIG["flag_filter"].'!i', $title))
                           {    
                              $info[]=array('flag'=> $froms[$i],'flag_name'=>$flag_name,'from'=>$api[$froms[$i]]['name'],'type'=>$type,'id'=>$id,'title'=>$title,'img:'=>'null');  
                            }
			 }
	    }  
        }
    }
  
    if(isset($info)){$videoinfo['success']=1;  $videoinfo['info']= $info;}else{$videoinfo['code']=404;}
    return $videoinfo;
 }
 
 
  //正则快速取视频ID
  public static function getvid($data)
 { 
       $matches=array();  
       $id = preg_match('!<id>(\d*?)</id>!i', $data, $matches) ? trim($matches[1]) : '';			 
       return $id;      
  } 
 
 
 
 //取视频ID
  public static function getid($api,$name,$num)
 {
	  
     //根据标题取ID
	 $data=self::curl($api."?wd=".rawurlencode($name)); if ($data==""){return false;};	 

         
	 $xml=simplexml_load_string($data);$forst=false;
	 
	 if(!$xml){return false;}
	
	 //匹配标题对应ID
	 foreach($xml->list->video as $video)	    
	    {   			 			 
			$id=(string)$video->id;						
			 $video=(string)$video->name;		  
			 if ($video==$name){ return $id;} 		
		} 	 
	//如果未找到，取集数匹配的视频	
	if($num>1){
     	foreach($xml->list->video as $video)	    
	    {   			 			 
			$id=(string)$video->id;	
			$ret=self::curl($api."?ac=videolist&ids=".$id);if($ret==''){return false;} 
			$xm = simplexml_load_string($ret); if(empty($xm)){return false;}  
			$ret=(string)$xm->list->video->dl->dd[0];
			$vod=explode("#",$ret);
			if(sizeof($vod)>=$num){return $id;}
		}
    }			  
	  //如果还未未匹配到就取第一个资源；	 
	  
	$matches=array();  
    $id = preg_match('!<id>(\d*?)</id>!i', $data, $matches) ? trim($matches[1]) : '';	
    if ($id==""){return false;}				 
	return $id;
	 
 
 }
 //取视频名称及集数
 public static function getname($url,&$name,&$num)
 {
      global $YUN_MATCH,$YUN_CONFIG;
     
     
     //$title_replace,$title_match,$name_match,$url_match;
      $title='';$name='';$num=1;
   

  //视频地址替换,用于移动转换为pc版本
     foreach ($YUN_MATCH["url_match"] as $val => $value) 
    {     	 	 
	 if (preg_match($val,$url,$matches)){	
             
         
		  for ($i=1;$i<sizeof($matches);$i++)
		    {		  	    
		     $value=str_replace('(?'.(string)$i.')',$matches[$i] ,$value);		   
		    }	       
		   $url=$value;
           break;	 
		}
     }
	
    
     $data=self::curl($url);

   
     if($data==''){if(function_exists('file_get_contents')){$data= file_get_contents($url);}} if($data==""){return false;}
	 
   
   
  //调用配置预设正则，获取视频标题。
  foreach($YUN_MATCH["title_match"] as  $val=>$value) 
  {    
	 if (preg_match($val, $url))		 
	  {	
             foreach  ($value as $word) 
		 {	
                 if (preg_match($word, $data, $matches))
                      {	    
                           $title=$matches[1]?utf8(trim(strip_tags($matches[1]))):'';                   
                           break; 				   
	                 }	
		 }
          if($title!=""){break;} 	 
	  }  
  }
  
    
  
//过滤换行符
   $title=trim(str_replace(array("\r\n","\n","\r"),"",$title));
 

 //调用配置预设正则，获取视频名称和集数。
  foreach($YUN_MATCH["name_match"] as  $val=>$value) 
  {    
	 if (preg_match($val, $url))		 
	  {	     	 		  		    	 
           
             foreach  ($value as $word) 
		 {	
              if (preg_match($word, $title, $matches))	          
			 {	
			   $name=$matches[1] ? trim($matches[1]):'';
			   $num=$matches[2] ? trim($matches[2]):1;
			   break;	 				   
	          }	
		 }
        if($name!=""){break;} 	 
	  }  
  }
     $name=trim(str_replace($YUN_MATCH["title_replace"],"",$name));
     
     
     if(filter_input(INPUT_GET, 'dd')){echo "{'name':$name,'num':$num,'url':$url},";}
     
        return ($name!== "");   
 } 	
	 //检测字符串组的字符在字符串中是否存在
    public static function findstrs($str,$find,$separator="|")
	{
	  $ymarr = explode($separator,$find);
	  foreach ($ymarr as  $find){ if(strcasecmp($str,$find)==0){return true;}}  	 
      return false;
    }
	 
	public static function curl($url,$ref='')
	{
    	$params["ua"] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    	$params['ref'] = $ref;
      	return GlobalBase::curl($url,$params);
	}

	
}

