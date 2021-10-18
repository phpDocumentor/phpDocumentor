<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\Nodes\Links\InvalidLink;
use PHPUnit\Framework\TestCase;

final class InvalidLinkTest extends TestCase
{
    public function test_it_has_a_name(): void
    {
        $invalidLink = new InvalidLink('name');

        $this->assertSame('name', $invalidLink->getName());
    }
}
