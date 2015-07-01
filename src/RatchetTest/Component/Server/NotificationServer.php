<?php
namespace RatchetTest\Component\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use RatchetTest\Bundle\CoreBundle\Entity\Document;

class NotificationServer implements MessageComponentInterface {
    protected $clients;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function __construct(\AppKernel $kernel) {
        $this->clients = new \SplObjectStorage;
        $this->kernel = $kernel;
        $this->container = $kernel->getContainer();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $documents = $this->container->get("doctrine.orm.entity_manager")
            ->getRepository("RatchetTestCoreBundle:Document")->findAll();

        /* @var \JMS\Serializer\Serializer */
        $serializer = $this->container->get("jms_serializer");

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($serializer->serialize([
                    "message" => "cześć"
                ], "json"));
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