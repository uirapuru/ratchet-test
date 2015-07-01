<?php
namespace RatchetTest\Component\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use RatchetTest\Bundle\CoreBundle\Entity\Document;

class NotificationServer implements MessageComponentInterface {
    protected $clients;

    public function __construct(\AppKernel $kernel) {
        $this->clients = new \SplObjectStorage;
        $this->kernel = $kernel;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $documents = $this->kernel->getContainer()->get("doctrine.orm.entity_manager")
            ->getRepository("RatchetTestCoreBundle:Document")->findAll();

        $count = count($documents);

        $titles = array_map(function(Document $document){
            return $document->getTitle();
        }, $documents);

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send(
                    "There was a message: ".$msg .
                    "\n env: ".$this->kernel->getEnvironment() .
                    "\n root dir:" . $this->kernel->getRootDir() .
                    "\n documents count:" . $count .
                    "\n titles: " . implode(", ", $titles)
                );
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}