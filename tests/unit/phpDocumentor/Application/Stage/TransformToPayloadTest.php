<?php

declare(strict_types=1);

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Application\Stage\TransformToPayload
 * @covers ::__construct()
 */
final class TransformToPayloadTest extends TestCase
{
    /**
     * @covers ::__invoke()
     */
    public function test_it_converts_the_configuration_to_an_payload() : void
    {
        $config  = ['config' => 'yes'];
        $builder = $this->prophesize(ProjectDescriptorBuilder::class)->reveal();

        $payload = (new TransformToPayload($builder))($config);

        $this->assertSame($builder, $payload->getBuilder());
        $this->assertSame($config, $payload->getConfig());
    }
}
