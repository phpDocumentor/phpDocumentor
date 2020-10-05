<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Twig;

use PHPUnit\Framework\TestCase;

final class AssetsExtensionTest extends TestCase
{
    public function test_it_has_a_function_to_convert_an_assets_path_to_the_actual_asset_location(): void
    {
        $extension = new AssetsExtension();

        $this->assertCount(1, $extension->getFunctions());

        $function = $extension->getFunctions()[0];
        $this->assertSame('asset', $function->getName());
        $this->assertSame('/assets/path', $function->getCallable()('path'));
    }
}
