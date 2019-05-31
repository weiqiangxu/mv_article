<?php


/**
* $str Unicode编码后的字符串
* $decoding 原始字符串的编码，默认utf-8
* $prefix 编码字符串的前缀，默认"&#"
* $postfix 编码字符串的后缀，默认";"
*/
function unicode_decode($unistr, $encoding = 'utf-8', $prefix = '&#', $postfix = ';')
{	
	$orig_str= $unistr;
	$arruni = explode($prefix, $unistr);
	$unistr = '';
	for ($i = 1, $len = count($arruni); $i < $len; $i++)
	{
		if (strlen($postfix) > 0) {
			$arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
		}
		$temp = intval($arruni[$i]);
		$unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
	}
	$str = str_split(iconv('UCS-2', $encoding, $unistr));

	foreach ($str as $v)
	{
		$orig_str = preg_replace('/&#[\S]+?;/', $v, $orig_str,1);
	}
	return $orig_str;
}


// 去除iphone,ios,emoji表情
function removeEmoji($text)
{
    $clean_text = "";
    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text     = preg_replace($regexEmoticons, '', $text);
    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text   = preg_replace($regexSymbols, '', $clean_text);
    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text     = preg_replace($regexTransport, '', $clean_text);
    // Match Miscellaneous Symbols
    $regexMisc  = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);
    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text    = preg_replace($regexDingbats, '', $clean_text);
    return $clean_text;
}

// 过滤掉emoji表情
function filterEmoji($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

    return $str;
}


/**
 * 手动文本日志
 * @author xu
 * @copyright 2018-07-30
 */
function logdebug($text,$step) {
    file_put_contents(APP_DOWN.$step.'-'.date('YmdH',time()).'.log', date('Y-m-d H:i:s').'  '.$text."\n", FILE_APPEND);
}

/* GET方法CURL */
function curlGet($url, $isFarmat = false)
{
    $ch     = curl_init($url);
    $output = "";
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证证书
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept'           => '*/*',
        'Accept-Charset'   => 'UTF-8,*;q=0.5',
        'Accept-Encoding'  => 'gzip,deflate,sdch',
        'Accept-Language'  => 'zh-CN,zh;q=0.8',
        'Connection'       => 'keep-alive',
        'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
        'User-Agent'       => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11',
        'X-Requested-With' => 'XMLHttpRequest',
    ));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.3.7; zh-cn; c8650 Build/GWK74) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1s');

    $output = curl_exec($ch);

    curl_close($ch); //关闭链接

    if ($isFarmat && $output) {$output = json_decode($output, true);}

    return $output;
}

/**
 * [decodeUnicode 对中文字符(unicode码)进行解码]
 * @param  [array] $str [要解码的数组]
 * @return [array] $str [解码后的数组]
 */
function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}



// 检测是否含有屏蔽字段
function Escape($text)
{
    $filter_arr = include(APP_PATH.'/lib/Filter.php');
    $diy_filter = array(
        '看不懂','他妈的','傻逼','他妈','坑爹','qq','微信号',
        'QQ','qq','扣扣','微信','weixin','威信','群','不好看','垃圾','盗版',
        '掌阅','掌阅系统','掌阅科技','卖毒品','官场黑暗浑浊','国家公务员','诈骗',
        '阅币','王思聪','阅饼','做扒手','治安管理条例','党争残酷','滚床单',
        '骗钱','嫖客','代金券','欺骗消费者','变态',
        '行政拘留','作假','做假','纵横中文网','辣鸡作者',
        '广电总局','精虫上脑','脱了','你吗的','vip免费','骗子',
        '国共合作','下药','二逼','抄袭','开枪','掌阅书城','免费下载','呻吟',
        '上床','种马','种马小说','审核','书圈','小受','调戏','script','br','alert','去死','犯贱',
        'amp','','╯▽╰','#x','&lt','&','//','&gt;',']','$','%','{'
    );
    $filter_arr = array_merge($filter_arr,$diy_filter);
    $data = str_replace($filter_arr, '***', $text);
    // 最终返回检测结果
    if(preg_match('/\*\*\*/', $data)){
        return false;
    }else{
        //3个中文字符以下也不要|UTF-8编码格式
        if(preg_match("/[\x{4e00}-\x{9fa5}]{3,}/u", $data)){
            return true;
        }else{
            return false;
        }

    }
}

