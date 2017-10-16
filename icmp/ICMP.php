<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 16/7/21
 * Time: 上午1:01
 * Description: 模拟发送ICMP报文
 */


/**
 * @param $host string 主机ip
 * @param $type int    icmp报文类型
 * @param $code int    icmp报文代码
 * @param $data string icmp报文数据
 * @param $callbackfunc string 请求完成之后的回调函数,由上层模块决定
 * @description 向主机发送icmp报文
 *
 * icmp 报文头  | 1字节类型 | 1字节代码 | 2字节校验和 |
 */
function sendPackage($host,$type,$code,$data,$callbackfunc){


    //icmp错误信息
    $g_icmp_error=null;

    //封装icmp报文
    //icmp头
    $package=chr($type).chr($code);
    $package.=chr(0).chr(0);
    $package.=$data;


    //设置校验和
    setSum($package);


    //发送报文
    $socket=socket_create(AF_INET,SOCK_RAW,getprotobyname('icmp')) or die('Cannot create socket');


    socket_sendto($socket,$package,strlen($package),0,$host,0);


    $read   = array($socket);
    $write  = NULL;
    $except = NULL;

    $select = socket_select($read, $write, $except, 0, 300 * 1000);

    if ($select === NULL)
    {
        $g_icmp_error = "Select Error";
        socket_close($socket);
        return $g_icmp_error;
    }
    elseif ($select === 0)
    {
        $g_icmp_error = "Timeout";
        socket_close($socket);
        return $g_icmp_error;
    }

    socket_recvfrom($socket, $recv, 65535, 0, $host, $port);


    //回调结果处理函数
    call_user_func($callbackfunc,$recv);

    return $g_icmp_error;

}


/**
 * @param $data string 报文数据
 * @description 设置icmp报文校验和
 */
function setSum(&$data){

    $list=unpack('n*',$data);

    $length=strlen($data);

    $sum=array_sum($list);

    if($length%2){

        $tmp=unpack('C*',$data[$length-1]);
        $sum+=$tmp[1];

    }

    $sum=($sum>>16)+($sum&0xffff);

    $sum+=($sum>>16);

    $r=pack('n*',~$sum);

    $data[2]=$r[0];
    $data[3]=$r[1];

}
