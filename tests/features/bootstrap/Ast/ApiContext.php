<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use phpDocumentor\Behat\Contexts\EnvironmentContext;
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use PHPUnit\Framework\Assert;

class ApiContext extends BaseContext implements Context
{
    /**
     * @Then /^the AST has a class named "([^"]*)" in file "([^"]*)"$/
     * @throws \Exception
     */
    public function theASTHasAclassNamedInFile($class, $file)
    {
        $ast = $this->getAst();

        $file = $this->processFilePath($file);
        /** @var FileDescriptor $fileDescriptor */
        $fileDescriptor = $ast->getFiles()->get($file);

        /** @var ClassDescriptor $classDescriptor */
        foreach ($fileDescriptor->getClasses() as $classDescriptor) {
            if ($classDescriptor->getName() === $class) {
                return;
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected class "%s" in "%s"', $class, $file));
    }

    /**
     * @Then /^the class named "([^"]*)" is in the default package$/
     * @throws \Exception
     */
    public function theASTHasAClassInDefaultPackage($class)
    {
        $class = $this->findClassByName($class);

        Assert::assertEquals('Default', $class->getPackage()->getName());
    }

    /**
     * @param $class
     * @param $expectedContent
     * @Then the class named ":class" has docblock with content:
     */
    public function classHasDocblockWithContent($class, PyStringNode $expectedContent)
    {
        $class = $this->findClassByName($class);

        Assert::assertEquals($expectedContent->getRaw(), $class->getDescription());
    }

    /**
     * @param $classFqsen
     * @param $docElement
     * @param $value
     * @Then class ":classFqsen" has :docElement:
     * @throws \Exception
     */
    public function classHasDocblockContent($classFqsen, $docElement, PyStringNode $value)
    {
        $class = $this->findClassByFqsen($classFqsen);

        $method = 'get' . $docElement;

        Assert::assertEquals($value->getRaw(), $class->$method());
    }

    /**
     * @param $classFqsen
     * @param $elementType
     * @param $elementName
     * @param $docElement
     * @param PyStringNode $value
     * @Then class ":classFqsen" has :elementType :elementName with :docElement:
     */
    public function classHasElementWithDocblockContent($classFqsen, $elementType, $elementName, $docElement, PyStringNode $value)
    {
        $class = $this->findClassByFqsen($classFqsen);

        switch ($elementType) {
            case 'method':
            case 'constant':
                $method = $method = 'get' . $elementType . 's';
                break;
            case 'property':
                $method = 'getProperties';
                break;
            default:
                $method = 'get' . $elementType;
                break;
        }

        $element = $class-> $method()->get($elementName);

        $method = 'get' . $docElement;
        $actual = $element->$method();

        Assert::assertEquals($value->getRaw(), $actual,  sprintf('"%s" does not match "%s"', $actual, $value->getRaw()));
    }

    /**
     * @param $classFqsen
     * @param $value
     * @Then class ":classFqsen" has version :value
     */
    public function classHasVersion($classFqsen, $value)
    {
        $class = $this->findClassByFqsen($classFqsen);

        /** @var VersionDescriptor $tag */
        foreach ($class->getVersion() as $tag) {
            if ($tag->getVersion() === $value) {
                return;
            }
        }

        Assert::fail(sprintf('Didn\'t find expected version "%s"', $value));
    }

    /**
     * @param $classFqsen
     * @param $tagName
     * @Then class ":classFqsen" without tag :tagName
     */
    public function classWithoutTag($classFqsen, $tagName)
    {
        $this->classHasTag($classFqsen, $tagName, 0);
    }

    /**
     * @param string $classFqsen
     * @param string $tagName
     * @param int $expectedNumber
     * @Then class ":classFqsen" has exactly :expectedNumber tag :tagName
     */
    public function classHasTag($classFqsen, $tagName, $expectedNumber)
    {
        $class = $this->findClassByFqsen($classFqsen);
        static::AssertTagCount($class, $tagName, $expectedNumber);
    }

    /**
     * @param string $classFqsen
     * @param string $tagName
     * @param string $method
     * @Then class ":classFqsen" has a method named :method without tag :tagName
     */
    public function classHasMethodWithoutTag($classFqsen, $tagName, $method)
    {
        $this->classHasMethodWithExpectedCountTag($classFqsen, $tagName, $method, 0);
    }

    /**
     * @param string $classFqsen
     * @param string $tagName
     * @param string $methodName
     * @Then class ":classFqsen" has a method named :method with exactly :expected tag :tagName
     */
    public function classHasMethodWithExpectedCountTag($classFqsen, $tagName, $methodName, $expectedCount)
    {
        $class = $this->findClassByFqsen($classFqsen);
        $method = $class->getMethods()->get($methodName);

        static::AssertTagCount($method, $tagName, $expectedCount);
    }

    /**
     * @param string $classFqsen
     * @param string $tagName
     * @param string $methodName
     * @Then class ":classFqsen" has a method :method with argument ":argument is variadic
     */
    public function classHasMethodWithAgumentVariadic($classFqsen, $methodName, $argument)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->get($methodName);
        Assert::assertArrayHasKey($argument, $method->getArguments());
        /** @var ArgumentDescriptor $argumentD */
        $argumentD = $method->getArguments()[$argument];

        //TODO: enable this check when we support variadic arguments.
        //Assert::assertTrue($argumentD->isVariadic(), 'Expected argument to be variadic');
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @param string $argument
     * @param string $type
     * @Then class ":classFqsen" has a method :method with argument :argument of type ":type"
     */
    public function classHasMethodWithAgumentOfType($classFqsen, $methodName, $argument, $type)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->get($methodName);
        Assert::assertArrayHasKey($argument, $method->getArguments());
        /** @var ArgumentDescriptor $argumentDescriptor */
        $argumentDescriptor = $method->getArguments()[$argument];

        Assert::assertEquals($type, (string)$argumentDescriptor->getTypes());
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @param string $param
     * @param string $type
     * @Then class ":classFqsen" has a method :method with param :param of type ":type"
     */
    public function classHasMethodWithParamOfType($classFqsen, $methodName, $param, $type)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->get($methodName);
        /** @var ParamDescriptor $paramDescriptor */
        foreach ($method->getParam() as $paramDescriptor) {
            if($paramDescriptor->getName() === $param) {
                Assert::assertEquals($type, (string)$paramDescriptor->getTypes());
            }
        }
    }

    /**
     * @param string $classFqsen
     * @param string $constantName
     * @Then class ":classFqsen" has a constant :constantName
     */
    public function classHasConstant($classFqsen, $constantName)
    {
        /** @var ClassDescriptor $class */
        $class = $this->findClassByFqsen($classFqsen);
        $constant = $class->getConstants()->get($constantName);
        Assert::assertInstanceOf(ConstantDescriptor::class, $constant);
    }

    /**
     * @param string $className
     * @return ClassDescriptor
     * @throws \Exception
     */
    private function findClassByName($className)
    {
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
     * @param string $tagName
     * @param int $expectedNumber
     * @param DescriptorAbstract $element
     */
    private static function AssertTagCount($element, $tagName, $expectedNumber)
    {
        /** @var Collection $tagCollection */
        $tagCollection = $element->getTags()->get($tagName, new Collection());

        Assert::assertEquals((int)$expectedNumber, $tagCollection->count());
        if ($expectedNumber > 0) {
            Assert::assertEquals($tagName, $tagCollection[0]->getName());
        }
    }
}
