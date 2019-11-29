<?php

declare(strict_types=1);

namespace phpDocumentor\Application\Stage;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class TwigTest extends MockeryTestCase
{
    public function test_that_the_cache_folder_gets_configured() : void
    {
        $twig   = new Environment(new ArrayLoader());
        $config = [ 'phpdocumentor' => [ 'paths' => [ 'cache' => 'phpdoc-cache' ] ] ];

        (new Twig($twig))(new Payload($config, m::mock(ProjectDescriptorBuilder::class)));

        $this->assertSame('phpdoc-cache/twig', $twig->getCache());
    }
}
