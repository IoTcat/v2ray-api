<?php

if(isset($_REQUEST['uid'])) $uid = $_REQUEST['uid']; else die();
if(isset($_REQUEST['key'])) $key = $_REQUEST['key']; else die();


$o = '';


if($uid == 'cp' && $key == '2333')
$o .= 'vmess://ew0KICAidiI6ICIyIiwNCiAgInBzIjogIkxvc19BbmdlbGVzIiwNCiAgImFkZCI6ICJ2bWVzcy55aW1pYW4ueHl6IiwNCiAgInBvcnQiOiAiNDQzIiwNCiAgImlkIjogIjRkN2E2NjAyLTE3N2YtNDAyNi04MjU5LWEyNWU2NGZhMjBhZSIsDQogICJhaWQiOiAiMjMzIiwNCiAgIm5ldCI6ICJ3cyIsDQogICJ0eXBlIjogIm5vbmUiLA0KICAiaG9zdCI6ICJ2bWVzcy55aW1pYW4ueHl6IiwNCiAgInBhdGgiOiAiLyIsDQogICJ0bHMiOiAidGxzIg0KfQ==
';


if($uid == 'ushio-win' && $key == '666')
$o .= 'vmess://ew0KICAidiI6ICIyIiwNCiAgInBzIjogIkxvc19BbmdlbGVzIiwNCiAgImFkZCI6ICJ2bWVzcy55aW1pYW4ueHl6IiwNCiAgInBvcnQiOiAiNDQzIiwNCiAgImlkIjogIjRkN2E2NjAyLTE3N2YtNDAyNi04MjU5LWEyNWU2NGZhMjBhZSIsDQogICJhaWQiOiAiMjMzIiwNCiAgIm5ldCI6ICJ3cyIsDQogICJ0eXBlIjogIm5vbmUiLA0KICAiaG9zdCI6ICJ2bWVzcy55aW1pYW4ueHl6IiwNCiAgInBhdGgiOiAiLyIsDQogICJ0bHMiOiAidGxzIg0KfQ==
';

if(!$o){
    echo '';
}else{
    echo base64_encode($o);
}
