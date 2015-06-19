<?php

namespace phpDocumentor\Configuration;

use phpDocumentor\Uri;

/**
 * Test case for ConfigurationFactory
 *
 * @coversDefaultClass phpDocumentor\Configuration\ConfigurationFactory
 */
final class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAReadableUri()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        chmod($uri, 000);
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriThatIsAFile()
    {
        $uri = new Uri(sys_get_temp_dir());
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriWithContent()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsValidXml()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        file_put_contents($uri, 'foo');
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage Root element name should be phpdocumentor, foo found
     */
    public function testItOnlyAcceptsAllowedXmlStructure()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        $xml  = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<foo>
</foo>
XML;

        file_put_contents($path, $xml);

        $uri = new Uri($path);
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc2XmlToAnArray()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, $this->getPhpDocumentor2XML());

        $uri   = new Uri($path);
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $expectedArray = [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'file://build/docs',
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'symlinks' => true,
                                'paths'    => [
                                    0 => 'src/ServiceDefinitions.php'
                                ]
                            ],
                            'extensions'           => [
                                0 => 'php',
                                1 => 'php3',
                                2 => 'phtml'
                            ],
                            'visibility'           => 'public',
                            'default-package-name' => 'Default',
                            'markers'              => [
                                0 => 'TODO',
                                1 => 'FIXME'
                            ]
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://../phpDocumentor/phpDocumentor2',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ]
                ],
                'templates' => [
                    0 => [
                        'name' => 'clean'
                    ],
                    1 => [
                        'location' => 'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItSetsDefaultValuesIfNoneAreFoundInThePhpdoc2Xml()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, '<phpdocumentor></phpdocumentor>');

        $uri   = new Uri($path);
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $expectedArray = [
            'phpdocumentor' => [
                'paths'     => [
                    'output' => 'file://build/docs',
                    'cache'  => '/tmp/phpdoc-doc-cache'
                ],
                'versions'  => [
                    '1.0.0' => [
                        'folder' => 'latest',
                        'api'    => [
                            'format'               => 'php',
                            'source'               => [
                                'dsn'   => 'file://.',
                                'paths' => [
                                    0 => 'src'
                                ]
                            ],
                            'ignore'               => [
                                'hidden'   => true,
                                'symlinks' => true,
                                'paths'    => [
                                    0 => 'src/ServiceDefinitions.php'
                                ]
                            ],
                            'extensions'           => [],
                            'visibility'           => 'public',
                            'default-package-name' => 'Default',
                            'markers'              => []
                        ],
                        'guide'  => [
                            'format' => 'rst',
                            'source' => [
                                'dsn'   => 'file://../phpDocumentor/phpDocumentor2',
                                'paths' => [
                                    0 => 'docs'
                                ]
                            ]
                        ]
                    ]
                ],
                'templates' => [
                    0 => [
                        'name' => 'clean'
                    ],
                    1 => [
                        'location' => 'https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * Gets the phpDocumentor2 configuration template xml
     *
     * @return string Contents of phpdoc.tpl.xml
     */
    private function getPhpDocumentor2XML()
    {
        $path = realpath(__DIR__ . '/../../../../tests/data/phpdoc.tpl.xml');

        return file_get_contents($path);
    }

    private function getPhpDocumentor3XML()
    {
        return <<<XML
<?xml version="1.0" encoding="utf-8"?>

<phpdocumentor
    version="3"
    xmlns="http://www.phpdoc.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.phpdoc.org phpdoc.xsd">
    <paths>
        <output>file://build/docs</output>
        <cache>/tmp/phpdoc-doc-cache</cache>
    </paths>
    <version number="1.0.0">
        <output>latest</output>
        <api format="php">
            <source dsn="file://.">
                <path>src</path>
            </source>
            <ignore hidden="true" symlinks="true">
                <path>src/ServiceDefinitions.php</path>
            </ignore>
     <extensions>
         <extension>php</extension>
     </extensions>
     <visibility>public</visibility>
     <default-package-name>Default</default-package-name>
     <markers>
         <marker>TODO</marker>
         <marker>FIXME</marker>
     </markers>
        </api>
        <guide format="rst">
            <source dsn="file://../phpDocumentor/phpDocumentor2">
                <path>docs</path>
            </source>
        </guide>
    </version>
    <template name="clean"/>
    <template location="https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean"/>
</phpdocumentor>
XML;
    }
}
