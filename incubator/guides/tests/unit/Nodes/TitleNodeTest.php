<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Guides\MarkupLanguageParser;
use phpDocumentor\Guides\ParserContext;

final class TitleNodeTest extends MockeryTestCase
{
    public function test_it_can_be_created_with_a_title_slug_and_depth(): void
    {
        $environment = m::mock(ParserContext::class);
        $environment->shouldReceive('getTitleLetters')->andReturn(['a']);
        $environment->shouldReceive('resetAnonymousStack');

        $parser = m::mock(MarkupLanguageParser::class);
        $parser->shouldReceive('getEnvironment')->andReturn($environment);

        $titleNode = new SpanNode('Raw String');
        $node = new TitleNode($titleNode, 1);
        $node->setTarget('target');

        self::assertSame('raw-string', $node->getId());
        self::assertSame($titleNode, $node->getValue());
        self::assertSame(1, $node->getLevel());
        self::assertSame('target', $node->getTarget());
    }
}
