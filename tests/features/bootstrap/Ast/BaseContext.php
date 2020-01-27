<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts\Ast;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use phpDocumentor\Behat\Contexts\EnvironmentContext;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;

class BaseContext
{
    /** @var EnvironmentContext */
    private $environmentContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope) : void
    {
        $environment = $scope->getEnvironment();

        $this->environmentContext = $environment->getContext(EnvironmentContext::class);
    }

    protected function findClassByFqsen(string $classFqsen) : ClassDescriptor
    {
        $ast = $this->getAst();
        foreach ($ast->getFiles() as $file) {
            /** @var ClassDescriptor $classDescriptor */
            foreach ($file->getClasses() as $classDescriptor) {
                if (((string) $classDescriptor->getFullyQualifiedStructuralElementName()) === $classFqsen) {
                    return $classDescriptor;
                }
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected class "%s"', $classFqsen));
    }

    protected function findFunctionByFqsen(string $fqsen) : FunctionDescriptor
    {
        $ast = $this->getAst();
        foreach ($ast->getFiles() as $file) {
            /** @var FunctionDescriptor $classDescriptor */
            foreach ($file->getFunctions() as $function) {
                if ((string) $function->getFullyQualifiedStructuralElementName() === '\\' . $fqsen . '()') {
                    return $function;
                }
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected function "%s"', $fqsen));
    }

    /**
     * @return ProjectDescriptor|null
     * @throws \Exception when AST file doesn't exist
     */
    protected function getAst() : ?ProjectDescriptor
    {
        $file = $this->environmentContext->getWorkingDir() . '/ast.dump';
        if (!file_exists($file)) {
            throw new \Exception(
                'The output of phpDocumentor was not generated, this probably means that the execution failed. '
                . 'The error output was: ' . $this->environmentContext->getErrorOutput()
            );
        }

        return unserialize(file_get_contents($file));
    }

    protected function processFilePath($file)
    {
        return $file;
    }
}
