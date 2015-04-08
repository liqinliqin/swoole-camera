<?php
$serv = new swoole_websocket_server('0.0.0.0', 9501);

$serv->on('Request', function($req, $resp) {
    if ($req->server['request_uri'] == '/camera') {
        $html = file_get_contents(__DIR__.'/camera.html');
    } else {
        $html = file_get_contents(__DIR__.'/index.html');
    }
    $resp->end($html);
});

$serv->on('Message', function($serv, $frame) {
    $connections = $serv->connection_list();
    foreach($connections as $fd)
    {
        $info = $serv->connection_info($fd);
        if ($fd != $frame->fd and $info['websocket_status'] > 1)
        {
            $serv->push($fd, $frame->data);
        }
    }
});

$serv->start();
