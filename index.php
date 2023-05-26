<?php

include './functions.php';
header('Access-Control-Allow-Origin:*');

if(isset($_REQUEST['hash'])) $hash = $_REQUEST['hash']; else die();
if(isset($_REQUEST['type'])) $type = $_REQUEST['type']; else $type = 'vmess';

$cnn = db__connect();
$res_account = db__getData($cnn, "account", "hash", $hash);

if(!count($res_account)){
    die();
}

$res_vmess = db__getData($cnn, "vmess", "uid", $res_account[0]['uid']);

$o = '';
$items = Array();

foreach($res_vmess as $item){
    if($item['state']){
        $o .= gCode($item['host'], $item['uuid']).'
';
	$items[$item['host']] = $item['uuid'];
    }
}

if($type=='clash'){
  header("Content-Type: text/plain");
  echo gClash($items);
  die();
}


if(!$o && $_GET['auth'] == 'iotcat'){

    $json = json_decode(file_get_contents('/mnt/config/v2ray/uuid.json'));
    foreach($json as $item){
        if(!db__rowNum($cnn, "vmess", "uuid", $item)){
            db__pushData($cnn, "vmess", array(
                "uid" => $res_account[0]['uid'],
                "state"=>'1',
                "host"=>"vmess.yimian.xyz",
                "uuid"=>$item,
                "comments"=>"auto by v2ray.api"
            ));
            $o .= gCode('vmess.yimian.xyz', $item);
            echo base64_encode($o);
            die();
        }
    }
    echo '';
}else{
    echo base64_encode($o);
}


$o = '';



function gCode($host, $uuid){


    if($host == 'v-usa'){
        $pack = array(
            "v" => "2",
            "ps" => "Los_Angeles",
            "add" => "net.yimian.xyz",
            "port" => "80",
            "id" => $uuid,
            "aid" => "0",
            "scy" => "auto",
            "net" => "ws",
            "type" => "none",
            "host" => "net.yimian.xyz",
            "path" => "/v-usa/abcdefg",
            "tls" => ""
        );
        //return 'vless://'.$uuid.'@v-usa.yimian.xyz:443?encryption=none&security=tls&type=ws&host=v-usa.yimian.xyz&path=%2f#Los_Angeles';
    }elseif($host == 'v-china'){
        $pack = array(
            "v" => "2",
            "ps" => "China-Taian",
            "add" => "net.yimian.xyz",
            "port" => "80",
            "id" => $uuid,
            "aid" => "0",
            "scy" => "auto",
            "net" => "ws",
            "type" => "none",
            "host" => "net.yimian.xyz",
            "path" => "/v-china/abcdefg",
            "tls" => ""
        );
        //return 'vless://'.$uuid.'@v-china.yimian.xyz:443?encryption=none&security=tls&type=ws&host=v-china.yimian.xyz&path=%2f#China-Taian';
    }else{
        $pack = array(
            "v" => "2",
            "ps" => "Unknown",
            "add" => $host,
            "port" => "443",
            "id" => $uuid,
            "aid" => "0",
            "net" => "ws",
            "type" => "none",
            "host" => $host,
            "path" => "/",
            "tls" => "tls"
        );
    
    }
    return 'vmess://'.base64_encode(json_encode($pack));
}


function gClash($items){
$oo = 'mixed-port: 7890
allow-lan: true
mode: Rule
log-level: info
external-controller: :9090
proxies:
';
if(array_key_exists('v-usa', $items))
$oo .= '  - {name: Los_Angeles, server: net.yimian.xyz, port: 80, type: vmess, uuid: '.$items['v-usa'].', alterId: 0, cipher: auto, tls: false, network: ws, ws-opts: {path: /v-usa/abcdefg, headers: {Host: net.yimian.xyz}}}
';
if(array_key_exists('v-china', $items))
$oo .= '  - {name: 🇨🇳 China-Taian, server: net.yimian.xyz, port: 80, type: vmess, uuid: '.$items['v-china'].', alterId: 0, cipher: auto, tls: false, network: ws, ws-opts: {path: /v-china/abcdefg, headers: {Host: net.yimian.xyz}}}
';
$oo.='proxy-groups:
  - name: Proxies-US
    type: select
    proxies:
';
if(array_key_exists('v-usa', $items))
$oo.='      - Los_Angeles
';
$oo .='      - DIRECT
  - name: Proxies-CN
    type: select
    proxies:
';
if(array_key_exists('v-china', $items))
$oo.='      - 🇨🇳 China-Taian
';
$oo.='      - DIRECT
  - name: Netease
    type: select
    proxies:
      - DIRECT
      - Proxies-CN
  - name: Bilibili
    type: select
    proxies:
      - DIRECT
      - Proxies-CN
  - name: gfw
    type: select
    proxies:
      - DIRECT
      - Proxies-US
  - name: IP-CN
    type: select
    proxies:
      - DIRECT
      - Proxies-CN
      - Proxies-US
  - name: IP-not-CN
    type: select
    proxies:
      - DIRECT
      - Proxies-US
      - Proxies-CN
rule-providers:
  gfw:
    type: http
    behavior: domain
    url: "https://cdn.jsdelivr.net/gh/Loyalsoldier/clash-rules@release/gfw.txt"
    path: ./ruleset/gfw.yaml
    interval: 86400
  private:
    type: http
    behavior: domain
    url: "https://cdn.jsdelivr.net/gh/Loyalsoldier/clash-rules@release/private.txt"
    path: ./ruleset/private.yaml
    interval: 86400
  cncidr:
    type: http
    behavior: ipcidr
    url: "https://cdn.jsdelivr.net/gh/Loyalsoldier/clash-rules@release/cncidr.txt"
    path: ./ruleset/cncidr.yaml
    interval: 86400
rules:
 - DOMAIN-SUFFIX,163yun.com,Netease
 - DOMAIN-SUFFIX,music.163.com,Netease
 - DOMAIN-SUFFIX,music.126.net,Netease
 - DOMAIN-SUFFIX,api.iplay.163.com,Netease
 - DOMAIN-SUFFIX,apm.music.163.com,Netease
 - DOMAIN-SUFFIX,apm3.music.163.com,Netease
 - DOMAIN-SUFFIX,interface.music.163.com,Netease
 - DOMAIN-SUFFIX,interface3.music.163.com,Netease
 - DOMAIN-SUFFIX,mam.netease.com,Netease
 - DOMAIN-SUFFIX,hz.netease.com,Netease
 - DOMAIN-SUFFIX,bilibili.com,Bilibili
 - DOMAIN-SUFFIX,bilivideo.com,Bilibili
 - DOMAIN-SUFFIX,biliapi.net,Bilibili
 - RULE-SET,gfw,gfw
 - GEOIP,LAN,DIRECT
 - RULE-SET,private,DIRECT
 - GEOIP,CN,IP-CN
 - RULE-SET,cncidr,IP-CN
 - MATCH,IP-not-CN';

return $oo;
}
