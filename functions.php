<?php
include '/mnt/config/dbKeys/auth.php';
include '/mnt/config/php/config.php';
/**database connection**/

//connect to database
function db__connect($servername="",$username="",$password="",$dbname="")
{
	/* reset */
	if($servername=="") $servername=$GLOBALS['g_db_serverName'];
	if($username=="") $username=$GLOBALS['g_db_usrName'];
	if($password=="") $password=$GLOBALS['g_db_psswd'];
	if($dbname=="") $dbname=$GLOBALS['g_db_dbName'];

	if($servername == "log"){

		$servername = $GLOBALS['g_db_log_serverName'];
		$username = $GLOBALS['g_db_log_usrName'];
		$password = $GLOBALS['g_db_log_psswd'];
		$dbname = $GLOBALS['g_db_log_dbName'];
	}elseif($servername == "yulu"){

		$servername = $GLOBALS['g_db_log_serverName'];
		$username = $GLOBALS['g_db_log_usrName'];
		$password = $GLOBALS['g_db_log_psswd'];
		$dbname = "yulu";
	}
	
	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) 
	{
		die("Mysql Connect Failed: " . $conn->connect_error);
	} 

	return ($conn);
}

//get table row number::(data_cnnct var,table name) ::(row number)
function db__rowNum($conn,$table,$clmnName="",$value="",$clmnName2="",$value2="")
{
	
	$table=db__antisql($table);
	$clmnName=db__antisql($clmnName);
	$value=db__antisql($value);
	$clmnName2=db__antisql($clmnName2);
	$value2=db__antisql($value2);
	
	
	if($clmnName=="") $sql = "SELECT COUNT(*) FROM $table";
	elseif($clmnName2=="") $sql = "SELECT COUNT(*) FROM $table where $clmnName='$value'";
	else $sql = "SELECT COUNT(*) FROM $table where $clmnName='$value' AND $clmnName2='$value2'";
	
	$row_count = $conn->query($sql);   
	list($row_num) = $row_count->fetch_row(); 
	return ($row_num);
}

//get row data from database::(data_cnnct var, table name,column name, column value)::(row info)
function db__getData($conn,$table,$clmnName="",$value="",$clmnName2="",$value2="")
{
	
	$table=db__antisql($table);
	$clmnName=db__antisql($clmnName);
	$value=db__antisql($value);
	$clmnName2=db__antisql($clmnName2);
	$value2=db__antisql($value2);
		

	if($clmnName=="") $sql = "SELECT * FROM $table";
	elseif($clmnName2=="") $sql = "SELECT * FROM $table where $clmnName='$value'";
	else $sql = "SELECT * FROM $table where $clmnName='$value' AND $clmnName2='$value2'";
		
	$result = $conn->query($sql);
	//no data
	if ($result->num_rows > 0) {}else{return array();}

	$i=0;
	$arr=array();
	while($row = $result->fetch_assoc()) {
		$arr[$i++]=$row;
	}
	return ($arr);
}


//fnct for insert a row to database
function db__insertData($conn,$table,$content)
{	
	$table=db__antisql($table);
	
	$key=array_keys($content);
	
	$key=db__antisql($key);
	
	$sql="insert INTO $table (";
	
	for($i=0;$i<count($key);$i++)
	{
		$sql.="$key[$i]";
		if($i!=count($key)-1) $sql.=", ";
	}
	
	$sql.=") VALUES (";
	
	for($i=0;$i<count($key);$i++)
	{
		$tmp_key=$key[$i];
		$content[$tmp_key]=db__antisql($content[$tmp_key]);
		$sql.="'$content[$tmp_key]'";
		if($i!=count($key)-1) $sql.=", ";
	}
	
	$sql.=")";
	
	if (!($conn->query($sql) === TRUE))  echo "SQL Insert Error: " . $sql . "<br>" . $conn->error;

}


//fnct for update a row to database without check
function db__updateData($conn,$table,$content,$index)
{	
	$key=array_keys($content);
	$key=db__antisql($key);
	
	$sql="UPDATE $table SET ";
	
	for($i=0;$i<count($key);$i++)
	{
		$tmp_key=$key[$i];
		$content[$tmp_key]=db__antisql($content[$tmp_key]);
		$sql.="$key[$i]='$content[$tmp_key]'";
		if($i!=count($key)-1) $sql.=", ";
	}
	
	$key=array_keys($index);
	$key=db__antisql($key);
	
	$sql.=" WHERE ";
	
	for($i=0;$i<count($key);$i++)
	{
		$tmp_key=$key[$i];
		$index[$tmp_key]=db__antisql($index[$tmp_key]);
		$sql.="$tmp_key='$index[$tmp_key]'";
		if($i!=count($key)-1) $sql.=" AND ";
	}
	
	if (!($conn->query($sql) === TRUE))  echo "SQL Insert Error: " . $sql . "<br>" . $conn->error;

}




