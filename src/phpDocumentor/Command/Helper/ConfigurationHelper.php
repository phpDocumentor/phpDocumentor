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

namespace phpDocumentor\Command\Helper;

use phpDocumentor\Configuration;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;

class ConfigurationHelper extends Helper
{
    /**
     * @var \phpDocumentor\Configuration
     */
    private $configuration;

    /**
     * Initializes this helper and registers the application configuration on it.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @codeCoverageIgnore it is not interesting to test a name.
     *
     * @api
     */
    public function getName()
    {
        return 'phpdocumentor_configuration';
    }

    /**
     * Returns the value of an option from the command-line parameters,
     * configuration or given default.
     *
     * @param InputInterface $input           Input interface to query for information
     * @param string         $name            Name of the option to retrieve from argv
     * @param string|null    $configPath     Path to the config element(s) containing the value to be used when
     *     no option is provided.
     * @param mixed|null     $default         Default value used if there is no configuration option or path set
     * @param bool           $commaSeparated Could the value be a comma separated string requiring splitting
     *
     * @return string|array
     */
    public function getOption(
        InputInterface $input,
        $name,
        $configPath = null,
        $default = null,
        $commaSeparated = false
    ) {
        $value = $input->getOption($name);

        // find value in config
        if ($this->valueIsEmpty($value) && $configPath !== null) {
            $value = $this->getConfigValueFromPath($configPath);
        }

        // use default if value is still null
        if ($this->valueIsEmpty($value)) {
            return (is_array($value) && $default === null) ? array() : $default;
        }

        return $commaSeparated
            ? $this->splitCommaSeparatedValues($value)
            : $value;
    }

    /**
     * Split comma separated values.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function splitCommaSeparatedValues($value)
    {
        if (!is_array($value) || (count($value) == 1) && is_string(current($value))) {
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
    public function getConfigValueFromPath($path)
    {
        /** @var Configuration $node */
        $node = $this->configuration;

        foreach (explode('/', $path) as $nodeName) {
            if (!is_object($node)) {
                return null;
            }

            $node = $node->{'get' . ucfirst($nodeName)}();
        }

        return $node;
    }
}