// 获取评论的人的GGID
function getRandGid()
{
    $ggid = include(APP_PATH.'/lib/ggid.php');
    $key = array_rand($ggid);
    $ggid = $ggid[$key];
    return $ggid;
}



/**
 * [bookinfo 获取图书基本信息]
 * @param  [int] $book_id [图书id]
 * @return [array]  [图书基本信息]
 * bookinfo cache_time 6小时
 */
function bookinfo($book_id) {
    $url  = "http://content.mfs.book.lan/book/".substr($book_id,-2)."/".$book_id."/bookinfo.html";
    $res  = curlGet($url, true);
    $res  = decodeUnicode($res);
    $book = json_decode($res['data'], true);
    return $book;
}

/**
 * [bookmenu 获取图书章节列表]
 * @param  [int] $book_id [图书id]
 * @return [array]  [图书章节列表]
 */
function bookmenu($book_id) {
    $url  = "http://content.mfs.book.lan/book/".substr($book_id,-2)."/".$book_id."/menu.html";
    $res  = curlGet($url, true);
    $res  = decodeUnicode($res);
    $menu = json_decode($res['data'], true);
    return $menu;
}



if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey = null, $indexKey = null)
    {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}



// 数字转阿拉伯
function numtochr($num,$mode=true) {
    $char = array("零","一","二","三","四","五","六","七","八","九");
    $dw = array("","十","百","千","","万","亿","兆");
    $dec = "点";
    $retval = "";
    if($mode)
        preg_match_all("/^0*(\d*)\.?(\d*)/",$num, $ar);
    else
        preg_match_all("/(\d*)\.?(\d*)/",$num, $ar);
    if($ar[2][0] != "")
        $retval = $dec . $this->ch_num($ar[2][0],false); //如果有小数，先递归处理小数
    if($ar[1][0] != "") {
        $str = strrev($ar[1][0]);
        for($i=0;$i<strlen($str);$i++) {
            $out[$i] = $char[$str[$i]];
            if($mode) {
                $out[$i] .= $str[$i] != "0"? $dw[$i%4] : "";
                if($str[$i]+$str[$i-1] == 0)
                    $out[$i] = "";
                if($i%4 == 0)
                    $out[$i] .= $dw[4+floor($i/4)];
            }
        }
        $retval = join("",array_reverse($out)) . $retval;
    }
    return $retval;
}


//中文转阿拉伯
function chrtonum($str){
    $num=0;
    $bins=array("零","一","二","三","四","五","六","七","八","九",'a'=>"个",'b'=>"十",'c'=>"百",'d'=>"千",'e'=>"万");
    $bits=array('a'=>1,'b'=>10,'c'=>100,'d'=>1000,'e'=>10000);
    foreach($bins as $key=>$val){
        if(strpos(" ".$str,$val)) $str=str_replace($val,$key,$str);
    }
    foreach(str_split($str,2) as $val){
        $temp=str_split($val,1);
        if(count($temp)==1) $temp[1]="a";
        if(isset($bits[$temp[0]])){
            $num=$bits[$temp[0]]+(int)$temp[1];
        }else{
            $num+=(int)$temp[0]*$bits[$temp[1]];
        }
    }
    return $num;
}


