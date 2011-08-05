<?php

class DocBlox_Task_Project_ParseTest extends PHPUnit_Framework_TestCase
{
    const TITLE      = 'test title';
    const VISIBILITY = 'public';

    public function setup()
    {
        $xml             = new SimpleXMLElement('<docblox />');
        $xml->title      = self::TITLE;
        $xml->visibility = self::VISIBILITY;

        DocBlox_Core_Abstract::setConfig(
            new DocBlox_Core_Config($xml->saveXML())
        );
        $this->fixture = new DocBlox_Task_Project_Parse();
    }

    public function testGetTitle()
    {
        $this->assertEquals(self::TITLE, $this->fixture->getTitle());
    }

    public function testGetVisibility()
    {
        $this->assertEquals(self::VISIBILITY, $this->fixture->getVisibility());
    }

    public function tearDown()
    {
        DocBlox_Core_Abstract::resetConfig();
    }
}
