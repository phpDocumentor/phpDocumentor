<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use PHPUnit\Framework\Assert;

/**
 * This class contains the context methods for tests of the uses tag.
 */
final class UsesTagContext extends BaseContext implements Context
{
    /**
     * @param string $classFqsen
     * @param $reference
     * @throws \Exception
     * @Then class ":classFqsen" has a tag uses referencing url ":reference"
     */
    public function classHasTagUsesReferencingUrl($classFqsen, $reference)
    {
        $class = $this->findClassByFqsen($classFqsen);
        $usesTags = $class->getTags()->get('uses', new Collection());
        /** @var UsesTag $tag */
        foreach ($usesTags as $tag) {
            if ($tag->getReference() === $reference) {
                return;
            }
        }

        throw new \Exception(sprintf('Missing uses tag with reference "%s"', $reference));
    }

    /**
     * @param string $classFqsen
     * @param $element
     * @param $reference
     * @Then class ":classFqsen" has :number tag/tags uses referencing :element descriptor ":reference"
     */
    public function classHasTagUsesReferencing($classFqsen, $number, $element, $reference)
    {
        $this->classHasTagUsesReferencingWithDescription($classFqsen, $number, $element, $reference, new PyStringNode([],0));
    }

    /**
     * @param string $classFqsen
     * @param $element
     * @param $reference
     * @param $description
     * @throws \Exception
     * @Then class ":classFqsen" has :number tag/tags uses referencing :element descriptor ":reference" with description:
     */
    public function classHasTagUsesReferencingWithDescription($classFqsen, $number, $element, $reference, PyStringNode $description)
    {
        $count = 0;
        $class = $this->findClassByFqsen($classFqsen);
        $usesTags = $class->getTags()->get('uses', new Collection());
        $element = '\\phpDocumentor\\Descriptor\\' .ucfirst($element) . 'Descriptor';
        /** @var UsesTag $tag */
        foreach ($usesTags as $tag) {
            $r = $tag->getReference();
            if ($r instanceof $element
                && $r->getFullyQualifiedStructuralElementName() === $reference
                && $tag->getDescription() === $description->getRaw()
            ) {
                $count++;
            }
        }

        Assert::assertEquals($number, $count, sprintf('Missing uses tag with reference "%s"', $reference));
    }
}
