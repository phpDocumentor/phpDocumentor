<?php

declare(strict_types=1);

namespace phpDocumentor\Plugin\Twig\Stage;

final class ConfigureTwigTest extends \PHPUnit\Framework\TestCase
{
    public function test_that_the_cache_folder_gets_configured()
    {
        $twig = new \Twig\Environment();
        $config = [ 'phpdocumentor' => [ 'paths' => [ 'cache' => 'phpdoc-cache' ] ] ];

        (new ConfigureTwig($twig))($config);

        $this->assertSame('phpdoc-cache/twig', $twig->getCache());
    }
}
