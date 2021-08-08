<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

/**
 * Renders a code block, example:
 *
 * .. code:: php
 *
 *      <?php
 *
 *      echo "Hello world!\n";
 *
 * @see CodeBlock
 */
class Code extends CodeBlock
{
    public function getName(): string
    {
        return 'code';
    }
}
