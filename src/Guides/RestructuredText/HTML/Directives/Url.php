<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser;
use function trim;

/**
 * Sets the document URL
 */
class Url extends Directive
{
    public function getName() : string
    {
        return 'url';
    }

    /**
     * @param string[] $options
     */
    public function processAction(
        Parser $parser,
        string $variable,
        string $data,
        array $options
    ) : void {
        $environment = $parser->getEnvironment();

        $environment->setUrl(trim($data));
    }
}
