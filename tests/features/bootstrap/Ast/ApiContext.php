<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Php\File;
use Webmozart\Assert\Assert;

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
     * @Then /^the AST doesn't have a class "([^"]*)"$/
     * @throws \Exception
     */
    public function theASTDoesnTHaveAClass($className)
    {
        $ast = $this->getAst();
        foreach ($ast->getFiles() as $file) {
            foreach ($file->getClasses() as $classDescriptor) {
                if ($classDescriptor->getName() === $className) {
                    throw new \Exception('Found unexpected class');
                }
            }
        }
    }

    /**
     * @Then /^the class named "([^"]*)" is in the default package$/
     * @throws \Exception
     */
    public function theASTHasAClassInDefaultPackage($class)
    {
        $class = $this->findClassByName($class);

        Assert::eq('Default', $class->getPackage()->getName());
    }

    /**
     * @Then /^the AST has a trait named "([^"]*)" in file "([^"]*)"$/
     * @throws \Exception
     */
    public function theASTHasATraitNamedInFile($trait, $file)
    {
        $ast = $this->getAst();

        $file = $this->processFilePath($file);
        /** @var FileDescriptor $fileDescriptor */
        $fileDescriptor = $ast->getFiles()->get($file);

        /** @var TraitDescriptor $classDescriptor */
        foreach ($fileDescriptor->getTraits() as $classDescriptor) {
            if ($classDescriptor->getName() === $trait) {
                return;
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected trait "%s" in "%s"', $trait, $file));
    }

    /**
     * @Then the class named ":class" has docblock with content:
     */
    public function classHasDocblockWithContent($class, PyStringNode $expectedContent)
    {
        $class = $this->findClassByName($class);

        Assert::eq($expectedContent->getRaw(), $class->getDescription());
    }

    /**
     * @Then class ":classFqsen" has :docElement:
     * @throws Exception
     */
    public function classHasDocblockContent($classFqsen, $docElement, PyStringNode $value)
    {
        $class = $this->findClassByFqsen($classFqsen);

        $method = 'get' . $docElement;

        Assert::eq($value->getRaw(), $class->{$method}());
    }

    /**
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

        $element = $class-> {$method}()->get($elementName);
        $method = 'get' . $docElement;
        $actual = $element->{$method}();

        Assert::eq($value->getRaw(), $actual, sprintf('"%s" does not match "%s"', $actual, $value->getRaw()));
    }

    /**
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

        Assert::false(true, sprintf('Didn\'t find expected version "%s"', $value));
    }

    /**
     * @Then class ":classFqsen" without tag :tagName
     */
    public function classWithoutTag($classFqsen, $tagName)
    {
        $this->classHasTag($classFqsen, $tagName, 0);
    }

    /**
     * @Then class ":classFqsen" has exactly :expectedNumber tag :tagName
     */
    public function classHasTag(string $classFqsen, string $tagName, string $expectedNumber)
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
     * @param string $methodName
     * @Then class ":classFqsen" has a method :method with argument ":argument is variadic
     */
    public function classHasMethodWithArgumentVariadic($classFqsen, $methodName, $argument)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->get($methodName);
        Assert::keyExists($method->getArguments()->getAll(), $argument);

        /** @var ArgumentDescriptor $argumentD */
        $argumentD = $method->getArguments()[$argument];
        Assert::true($argumentD->isVariadic(), 'Expected argument to be variadic');
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @Then class ":classFqsen" has a method :method
     */
    public function classHasMethod($classFqsen, $methodName)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->fetch($methodName, null);
        $methodNames = implode(', ', array_keys($class->getMethods()->getAll()));

        $visibilityLevel = $this->getAst()->getSettings()->getVisibility();
        Assert::isInstanceOf(
            $method,
            MethodDescriptor::class,
            "Class $classFqsen does not have a method $methodName, it does have the methods: $methodNames "
            . "(visibility level: $visibilityLevel})"
        );
        Assert::eq($methodName, $method->getName());
    }

    /**
     * @param string $classFqsen
     * @param string $propertyName
     * @Then class ":classFqsen" has a property :property
     */
    public function classHasProperty($classFqsen, $propertyName)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var PropertyDescriptor $property */
        $property = $class->getProperties()->fetch($propertyName, null);
        Assert::isInstanceOf($property, PropertyDescriptor::class);
        Assert::eq($propertyName, $property->getName());
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @param string $argument
     * @param string $type
     * @Then class ":classFqsen" has a method :method with argument :argument of type ":type"
     */
    public function classHasMethodWithArgumentOfType($classFqsen, $methodName, $argument, $type)
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->get($methodName);
        Assert::keyExists($method->getArguments()->getAll(), $argument);
        /** @var ArgumentDescriptor $argumentDescriptor */
        $argumentDescriptor = $method->getArguments()[$argument];

        Assert::eq($type, (string) $argumentDescriptor->getType());
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
            if ($paramDescriptor->getName() === $param) {
                Assert::eq($type, (string) $paramDescriptor->getType());
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
        $class = $this->findClassByFqsen($classFqsen);
        $constant = $class->getConstants()->get($constantName);
        Assert::isInstanceOf($constant, ConstantDescriptor::class);
    }

    /**
     * @param string $className
     * @throws \Exception
     */
    private function findClassByName($className) : ClassDescriptor
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
        $tagCollection = $element->getTags()->fetch($tagName, new Collection());

        Assert::eq((int) $expectedNumber, $tagCollection->count());
        if ($expectedNumber > 0) {
            Assert::eq($tagName, $tagCollection[0]->getName());
        }
    }

    /**
     * @Then /^the ast has a file named "([^"]*)" with a summary:$/
     * @throws \Exception
     */
    public function theAstHasAFileNamedWithASummary(string $fileName, PyStringNode $string)
    {
        $ast = $this->getAst();
        /** @var FileDescriptor $file */
        $file = $ast->getFiles()->get($fileName);

        Assert::eq($string->getRaw(), $file->getSummary());
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @throws Exception
     * @Then class ":classFqsen" has a method :method with returntype :returnType
     * @Then class ":classFqsen" has a method :method with returntype :returnType without description
     */
    public function classHasMethodWithReturnType($classFqsen, $methodName, $returnType)
    {
        $response = $this->findMethodResponse($classFqsen, $methodName);

        Assert::eq((string) $response->getType(), $returnType);
        Assert::eq((string) $response->getDescription(), '');
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @throws Exception
     * @Then class ":classFqsen" has a magic method :method with returntype :returnType
     * @Then class ":classFqsen" has a magic method :method with returntype :returnType without description
     */
    public function classHasMagicMethodWithReturnType($classFqsen, $methodName, $returnType)
    {
        $response = $this->findMagicMethodResponse($classFqsen, $methodName);

        Assert::eq((string) $response->getType(), $returnType);
        Assert::eq((string) $response->getDescription(), '');
    }

    /**
     * @param string $classFqsen
     * @param string $methodName
     * @throws Exception
     * @Then class ":classFqsen" has a method :method with returntype :returnType with description:
     */
    public function classHasMethodWithReturnTypeAndDescription($classFqsen, $methodName, $returnType, PyStringNode $description)
    {
        $response = $this->findMethodResponse($classFqsen, $methodName);

        Assert::eq($returnType, (string) $response->getType());
        Assert::eq($description, (string) $response->getDescription());
    }

    /**
     * @Then class ":classFqsen" has a method ":method" without returntype
     * @throws \Exception
     */
    public function classReturnTaggetReturnWithoutAnyWithoutReturntype($classFqsen, $methodName)
    {
        $response = $this->findMethodResponse($classFqsen, $methodName);
        Assert::eq('mixed', (string) $response->getType());
        Assert::eq('', $response->getDescription());
    }

    /**
     * @throws Exception
     * @Then has function :fqsen with returntype :returnType
     * @Then has function :fqsen with returntype :returnType without description
     */
    public function functionWithReturnType($fqsen, $returnType)
    {
        $response = $this->findFunctionResponse($fqsen);

        Assert::eq($returnType, (string) $response->getType());
        Assert::eq('', (string) $response->getDescription());
    }

    /**
     * @throws Exception
     * @Then has function :fqsen with returntype :returnType with description:
     */
    public function functionWithReturnTypeAndDescription($fqsen, $returnType, PyStringNode $description)
    {
        $response = $this->findFunctionResponse($fqsen);

        Assert::eq($returnType, (string) $response->getType());
        Assert::eq($description, (string) $response->getDescription());
    }

    /**
     * @Then has function :fqsen without returntype
     * @throws \Exception
     */
    public function functionWithoutReturntype($fqsen)
    {
        $response = $this->findFunctionResponse($fqsen);
        Assert::eq('mixed', (string) $response->getType());
        Assert::eq('', $response->getDescription());
    }

    /**
     * @throws Exception
     */
    private function findMethodResponse($classFqsen, $methodName): ReturnDescriptor
    {
        $class = $this->findClassByFqsen($classFqsen);
        /** @var MethodDescriptor $method */
        $method = $class->getMethods()->fetch($methodName, null);
        Assert::isInstanceOf($method, MethodDescriptor::class);
        Assert::eq($methodName, $method->getName());

        return $method->getResponse();
    }

    /**
     * @throws Exception
     */
    private function findMagicMethodResponse($classFqsen, $methodName): ReturnDescriptor
    {
        $class = $this->findClassByFqsen($classFqsen);
        $match = null;

        /** @var MethodDescriptor $method */
        foreach ($class->getMagicMethods() as $method) {
            if ($method->getName() === $methodName) {
                $match = $method;
            }
        }

        Assert::isInstanceOf($match, MethodDescriptor::class);
        Assert::eq($methodName, $match->getName());

        return $match->getResponse();
    }

    /**
     * @throws Exception
     */
    private function findFunctionResponse(string $fqsen): ReturnDescriptor
    {
        $function = $this->findFunctionByFqsen($fqsen);
        return $function->getResponse();
    }

    /**
     * @Then class ":classFqsen" has a magic method :method with argument ":argument" of type :type
     */
    public function classHasMagicMethodWithArgument($classFqsen, $methodName, $argument, $type)
    {
        $class = $this->findClassByFqsen($classFqsen);
        $match = null;

        /** @var MethodDescriptor $method */
        foreach ($class->getMagicMethods() as $method) {
            if ($method->getName() === $methodName) {
                $match = $method;
            }
        }

        Assert::isInstanceOf($match, MethodDescriptor::class);
        Assert::notNull($match->getArguments()->get($argument));
    }

    /**
     * @Then /^(\d+) files should be parsed$/
     */
    public function filesShouldBeParsed($count)
    {
        Assert::same($this->getAst()->getFiles()->count(), (int) $count);
    }

    /**
     * @Then /^the ast has a function named "([^"]*)"$/
     */
    public function theAstHasAFunctionNamed($functionName)
    {
        Assert::isInstanceOf(
            $this->getAst()->getIndexes()->get('functions')->get($functionName . '()'),
            FunctionDescriptor::class
        );
    }

    /**
     * @Then argument :argument of function ":functionName" has no defined type and description is:
     */
    public function argumentOfFunctionHasNoTypeAndHasDescripion($argument, $functionName, PyStringNode $description)
    {
        /** @var FunctionDescriptor $functionDescriptor */
        $functionDescriptor = $this->getAst()->getIndexes()->get('functions')->get($functionName . '()');
        Assert::isInstanceOf(
            $functionDescriptor,
            FunctionDescriptor::class
        );

        /** @var ArgumentDescriptor $argumentDescriptor */
        $argumentDescriptor = $functionDescriptor->getArguments()->get($argument);

        Assert::isInstanceOf($argumentDescriptor, ArgumentDescriptor::class);

        Assert::same($description->getRaw(), (string) $argumentDescriptor->getDescription());
    }

    /**
     * @Given the namespace ':namespace' has a function named ':functionName'
     */
    public function theNamespaceFoo(string $namespace, string $functionName)
    {
        /** @var NamespaceDescriptor $namespace */
        $namespace = $this->getAst()->getIndexes()->get('namespaces')->get($namespace);
        Assert::isInstanceOf($namespace, NamespaceDescriptor::class);
        $function = $this->findFunctionInNamespace($namespace, $functionName);
        Assert::isInstanceOf($function, FunctionDescriptor::class);
    }

    private function findFunctionInNamespace(NamespaceDescriptor $namespace, string $functionName)
    {
        foreach ($namespace->getFunctions()->getAll() as $key => $function) {
            if ($function->getName() === $functionName) {
                return $function;
            }
        }

        return null;
    }

    /**
     * @Then /^file "([^"]*)" must contain a marker$/
     */
    public function fileMustContainAMarker($filename)
    {
        $ast = $this->getAst();

        /** @var FileDescriptor $file */
        $file = $ast->getFiles()->get($filename);

        Assert::count($file->getMarkers(), 1);
    }

    /**
     * @Then class ":className" must have magic property ":propertyName" of type :type
     */
    public function classMustHaveMagicPropertyOfType($className, $propertyName, $type)
    {
        $classDescriptor = $this->findClassByFqsen($className);
        /** @var PropertyDescriptor $propertyDescriptor */
        $propertyDescriptor = null;
        foreach ($classDescriptor->getMagicProperties() as $property) {
            if ($property->getName() === $propertyName) {
                $propertyDescriptor = $property;
                break;
            }
        }

        Assert::isInstanceOf($propertyDescriptor, PropertyDescriptor::class);
        Assert::eq($type, (string) $propertyDescriptor->getType());
    }

    /**
     * @Then /^file "([^"]*)" must contain an error$/
     */
    public function fileMustContainAnError($filename) : void
    {
        $ast = $this->getAst();

        /** @var FileDescriptor $file */
        $file = $ast->getFiles()->get($filename);

        Assert::count($file->getAllErrors(), 1);
    }
}