//push row data from database::(data_cnnct var, table name,column name, column value)::(row info)
function db__pushData($conn,$table,$content,$index="",$is_force=1)
{
	if($index)
	{
		$index_keys=array_keys($index);

		if(count($index_keys)==1) $result=db__rowNum($conn,$table,$index_keys[0],$index[$index_keys[0]]); 
			
		elseif(count($index_keys)==2)	$result=db__rowNum($conn,$table,$index_keys[0],$index[$index_keys[0]],$index_keys[1],$index[$index_keys[1]]); 
			
		else return -1;
			
		if($result>0) db__updateData($conn,$table,$content,$index);
		else if($is_force) db__insertData($conn,$table,$content);
			
	}
	else
		db__insertData($conn,$table,$content);
}


function db__delData($conn, $table, $clmnName, $value)
{
	$value=db__antisql($value);
	$clmnName=db__antisql($clmnName);

	$sql = "DELETE FROM $table WHERE $clmnName = '$value'";
	$conn->query($sql);
}


//anti sql
function db__antisql($str)
{
	return(str_ireplace("'","",$str));
}


/*****log******/
function yimian__log($table, $val, $index = "", $cnt = null){

	if(!isset($cnt)) $cnt = db__connect("log");
	if($index != "") db__pushData($cnt, $table, $val, $index);
	else db__pushData($cnt, $table, $val);
}

/** get from address **/
function get_from(){

	if($_SERVER['HTTP_REFERER']) return $_SERVER['HTTP_REFERER'];
	elseif($_REQUEST['from']) return $_REQUEST['from'];
}

function get_from_domain(){

	$str = str_replace("http://","",get_from());
	$str = str_replace("https://","",$str);
	$strdomain = explode("/",$str);
	return $strdomain[0];
}


/*****curl*****/

function curl__post($url = '', $param) {

    if(empty($url)) {
        return false;
    }

    $o = "";
    foreach ($param as $k => $v) { 
        $o .= "$k=".urlencode($v)."&" ;
    }

    $postUrl = $url;
    $curlPost = substr($o,0,-1);
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}



/* get IP */
function get_ip(){
    return getIp();
}
function getIp()
{
	if (isset($_SERVER)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

			foreach ($arr as $ip) {
				$ip = trim($ip);

				if ($ip != 'unknown') {
					$realip = $ip;
					break;
				}
			}
		} else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			$realip = $_SERVER['REMOTE_ADDR'];
		} else {
			$realip = '0.0.0.0';
		}
	} else if (getenv('HTTP_X_FORWARDED_FOR')) {
		$realip = getenv('HTTP_X_FORWARDED_FOR');
	} else if (getenv('HTTP_CLIENT_IP')) {
		$realip = getenv('HTTP_CLIENT_IP');
	} else {
		$realip = getenv('REMOTE_ADDR');
	}

	preg_match('/[\\d\\.]{7,15}/', $realip, $onlineip);
	$realip = (!empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0');
	return $realip;
}

/* obs sdk */
require '/home/lib/huaweicloud-sdk-php-obs/vendor/autoload.php';
require '/home/lib/huaweicloud-sdk-php-obs/obs-autoloader.php';

use Obs\ObsClient;


/* obs get video */
function getVideo($path, $time = 120*60){

    return obsSign($path, $time);
}


/* obs get img */
function getImg($path, $time = 300){

    return str_replace('yimian-image.obs.cn-east-2.myhuaweicloud.com:443','image.yimian.xyz',obsSign($path, $time));
}




function obsSign($path, $expires = 300){ 

    $obsClient = new ObsClient([
            'key' => $GLOBALS['huawei_AK'],
            'secret' => $GLOBALS['huawei_SK'],
            'endpoint' => "https://obs.cn-east-2.myhuaweicloud.com",
    ]);


    $resp = $obsClient->createSignedUrl( [ 
        'Method' => 'GET',
        'Bucket' => 'yimian-image',
        'Key' => $path,
        'Expires' => $expires
    ] );

    $obsClient -> close();
    return $resp['SignedUrl'];

}





