<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Input;

use Symfony\Component\Console\Input\InputDefinition;

/**
 * Argv input for the Console component of Symfony adapted to phpDocumentor.
 *
 * This InputStream for the Symfony Console component prepends the namespace and command
 * name `project:run` to the Argv array if no command has been provided.
 */
class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput
{
    /**
     * Constructor.
     *
     * The constructor has been overridden to check whether the first element in
     * the argument list is an argument (as it represents the command name).
     *
     * If it is not then we insert the command name: *project:run*.
     *
     * This way we can ensure that if no command name is given that the project
     * defaults to the execution of phpDocumentor. This is behavior that is
     * expected from previous releases of phpDocumentor.
     *
     * @param string[]        $argv       An array of parameters from the CLI (in the argv format)
     * @param InputDefinition $definition A InputDefinition instance
     *
     * @api
     */
    public function __construct(array $argv = null, InputDefinition $definition = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }

        if ((count($argv) === 1) || ($argv[1][0] === '-')) {
            array_splice($argv, 1, 0, 'project:run');
        }

        parent::__construct($argv, $definition);
    }
}
