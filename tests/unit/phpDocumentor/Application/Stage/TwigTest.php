<?php

declare(strict_types=1);

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use \Mockery as m;

final class TwigTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function test_that_the_cache_folder_gets_configured()
    {
        $twig = new \Twig\Environment();
        $config = [ 'phpdocumentor' => [ 'paths' => [ 'cache' => 'phpdoc-cache' ] ] ];

        (new Twig($twig))(new Payload($config, m::mock(ProjectDescriptorBuilder::class)));

        $this->assertSame('phpdoc-cache/twig', $twig->getCache());
    }
}