function getImgsInfo($type, $isFast = false){ 
    
    $obsClient = new ObsClient([
            'key' => $GLOBALS['huawei_AK'],
            'secret' => $GLOBALS['huawei_SK'],
            'endpoint' => "https://obs.cn-east-2.myhuaweicloud.com",
    ]);

    $resp = $obsClient -> listObjects([
        'Bucket' => 'yimian-image',
        'MaxKeys' => 10000,
        'Prefix' => $type.'/',
        'Marker' => $type.'/img'
    ]);


    //ini_set("pcre.backtrack_limit" , -1); ini_set("pcre.recursion_limit" , -1); ini_set("memory_limit" , "1024M");

    foreach($resp['Contents'] as $index => $val){
        $str .= $val['Key'].'  ';
    };

    $obsClient -> close();


    preg_match_all('/img_(\S*?)_(\d{2,4})x(\d{2,4})_(\S*?)_(\S*?)_(\S*?).(jpe?g|png|gif|svg)\b/', $str, $arr);

//echo var_dump($str);

    return $arr;

}

/*****gugu*****/

function yimian__gugu($body){

	$body = iconv("UTF-8","gbk//TRANSLIT",$body);
	$url = "http://open.memobird.cn/home/printpaper";
	return curl__post($url, array("ak" => $GLOBALS['ggj_ak'], "userID" => $GLOBALS['ggj_userID'], "memobirdID" => $GLOBALS['ggj_memobirdID'], "printcontent" => "T:".base64_encode($body)."", "timestamp" => "".time().""));
}


function gugu__send($ak, $userID, $memobirdID, $body){

	$body = iconv("UTF-8","gbk//TRANSLIT",$body);
	$url = "http://open.memobird.cn/home/printpaper";
	return curl__post($url, array("ak" => $ak, "userID" => $userID, "memobirdID" => $memobirdID, "printcontent" => "T:".base64_encode($body)."", "timestamp" => "".time().""));
}



/** function for mail **/

function yimian__mail($to, $subject, $body, $from){


    if($from == "") $from = "IoTcat 呓喵酱";
    if($body == "") $body = "额(⊙﹏⊙) 未找到指定的邮件内容耶( •̀ ω •́ )y<br/><br/>更多信息请咨询<a href = 'https://iotcat.me'>IoTcat</a>期待你的回应啦~";
    if($subject == "") $subject = "来自IoTcat的一声问候~";

    $data = array(
        'fromName' => $from, // 发件人名称
        'from' => "admin@iotcat.xyz", // 发件地址
        'to' => $to, // 收件地址
        'replyTo' => "i@iotcat.me", // 回信地址
        'subject' => $subject,
        'html' => $body
    );

    // 当前请求区域
    // 杭州
    // API地址
    $data['api'] = 'https://dm.aliyuncs.com/';
    // API版本号
    $data['version'] = '2015-11-23';
    // 机房信息
    $data['region'] = 'cn-hangzhou';

    // AccessKeyId
    $data['accessid'] = $GLOBALS['aym_AccessKey'];
    // AccessKeySecret
    $data['accesssecret'] = $GLOBALS['aym_SecretKey'];
    // 是否成功
    return aliyun($data);

}



//mail alliyun api
function aliyun($param)
{
    // 重新组合为阿里云所使用的参数
    $data = array(
        'Action' => 'SingleSendMail', // 操作接口名
        'AccountName' => $param['from'], // 发件地址
        'ReplyToAddress' => "true", // 回信地址
        'AddressType' => 1, // 地址类型
        'ToAddress' => $param['to'], // 收件地址
        'FromAlias' => $param['fromName'], // 发件人名称
        'Subject' => $param['subject'], // 邮件标题
        'HtmlBody' => $param['html'], // 邮件内容
        'Format' => 'JSON', // 返回JSON
        'Version' => $param['version'], // API版本号
        'AccessKeyId' => $param['accessid'], // Access Key ID
        'SignatureMethod' => 'HMAC-SHA1', // 签名方式
        'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'), // 请求时间
        'SignatureVersion' => '1.0', // 签名算法版本
        'SignatureNonce' => md5(time()), // 唯一随机数
        'RegionId' => $param['region'] // 机房信息
    );
    // 请求签名
    $data['Signature'] = sign($data, $param['accesssecret']);
    // 初始化Curl
    $ch = curl_init();
    // 设置为POST请求
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    // 请求地址
    curl_setopt($ch, CURLOPT_URL, $param['api']);
    // 返回数据
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // 提交参数
    curl_setopt($ch, CURLOPT_POSTFIELDS, getPostHttpBody($data));
    // 关闭ssl验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // 执行请求
    $result = curl_exec($ch);
    // 获取错误代码
    $errno = curl_errno($ch);
    // 获取错误信息
    $error = curl_error($ch);
    // 获取返回状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // 关闭请求
    curl_close($ch);
    // 成功标识
    $flag = TRUE;
    // 如果开启了Debug
    if (1) {
        // 记录时间
        $log = '[Aliyun] ' . date('Y-m-d H:i:s') . ': ' . PHP_EOL;
        // 如果失败
        if ( $errno ) {
            // 设置失败
            $flag = FALSE;
            $log .= '邮件发送失败, 错误代码：' . $errno . '，错误提示: ' . $error . PHP_EOL;
        }
        // 如果失败
        if ( 400 <= $httpCode ) {
            // 设置失败
            $flag = FALSE;
            // 尝试转换json
            if ( $json = json_decode($result) ) {
                $log .= '邮件发送失败，错误代码：' . $json->Code . '，错误提示：' . $json->Message . PHP_EOL;
            } else {
                $log .= '邮件发送失败, 请求返回HTTP Code：' . $httpCode . PHP_EOL;
            }
        }
        // 记录返回值
        $log .= '邮件发送返回数据：' . serialize($result) . PHP_EOL;
        // 写入文件
    }
    yimian__log("log_mail",array("timestamp" => date('Y-m-d H:i:s', time()), "to_" => $param['to'], "from_" => $param['fromName'], "subject" => $param['subject'], "body" => $param['html'], "success" => (($flag)?1:0), "return_" => $log));
    // 返回结果
    //echo $log;
    return $flag;
} 


