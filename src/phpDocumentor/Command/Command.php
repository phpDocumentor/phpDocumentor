<?php
namespace phpDocumentor\Command;

use \Symfony\Component\Console\Input\InputInterface;

class Command extends \Cilex\Command\Command
{
    /**
     * Returns the value of an option from the command-line parameters,
     * configuration or given default.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *     Input interface to query for information
     * @param string                                          $name
     *     Name of the option to retrieve from argv
     * @param string|null                                     $config_path
     *     Path to the config element(s) containing the value to be used when
     *     no option is provided.
     * @param mixed|null                                      $default
     *     Default value used if there is no configuration option or path set
     *
     * @return string
     */
    public function getOption(InputInterface $input, $name, $config_path = null,
        $default = null
    ) {
        $value = $input->getOption($name);

        // find value in config
        if (($value === null || is_array($value) && empty($value))
            && $config_path !== null
        ) {
            /** @var \SimpleXMLElement $node  */
            $node = $this->getService('config');
            foreach (explode('/', $config_path) as $node_name) {
                $node = $node->$node_name;
            }

            // small quirk: if there is more than 1 node (thus an array with
            // more than 1 result) than nodeType is null
            $value = $node->nodeType === null ? (array)$node : (string)$node;
        }

        // use default if value is still null
        if ($value === null || is_array($value) && empty($value)) {
            return (is_array($value) && $default === null)
                ? array()
                : $default;
        }

        return $value;
    }
}
