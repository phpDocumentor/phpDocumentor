<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Tasks
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Abstract base class for the tasks.
 *
 * @category DocBlox
 * @package  Tasks
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 *
 * @method string getHelp()
 * @method void   setHelp(string $help)
 */
abstract class DocBlox_Task_Abstract extends Zend_Console_Getopt
{
    /** @var string The description used for usage */
    protected $usage_description = null;

    /** @var string The namespace:task name for this task*/
    protected $taskname = '';

    /**
     * Override constructor to temporarily wait with defining rules.
     */
    public function __construct()
    {
        parent::__construct(array());

        // we always offer a help message
        $this->addOption('h|help', '', 'Show this help message');
        $this->addOption('q|quiet', '', 'Silences the output and logging');

        // we always offer a configuration option
        $this->addOption(
            'c|config', '-s',
            'Configuration filename OR "none", when this option is omitted '
            . 'DocBlox tries to load the docblox.xml or docblox.dist.xml '
            . 'from the current working directory'
        );

        $this->configure();

        // by default we do _not_ make this parseable to allow tasks to be nested
        $this->_parsed = true;
    }

    /**
     * Hook method which is invoked right before all setters are invoked.
     *
     * @return void
     */
    protected function prePopulate()
    {

    }

    /**
     * Parses the configuration options and populates the data store.
     *
     * @param bool $force if true; forces parsing independently of the
     *     _parsed property.
     *
     * @return null|self
     */
    public function parse($force = false)
    {
        if (($this->_parsed === true) && (!$force)) {
            return $this;
        }
        $this->_parsed = false;

        try {
            parent::parse();
        }
        catch (Zend_Exception $e)
        {
            $name = basename($_SERVER['SCRIPT_NAME'], '.php');
            echo($name . ': ' . $e->getMessage() . PHP_EOL);
            echo('Try: \'' . $name . ' --help\' for more information.' . PHP_EOL);
            exit(22);
        }

        if ($this->getHelp()) {
            DocBlox_Core_Abstract::renderVersion();
            echo $this->getUsageMessage();
            exit(0);
        }

        // prevent the loading of configuration files by specifying 'none'.
        if (strtolower($this->getConfig()) == 'none') {
            return null;
        }

        if ($this->getConfig()) {
            // when the configuration parameter is provided; merge that
            // with the basic config
            DocBlox_Core_Abstract::config()->merge(
                new Zend_Config_Xml($this->getConfig())
            );
        } elseif (is_readable('docblox.xml')) {
            // when the configuration is not provided; check for the presence
            // of a configuration file in the current directory and merge that
            DocBlox_Core_Abstract::config()->merge(
                new Zend_Config_Xml('docblox.xml')
            );
        } elseif (is_readable('docblox.dist.xml')) {
            // when no docblox.xml is provided; check for a dist.xml file.
            // Yes, compared to, for example, PHPUnit the xml and dist is
            // reversed; this is done on purpose so IDEs have an easier time
            // on it.
            DocBlox_Core_Abstract::config()->merge(
                new Zend_Config_Xml('docblox.dist.xml')
            );
        }

        $this->prePopulate();

        // the parse method does not have a hook point to invoke the setter
        // methods; thus we iterate through the options and invoke the setters.
        // If no setter exists the __call method will handle this. We have
        // explicitly kept this intact (as the __call's set does nothing special)
        // to enable subclasses to override the __call and receive the benefits.
        foreach ($this->getOptions() as $value) {
            $method_name = '';

            // loop through all aliases to check whether a real method
            // was overridden
            foreach ($this->_rules[$value]['alias'] as $alias) {
                $method_name = 'set' . str_replace(
                    ' ', '', ucwords(str_replace('_', ' ', $alias))
                );
                if (method_exists($this, $method_name)) {
                    // found one! execute it and continue to the next
                    $this->$method_name($this->getOption($value));
                    continue 2;
                }
            }

            if ($method_name == '') {
                throw new Exception(
                    'Unable to find a name for the setter for argument ' . $value
                );
            }

            // no overridden methods found; just invoke the default name to
            // trigger the __call method
            $this->$method_name($this->getOption($value));
        }

        return null;
    }