/**
 * 阿里云签名
 *
 * @static
 * @access private
 *
 * @param array  $param        签名参数
 * @param string $accesssecret 秘钥
 *
 * @return string
 */
function sign($param, $accesssecret)
{
    // 参数排序
    ksort($param);
    // 组合基础
    $stringToSign = 'POST&' . percentEncode('/') . '&';
    // 临时变量
    $tmp = '';
    // 循环参数列表
    foreach ( $param as $k => $v ) {
        // 组合参数
        $tmp .= '&' . percentEncode($k) . '=' . percentEncode($v);
    }
    // 去除最后一个&
    $tmp = trim($tmp, '&');
    // 组合签名参数
    $stringToSign = $stringToSign . percentEncode($tmp);
    // 数据签名
    $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accesssecret . '&', TRUE));
    // 返回签名
    return $signature;
}

/**
 * 阿里云签名编码转换
 *
 * @static
 * @access private
 *
 * @param string $val 要转换的编码
 *
 * @return string|string[]|null
 */
function percentEncode($val)
{
    // URL编码
    $res = urlencode($val);
    // 加号转换为%20
    $res = preg_replace('/\+/', '%20', $res);
    // 星号转换为%2A
    $res = preg_replace('/\*/', '%2A', $res);
    // %7E转换为~
    $res = preg_replace('/%7E/', '~', $res);
    return $res;
}

/**
 * 阿里云请求参数组合
 *
 * @static
 * @access private
 *
 * @param array $param 发送参数
 *
 * @return bool|string
 */
function getPostHttpBody($param)
{
    // 空字符串
    $str = "";
    // 循环参数
    foreach ( $param as $k => $v ) {
        // 组合参数
        $str .= $k . '=' . urlencode($v) . '&';
    }
    // 去除第一个&
    return substr($str, 0, -1);
}


/* sms */
require __DIR__ . "/../../../lib/qcloudsms/src/index.php";

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;

use Qcloud\Sms\VoiceFileUploader;
use Qcloud\Sms\FileVoiceSender;
use Qcloud\Sms\TtsVoiceSender;


function yimian__sms($to, $tpl, $msg1, $msg2, $msg3){

    $msg = array();
    if($tpl == 3) array_push($msg, $msg1, $msg2, $msg3);
    else array_push($msg, $msg1, $msg2);

    $appid = $GLOBALS['sms_appid']; 

    $appkey = $GLOBALS['sms_appkey'];

    $smsSign = $GLOBALS['sms_smsSign']; 

    if($tpl == 1) $templateId = 287129; /*由于{1}，本站{2}。给您带来不便深表歉意！*/
    if($tpl == 2) $templateId = 300726; /*您好！您收到一条来自{1}的消息，内容是{2}。感谢您使用本站的服务！*/
    if($tpl == 3) $templateId = 205311; /*您{1}的{2}为{3}，请于5分钟内填写。如非本人操作，请忽略本短信。祝好！*/
    if($tpl == 4) $templateId = 244004; /*{1}已解决，本站{2}服务已恢复！给您带来不便深表歉意！特此告知！*/
    if($tpl == 5) $templateId = 300722; /*你好呀，你收到了一条来自访客{1}的评论，内容是{2}。感谢你使用本站的服务啦 ~*/

    try {
        $ssender = new SmsSingleSender($appid, $appkey);
        $params = $msg;
        $result = $ssender->sendWithParam("86", $to, $templateId,
                $params, $smsSign, "", "");  /* 签名参数未提供或者为空时，会使用默认签名发送短信*/
        $rsp = json_decode($result);
        echo $result;
    } catch(\Exception $e) {
        echo var_dump($e);
    }

}

