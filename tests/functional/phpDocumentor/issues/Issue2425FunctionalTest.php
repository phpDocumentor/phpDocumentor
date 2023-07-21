<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace functional\phpDocumentor\issues;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\FunctionalTestCase;

final class Issue2425FunctionalTest extends FunctionalTestCase
{
    public function testInlineAliasedSeeIsResolved() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/issues/issue-2425/issue-2425.php');
        $project = $this->loadAst();

        $versions = $project->getVersions();
        $this->assertCount(1, $versions);

        $apiSets = $versions->first()->getDocumentationSets()->filter(ApiSetDescriptor::class);
        $this->assertCount(1, $apiSets);

        /** @var ApiSetDescriptor $apiSet */
        $apiSet = $apiSets->first();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $apiSet->getIndexes()->get('classes')->get('\\' . \Project\Sub\Level\Issue2425A::class);

        self::assertSame(
            <<<DESCRIPTION
A description containing an inline \Project\Other\Level\Issue2425B::bar() tag
to a class inside of the project referenced via a use statement.

And here is another inline \Project\Other\Level\Issue2425C::bar() tag to a class
aliased via a use statement.
DESCRIPTION,
            $classDescriptor->getDescription());
    }
}
