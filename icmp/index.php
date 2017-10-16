<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 16/7/21
 * Time: 上午12:58
 * Description: 使用ICMP协议,模拟ping程序
 */

// 只有root用户才能发icmp报文，因此运行时需要用sudo
require_once('ICMP.php');

//标识符随便填的,序列号也是随便填的211
$data='Z'.'F'.chr(0).chr(211);

//ping程序凑够64byte的报文,这里包括IP首部20byte
for($i=0;$i<56;$i++){
    $data.=chr(0);
}


$host='www.baidu.com';
$startTime=microtime();


$result=sendPackage($host,8,0,$data,function($message){

    global $host;
    global $startTime;

    //按照字节解析成数组
    $message = unpack('C*', $message);

    $seq=$message[28];

    $ttl=$message[9];

    $endTime=microtime();

    echo (count($message)-20)." bytes from $host: icmp_seq=$seq  ttl=$ttl  time=".(round((($endTime-$startTime)*1000),3))." ms".PHP_EOL;

});

if($result!=null){

    echo "Error :$result";

}
