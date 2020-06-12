<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @link      https://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use Webmozart\Assert\Assert;

/**
 * This class contains the context methods for tests of the uses tag.
 */
final class UsesTagContext extends BaseContext implements Context
{
    /**
     * @param string $classFqsen
     * @throws Exception
     * @Then class ":classFqsen" has a tag uses referencing url ":reference"
     */
    public function classHasTagUsesReferencingUrl($classFqsen, $reference)
    {
        $class = $this->findClassByFqsen($classFqsen);
        $usesTags = $class->getTags()->fetch('uses', []);
        $this->hasUsesTagReference($usesTags, $reference);
    }

    /**
     * @param string $classFqsen
     * @Then class ":classFqsen" has :number tag/tags uses referencing :element descriptor ":reference"
     */
    public function classHasTagUsesReferencing($classFqsen, $number, $element, $reference)
    {
        $this->classHasTagUsesReferencingWithDescription($classFqsen, $number, $element, $reference, new PyStringNode([], 0));
    }

    /**
     * @param string $classFqsen
     * @throws Exception
     * @Then class ":classFqsen" has :number tag/tags uses referencing :element descriptor ":reference" with description:
     */
    public function classHasTagUsesReferencingWithDescription($classFqsen, $number, $element, $reference, PyStringNode $description)
    {
        $count = 0;
        $class = $this->findClassByFqsen($classFqsen);
        $usesTags = $class->getTags()->fetch('uses', []);
        /** @var UsesTag $tag */
        foreach ($usesTags as $tag) {
            $r = (string) $tag->getReference();
            if ($r === $reference
                && ((string) $tag->getDescription()) === $description->getRaw()
            ) {
                ++$count;
            }
        }

        Assert::eq($number, $count, sprintf('Missing uses tag with reference "%s"', $reference));
    }


    /**
     * @then function ":function" has tag uses referencing ":reference"
     * @throws Exception
     */
    public function functionHasUsesTagReferencing(string $function, string $reference)
    {
        $functionDescriptor = $this->findFunctionByFqsen($function);
        $this->hasUsesTagReference($functionDescriptor->getTags()->fetch('uses', []), $reference);
    }

    /**
     * @param $reference
     * @param UsesDescriptor[] $usesTags
     * @throws Exception
     */
    private function hasUsesTagReference($usesTags, string $reference) : void
    {
        foreach ($usesTags as $tag) {
            if (((string) $tag->getReference()) === $reference) {
                return;
            }
        }

        throw new Exception(sprintf('Missing uses tag with reference "%s"', $reference));
    }
}
