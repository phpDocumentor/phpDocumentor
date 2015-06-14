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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Uri is not readable
     */
    public function testItOnlyAcceptsAReadableUri()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        chmod($uri, 000);
        new ConfigurationFactory($uri);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Uri is not a file
     */
    public function testItOnlyAcceptsAUriThatIsAFile()
    {
        $uri = new Uri(sys_get_temp_dir());
        new ConfigurationFactory($uri);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Uri has empty content
     */
    public function testItOnlyAcceptsAUriWithContent()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        new ConfigurationFactory($uri);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Uri does not contain well-formed xml
     */
    public function testItOnlyAcceptsValidXml()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        file_put_contents($uri, 'foo');
        new ConfigurationFactory($uri);
    }

    /**
     * @expectedException \InvalidArgumentException
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

    public function testItConvertsPhpdoc2XmlToAnArray()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, $this->phpDocumentor2XML());

        $uri = new Uri($path);
        $array = new ConfigurationFactory($uri);

        $this->assertSame($this->expectedArray(), $array);
    }

    private function expectedArray()
    {
        return [
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
    }

    private function phpDocumentor2XML()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
    <parser>
        <default-package-name>Default</default-package-name>
        <encoding>utf-8</encoding>
        <target>output/build</target>
        <markers>
            <item>TODO</item>
            <item>FIXME</item>
        </markers>
        <extensions>
            <extension>php</extension>
            <extension>php3</extension>
            <extension>phtml</extension>
        </extensions>
        <visibility></visibility>
        <files>
            <ignore-hidden>true</ignore-hidden>
            <ignore-symlinks>true</ignore-symlinks>
        </files>
    </parser>
    <transformer>
        <target>output</target>
    </transformer>
    <logging>
        <level>error</level>
    </logging>
    <transformations>
        <template name="clean"/>
    </transformations>
    <translator>
        <locale>en</locale>
    </translator>
</phpdocumentor>
XML;
    }

    private function phpDocumentor3XML()
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
