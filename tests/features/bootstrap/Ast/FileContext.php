<?php

declare(strict_types=1);

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use phpDocumentor\Behat\Contexts\Ast\BaseContext;
use Webmozart\Assert\Assert;

final class FileContext extends BaseContext implements Context
{
    /**
     * @Then /^the ast contains the files:$/
     */
    public function theAstContainsTheFiles(TableNode $table)
    {
        $expectedFiles = $table->getColumn(0);
        $actualFiles = array_keys($this->getAst()->getFiles()->getAll());

        try {
            Assert::eq($expectedFiles, $actualFiles);
        } catch (\InvalidArgumentException $e) {
            $diffMissing = array_diff($expectedFiles, $actualFiles);
            $diffUnexpected = array_diff($actualFiles, $expectedFiles);
            throw new \Exception(
                'Missing files: ' . PHP_EOL . (implode(PHP_EOL, $diffMissing) ?: 'none' . PHP_EOL) .
                    'Unexpected files: ' . PHP_EOL . (implode(PHP_EOL, $diffMissing) ?: 'none' . PHP_EOL),
                0,
                $e
            );
        }

    }
}
