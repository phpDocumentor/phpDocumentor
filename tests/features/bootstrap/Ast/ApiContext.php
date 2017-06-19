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
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Behat\Contexts\EnvironmentContext;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\Assert;

class ApiContext implements Context
{
    /** @var EnvironmentContext */
    private $minkContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext(EnvironmentContext::class);
    }

    /**
     * @Then /^the AST has a class named "([^"]*)" in file "([^"]*)"$/
     */
    public function theASTHasAclassNamedInFile($class, $file)
    {
        $ast = $this->getAst();

        /** @var FileDescriptor $fileDescriptor */
        $fileDescriptor = $ast->getFiles()->get($file);

        /** @var ClassDescriptor $classDescriptor */
        foreach ($fileDescriptor->getClasses() as $classDescriptor) {
            if ($classDescriptor->getName() == $class) {
                return;
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected class "%s" in "%s"', $class, $file));
    }

    /**
     * @Then /^the class named "([^"]*)" is in the default package$/
     */
    public function theASTHasAClassInDefaultPackage($class)
    {
        $class = $this->findClass($class);

        Assert::assertEquals('Default', $class->getPackage()->getName());
    }

    /**
     * @param $class
     * @param $expectedContent
     * @Then the class named ":class" has docblock with content:
     */
    public function classHasDocblockWithContent($class, PyStringNode $expectedContent)
    {
        $class = $this->findClass($class);

        Assert::assertEquals($expectedContent->getRaw(), $class->getDescription());
    }

    /**
     * @param string $className
     * @return ClassDescriptor
     * @throws \Exception
     */
    private function findClass($className) {
        $ast = $this->getAst();
        foreach ($ast->getFiles() as $file) {
            foreach ($file->getClasses() as $classDescriptor) {
                if ($classDescriptor->getName() === $className) {
                    return $classDescriptor;
                }
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected class "%s"', $className));
    }

    /**
     * @return ProjectDescriptor|null
     */
    protected function getAst()
    {
        $file = $this->minkContext->getWorkingDir() . '/ast.dump';
        if (!file_exists($file)) {
            throw new \Exception(
                'The output of phpDocumentor was not generated, this probably means that the execution failed. '
                . 'The error output was: ' . $this->minkContext->getErrorOutput()
            );
        }

        return unserialize(file_get_contents($file));
    }
}
