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


use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class BaseContext
{
    /** @var EnvironmentContext */
    private $environmentContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->environmentContext = $environment->getContext('phpDocumentor\Behat\Contexts\EnvironmentContext');
    }

    /**
     * @param string $classFqsen
     * @return ClassDescriptor
     * @throws \Exception
     */
    protected function findClassByFqsen($classFqsen)
    {
        $ast = $this->getAst();
        foreach ($ast->getFiles() as $file) {
            /** @var ClassDescriptor $classDescriptor */
            foreach ($file->getClasses() as $classDescriptor) {
                if ($classDescriptor->getFullyQualifiedStructuralElementName() === $classFqsen) {
                    return $classDescriptor;
                }
            }
        }

        throw new \Exception(sprintf('Didn\'t find expected class "%s"', $classFqsen));
    }

    /**
     * @return ProjectDescriptor|null
     * @throws \Exception when AST file doesn't exist
     */
    protected function getAst()
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
}
