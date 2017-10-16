<?php
/**
 * Socket服务端
 * author: flycorn
 * email: ym1992it@163.com
 * time: 16/12/27 下午3:43
 */
//设置无限请求超时时间
set_time_limit(0);

$ip = '127.0.0.1';
$port = 8099;

//创建socket
if(($sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0) {
    echo "socket_create() 失败的原因是:".socket_strerror($sock)."\n";
    exit();
}
//把socket绑定在一个IP地址和端口上
if(($ret = socket_bind($sock,$ip,$port)) < 0) {
    echo "socket_bind() 失败的原因是:".socket_strerror($ret)."\n";
    exit();
}
//监听由指定socket的所有连接
if(($ret = socket_listen($sock,4)) < 0) {
    echo "socket_listen() 失败的原因是:".socket_strerror($ret)."\n";
    exit();
}

//次数
$count = 0;

do{
    //接收一个Socket连接
    if (($msgsock = socket_accept($sock)) < 0) {
        echo "socket_accept() failed: reason: " . socket_strerror($msgsock) . "\n";
        break;
    } else {
        //发送到客户端
        $msg = "测试成功! \n";
        socket_write($msgsock, $msg, strlen($msg));

        echo "测试成功了啊\n";
        // 获得客户端的输入
        $buf = socket_read($msgsock, 2048);

        $talkback = "收到的信息:$buf\n";
        echo $talkback;

        //第5次结束
        if(++$count >= 5){
            break;
        }
    }
    //关闭socket
    socket_close($msgsock);
}while(true);
