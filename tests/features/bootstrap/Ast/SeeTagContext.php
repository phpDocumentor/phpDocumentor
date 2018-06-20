<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Webmozart\Assert\Assert;

/**
 * This class contains the context methods for tests of the see tag.
 */
final class SeeTagContext extends BaseContext implements Context
{
    /**
     * @param string $classFqsen
     * @throws \Exception
     * @Then class ":classFqsen" has a tag see referencing url ":reference"
     */
    public function classHasTagSeeReferencingUrl($classFqsen, $reference)
    {
        $class = $this->findClassByFqsen($classFqsen);
        $seeTags = $class->getTags()->get('see', []);
        /** @var SeeTag $tag */
        foreach ($seeTags as $tag) {
            if (((string) $tag->getReference()) === $reference) {
                return;
            }
        }

        throw new \Exception(sprintf('Missing see tag with reference "%s"', $reference));
    }

    /**
     * @param string $classFqsen
     * @Then class ":classFqsen" has :number tag/tags see referencing :element descriptor ":reference"
     */
    public function classHasTagSeeReferencing($classFqsen, $number, $element, $reference)
    {
        $this->classHasTagSeeReferencingWithDescription($classFqsen, $number, $element, $reference, new PyStringNode([], 0));
    }

    /**
     * @param string $classFqsen
     * @throws \Exception
     * @Then class ":classFqsen" has :number tag/tags see referencing :element descriptor ":reference" with description:
     */
    public function classHasTagSeeReferencingWithDescription($classFqsen, $number, $element, $reference, PyStringNode $description)
    {
        $count = 0;
        $class = $this->findClassByFqsen($classFqsen);
        $seeTags = $class->getTags()->get('see', []);
        /** @var SeeTag $tag */
        foreach ($seeTags as $tag) {
            $r = (string) $tag->getReference();
            if ($r === $reference
                && ((string) $tag->getDescription()) === $description->getRaw()
            ) {
                ++$count;
            }
        }

        Assert::eq($number, $count, sprintf('Missing see tag with reference "%s"', $reference));
    }
}