    /**
     * Adds an option rule to the application.
     *
     * @param string[]|string $flags          Set of flags to support for this
     *     option.
     * @param string          $parameter_type May be nothing, or an string (s),
     *     word (w) or integer (i) prefixed with the availability specifier
     *     (- for optional and = for required).
     * @param string          $description    Help text
     *
     * @return void
     */
    public function addOption($flags, $parameter_type, $description)
    {
        if (!is_array($flags)) {
            $flags = array($flags);
        }

        $this->addRules(
            array(implode('|', $flags) . $parameter_type => $description)
        );
        $this->_parsed = true;
    }

    /**
     * Sets a description message for this task.
     *
     * @param string $description multi-line text with unlimited length.
     *
     * @return void
     */
    public function setUsageDescription($description)
    {
        $this->usage_description = $description;
    }

    /**
     * Returns the usage description or null if none is set.
     *
     * @return string|null
     */
    public function getUsageDescription()
    {
        if ($this->usage_description === null) {
            $refl = new DocBlox_Reflection_DocBlock(new ReflectionObject($this));
            $this->usage_description = $refl->getLongDescription()->getContents();
        }

        return $this->usage_description;
    }

    /**
     * Generates the usage / help message.
     *
     * @return string
     */
    public function getUsageMessage()
    {
        $prog_name = basename($this->_progname);

        $usage = '';
        if ($this->getUsageDescription()) {
            $usage .= $this->getUsageDescription() . PHP_EOL . PHP_EOL;
        }
        $usage .= "Usage:\n {$prog_name} {$this->taskname} [options]\n\n";

        $lines = array();
        $maxLen = 20;
        foreach ($this->_rules as $rule) {
            $flags = array();
            if (is_array($rule['alias'])) {
                foreach ($rule['alias'] as $flag) {
                    $flags[] = (strlen($flag) == 1 ? '-' : '--') . $flag;
                }
            }
            $linepart['name'] = implode(' [', $flags) . (count($flags) > 1 ? ']'
                    : '');
            if (isset($rule['param']) && $rule['param'] != 'none') {
                $linepart['name'] .= ' ';
                $rule['paramType'] = strtoupper($rule['paramType']);
                switch ($rule['param'])
                {
                case 'optional':
                    $linepart['name'] .= "[{$rule['paramType']}]";
                    break;
                case 'required':
                    $linepart['name'] .= "{$rule['paramType']}";
                    break;
                }
            }
            if (strlen($linepart['name']) > $maxLen) {
                $maxLen = strlen($linepart['name']);
            }
            $linepart['help'] = '';
            if (isset($rule['help'])) {
                $linepart['help'] .= $rule['help'];
            }
            $lines[] = $linepart;
        }

        foreach ($lines as $linepart) {
            $usage .= sprintf(
                "%s %s\n", str_pad($linepart['name'], $maxLen), $linepart['help']
            );
        }
        return $usage . PHP_EOL;
    }

    /**
     * If the method name is prefixed with 'get', it will try to find the
     * parameter in the options array.
     *
     * @param string   $name      Name of the invoked method.
     * @param string[] $arguments Array with arguments passed to the method.
     *
     * @return void|string
     */
    public function __call($name, $arguments)
    {
        // convert key from a camel-case term to an underscore one.
        $key = substr($name, 3);
        $key = strtolower(
            preg_replace(
                array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'),
                array('\\1_\\2', '\\1_\\2'),
                $key
            )
        );

        switch (substr($name, 0, 3))
        {
        case 'get':
            return $this->$key;
        case 'set':
            $this->$key = reset($arguments);
            return null;
        }
    }

    /**
     * Additionally checks whether the given filename is readable.
     *
     * @param string $value Path to the configuration file.
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setConfig($value)
    {
        if ($value
            && !is_readable($value) && (strtolower($value) !== 'none')
        ) {
            throw new InvalidArgumentException(
                'Config file "' . $value . '" is not readable'
            );
        }

        $this->__call('setConfig', array($value));
    }

    /**
     * Configuration override for setting the parser visibility
     *
     * By default it will use the command line options first, and then
     * look at the config file if no options have been supplied
     *
     * @return string
     */
    protected function getVisibility()
    {
        $visibility = $this->__call('getVisibility', array());

        if ('' == $visibility) {
            $visibility = DocBlox_Core_Abstract::config()->parser->visibility;
        }

        return $visibility;
    }

    /**
     * Prepare the settings and rules for this Task.
     *
     * By default it is empty and it is not required for a task to implement
     * this if there is no benefit.
     *
     * @return void
     */
    protected function configure()
    {

    }

    /**
     * Method containing the actual business rules for this Task.
     *
     * @return void
     */
    abstract public function execute();
}
