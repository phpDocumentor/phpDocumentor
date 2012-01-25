<?php

class phpDocumentor_Task_Project_ParseTest extends PHPUnit_Framework_TestCase
{
    const TITLE      = 'test title';
    const VISIBILITY = 'public';

    /** @var phpDocumentor_Task_Project_Parse */
    protected $fixture = null;

    public function setUp()
    {
        $xml             = new SimpleXMLElement('<phpdoc />');
        $xml->title      = self::TITLE;
        $xml->visibility = self::VISIBILITY;

        phpDocumentor_Core_Abstract::setConfig(
            new phpDocumentor_Core_Config($xml->saveXML())
        );
        $this->fixture = new phpDocumentor_Task_Project_Parse();
    }

    public function testGetTitle()
    {
        $this->assertEquals(self::TITLE, $this->fixture->getTitle());
    }

    public function testGetVisibility()
    {
        $this->fixture->setVisibility(self::VISIBILITY);
        $this->assertEquals(self::VISIBILITY, $this->fixture->getVisibility());
    }

    public function tearDown()
    {
        phpDocumentor_Core_Abstract::resetConfig();
    }
}
