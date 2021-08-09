<?php

declare(strict_types=1);

namespace Descriptor\Builder;

use DateTime;
use phpDocumentor\Descriptor\Builder\Matcher;
use PHPUnit\Framework\TestCase;
use stdClass;

final class MatcherTest extends TestCase
{
    public function test_it_can_match_against_the_given_class_as_a_callable(): void
    {
        $matcher = Matcher::forType(stdClass::class);

        $this->assertTrue($matcher(new stdClass()));
        $this->assertFalse($matcher(new DateTime()));
        $this->assertFalse($matcher('a'));
    }
}
