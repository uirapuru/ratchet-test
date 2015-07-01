<?php
namespace RatchetTest\Bundle\CoreBundle\DataFixtures\ORM;
use Dende\CommonBundle\DataFixtures\BaseFixture;
use RatchetTest\Bundle\CoreBundle\Entity\Document;

class DocumentsData extends BaseFixture
{
    /**
     * @param string[]
     * @return Document
     */
    public function insert($params)
    {
        $document = new Document();
        $document->setTitle($params['title']);
        $document->setBody($params['body']);
        $document->setAuthor($this->getReference($params['author']));

        return $document;
    }

    public function getOrder()
    {
        return 2;
    }


}