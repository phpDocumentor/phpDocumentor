<?php declare(strict_types=1);

namespace phpDocumentor\Configuration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Util\XmlUtils;

final class SymfonyConfigFactory
{
    public function create() : array
    {
        $values = XmlUtils::loadFile(__DIR__ . '/../../../phpdoc.xml', null);
        $values = XmlUtils::convertDomElementToArray($values->documentElement);

        $configurationVersion = $values['version'] ?? '2';

        $processor = new Processor();
        $definition = new Definition\Version3();

        return $processor->processConfiguration(
            $definition,
            [
                $values
            ]
        );
    }
}