/**
* 数字转换为中文
* @param  string|integer|float  $num  目标数字
* @param  integer $mode 模式[true:金额（默认）,false:普通数字表示]
* @param  boolean $sim 使用小写（默认）
* @return string
*/
 function number2chinese($num,$mode = true,$sim = true){
    if(!is_numeric($num)) return '含有非数字非小数点字符！';
    $char    = $sim ? array('零','一','二','三','四','五','六','七','八','九')
    : array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
    $unit    = $sim ? array('','十','百','千','','万','亿','兆')
    : array('','拾','佰','仟','','萬','億','兆');
    $retval  = $mode ? '':'点';
    //小数部分
    if(strpos($num, '.')){
        list($num,$dec) = explode('.', $num);
        $dec = strval(round($dec,2));
        if($mode){
            $retval .= "{$char[$dec['0']]}角{$char[$dec['1']]}分";
        }else{
            for($i = 0,$c = strlen($dec);$i < $c;$i++) {
                $retval .= $char[$dec[$i]];
            }
        }
    }
    //整数部分
    $str = $mode ? strrev(intval($num)) : strrev($num);
    for($i = 0,$c = strlen($str);$i < $c;$i++) {
        $out[$i] = $char[$str[$i]];
        if($mode){
            $out[$i] .= $str[$i] != '0'? $unit[$i%4] : '';
                if($i>1 and $str[$i]+$str[$i-1] == 0){
                $out[$i] = '';
            }
                if($i%4 == 0){
                $out[$i] .= $unit[4+floor($i/4)];
            }
        }
    }
    $retval = join('',array_reverse($out)) . $retval;
    return $retval;
 }

 /**
    * 下载远程文件到本地
    * @param [string] $source_url 文件远程地址
    * @param [string] $save_file 文件本地存储路径
    * @author xu
    * @copyright 2018-11-14
*/
function download_remote_file($source_url, $save_file)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $source_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $file = curl_exec($ch);
    curl_close($ch);
    @mkdir(dirname($save_file),0777,true);
    file_put_contents($save_file, $file);
    return true;
}

/**
    * 发送post请求
    * @param [string] $source_url 文件远程地址
    * @param [string] $save_file 文件本地存储路径
    * @author xu
    * @copyright 2018-11-14
*/
function curl_post($url, $post_data,$reture_cookie = false ,$cookie = '')
{
    $ch = curl_init();
    //设置接口地址
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
    //关闭https验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //设置请求类型为POST
    curl_setopt($ch, CURLOPT_POST, 1);
    //设置post的数据
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    //请求数据成功默认不输出数据到页面（这个应该每个都要吧，/苦笑）
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //至关重要，CURLINFO_HEADER_OUT选项可以拿到请求头信息
    curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
    if($cookie){
        curl_setopt($ch,CURLOPT_COOKIE,$cookie);    
    }
    // 获取头部信息 
    if($reture_cookie){
        curl_setopt($ch, CURLOPT_HEADER, 1); 
        //发出请求
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    //发出请求
    $html = curl_exec($ch);
    curl_close($ch);

    //返回数据
    return $html;
}



/** 
 *      把秒数转换为时分秒的格式 
 *      @param Int $times 时间，单位 秒 
 *      @return String 
 */  
function secToTime($times){  
    $result = '00:00:00';  
    if ($times>0) {  
        $hour = floor($times/3600);  
        $minute = floor(($times-3600 * $hour)/60);  
        $second = floor((($times-3600 * $hour) - 60 * $minute) % 60);  
        $result = $hour.':'.$minute.':'.$second;  
    }  
    return $result;  
}  


/** 
 * 删除文件夹 
 * @param [str] $dir 文件夹路径 
 */ 
function deleteDir($dir) {
    if (!$handle = @opendir($dir)) {
        return false;
    }
    while (false !== ($file = readdir($handle))) {
        if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
            $file = $dir . '/' . $file;
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                @unlink($file);
            }
        }

    }
    @rmdir($dir);
}


/** 
 * 删除文件夹 
 *
 *
 * @param [str] $path 图片所在路径
 * @author xu 435861851@qq.com
 */ 
function checkImages($path) {
    if(!file_exists($path)){
        return false;
    }
    try{
        $imageInfo = getimagesize($path);
        return !empty($imageInfo) && is_array($imageInfo) ? true : false;
    } catch (\Exception $e) {
        return false;
    }
}


/** 
 * PHP stdClass Object转array 
 *
 *
 * @param [obj] $obj 对象
 * @author https://www.cnblogs.com/zhangqie/p/8241908.html
 */ 
function object_array($object) {
    if(is_object($object)) {
        $array = (array)$object; 
    }
    if(is_array($object)) {
        foreach($object as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}