<?php

include './functions.php';
header('Access-Control-Allow-Origin:*');

if(isset($_REQUEST['hash'])) $hash = $_REQUEST['hash']; else die();

$cnn = db__connect();
$res_account = db__getData($cnn, "account", "hash", $hash);

if(!count($res_account)){
    die();
}

$res_vmess = db__getData($cnn, "vmess", "uid", $res_account[0]['uid']);

$o = '';


foreach($res_vmess as $item){
    if($item['state']){
        $o .= gCode($item['host'], $item['uuid']).'
';
    }
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

