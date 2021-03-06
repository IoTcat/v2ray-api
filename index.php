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


    if($host == 'vmess.yimian.xyz'){
        $pack = array(
            "v" => "2",
            "ps" => "Los_Angeles",
            "add" => "usa.yimian.xyz",
            "port" => "443",
            "id" => $uuid,
            "aid" => "88",
            "net" => "ws",
            "type" => "none",
            "host" => $host,
            "path" => "/",
            "tls" => "tls"
        );
    }elseif($host == 'v-china.yimian.xyz'){
        $pack = array(
            "v" => "2",
            "ps" => "China-Taian",
            "add" => "v-china.yimian.xyz",
            "port" => "443",
            "id" => $uuid,
            "aid" => "88",
            "net" => "ws",
            "type" => "none",
            "host" => $host,
            "path" => "/",
            "tls" => "tls"
        );
    
    }else{
        $pack = array(
            "v" => "2",
            "ps" => "Unknown",
            "add" => $host,
            "port" => "443",
            "id" => $uuid,
            "aid" => "88",
            "net" => "ws",
            "type" => "none",
            "host" => $host,
            "path" => "/",
            "tls" => "tls"
        );
    
    }
    return 'vmess://'.base64_encode(json_encode($pack));
}

