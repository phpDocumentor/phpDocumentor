<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command;

use phpDocumentor\Configuration;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Base class for commands that may make use of the configuration.
 *
 * Provides helper methods and a default argument to work with the configuration
 * files.
 */
class ConfigurableCommand extends Command
{
    /**
     * Returns the value of an option from the command-line parameters,
     * configuration or given default.
     *
     * @param InputInterface $input           Input interface to query for information
     * @param string         $name            Name of the option to retrieve from argv
     * @param string|null    $config_path     Path to the config element(s) containing the value to be used when
     *     no option is provided.
     * @param mixed|null     $default         Default value used if there is no configuration option or path set
     * @param bool           $comma_separated Could the value be a comma separated string requiring splitting
     *
     * @return string
     */
    public function getOption(
        InputInterface $input,
        $name,
        $config_path = null,
        $default = null,
        $comma_separated = false
    ) {
        $value = $input->getOption($name);

        // find value in config
        if ($this->valueIsEmpty($value) && $config_path !== null) {
            $value = $this->getConfigValueFromPath($config_path);
            if ($value === null) {
                return $default;
            }
        }

        // use default if value is still null
        if ($this->valueIsEmpty($value)) {
            return (is_array($value) && $default === null) ? array() : $default;
        }

        return $this->splitCommaSeparatedValues($value, $comma_separated);
    }
    
    /**
     * Split comma separated values if needed.
     * 
     * @param mixed $value
     * @param bool $comma_separated
     * 
     * @return mixed
     */
    protected function splitCommaSeparatedValues($value, $comma_separated)
    {
        if ($comma_separated
            && (!is_array($value)
                || (count($value) == 1) && is_string(current($value))
            )
        ) {
            $value = (array) $value;
            $value = explode(',', $value[0]);
        }
        
        return $value;
    }
    
    /**
     * Is value empty?
     * 
     * @param mixed $value
     * 
     * @return boolean
     */
    protected function valueIsEmpty($value)
    {
        return $value === null || is_array($value) && empty($value);
    }

    /**
     * Returns a value by traversing the configuration tree as if it was a file
     * path.
     *
     * @param string $path Path to the config value separated by '/'.
     *
     * @return string|integer|boolean
     */
    protected function getConfigValueFromPath($path)
    {
        /** @var Configuration $node */
        $node = $this->getService('config2');

        foreach (explode('/', $path) as $nodeName) {
            if (!is_object($node)) {
                return null;
            }

            $node = $node->{'get' . ucfirst($nodeName)}();
        }

        return $node;
    }
}
