<?php

namespace RatchetTest\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ZMQ;
use ZMQContext;

/**
 * Class AdminController
 * @package RatchetTest\Bundle\CoreBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm('ratchettest_document');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $serializer = $this->get('jms_serializer');
//            if ($form->isValid()) {

                $context = new ZMQContext();
                $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
                $socket->connect("tcp://localhost:5555");

                $data = [
                    "message" => "success"
                ];

                $socket->send($this->get("jms_serializer")->serialize($data, "json"));
//            } else {
//
//            }
        }

        return [
            "addDocumentForm" => $form->createView()
        ];
    }
}
