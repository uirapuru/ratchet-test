<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use RatchetTest\Component\Server\NotificationServer;
use Symfony\Component\Debug\Debug;

require dirname(__DIR__) . '/../../../vendor/autoload.php';

$loader = require_once __DIR__.'/../../../../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../../../../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$kernel->boot();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new NotificationServer($kernel)
        )
    ),
    8080
);

$server->run();