<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Plugin
 * @subpackage Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

/**
 * Listener for the Core Plugin.
 *
 * @category   phpDocumentor
 * @package    Plugin
 * @subpackage Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Plugin_Core_Listener extends phpDocumentor_Plugin_ListenerAbstract
{
    /** @var phpDocumentor_Core_Log Logger for phpDocumentor*/
    protected $logger = null;

    /**
     * Configures this listener; initializes and connects the logger.
     *
     * @return void
     */
    protected function configure()
    {
        $this->logger = new phpDocumentor_Core_Log(phpDocumentor_Core_Log::FILE_STDOUT);
        $this->logger->setThreshold($this->getConfiguration()->logging->level);

        $this->getEventDispatcher()->connect(
            'system.log.threshold', array($this->logger, 'setThreshold')
        );
        $this->event_dispatcher->connect('system.log', array($this->logger, 'log'));
    }

    /**
     * Applies all behaviours prior to transformation.
     *
     * @param sfEvent $data Event object containing the parameters.
     *
     * @phpdoc-event transformer.transform.pre
     *
     * @return void
     */
    public function applyBehaviours(sfEvent $data)
    {
        if (!$data->getSubject() instanceof phpDocumentor_Transformer) {
            throw new phpDocumentor_Plugin_Core_Exception(
                'Unable to apply behaviours, the invoking object is not a '
                . 'phpDocumentor_Transformer'
            );
        }

        $behaviours = new phpDocumentor_Plugin_Core_Transformer_Behaviour_Collection(
            $data->getSubject(),
            array(
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_GeneratePaths(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Inherit(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Ignore(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Return(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Param(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Property(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Method(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Uses(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Author(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_License(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_Tag_Internal(),
                 new phpDocumentor_Plugin_Core_Transformer_Behaviour_AddLinkInformation(),
            )
        );

        $data['source'] = $behaviours->process($data['source']);
    }

    /**
     * Checks all phpDocumentor whether they match the given rules.
     *
     * @param sfEvent $data Event object containing the parameters.
     *
     * @phpdoc-event reflection.docblock-extraction.post
     *
     * @return void
     */
    public function validateDocBlocks(sfEvent $data)
    {
        /** @var phpDocumentor_Reflection_DocBlockedAbstract $element  */
        $element = $data->getSubject();

        /** @var phpDocumentor_Reflection_DocBlock $docblock  */
        $docblock = $data['docblock'];

        // get the type of element
        $type = substr(
            get_class($element),
            strrpos(get_class($element), '_') + 1
        );

        // no docblock, or docblock should be ignored, so no reason to validate
        if ($docblock && $docblock->hasTag('ignore')) {
            return;
        }

        $validatorOptions = $this->loadConfiguration();

        foreach (array('Deprecated', 'Required', $type) as $validator) {

            $class = 'phpDocumentor_Plugin_Core_Parser_DocBlock_Validator_' . $validator;
            if (@class_exists($class)) {

                $val = new $class(
                    $this->plugin,
                    $element->getName(),
                    $docblock,
                    $element
                );

                $val->setOptions($validatorOptions);
                $val->isValid();
            }
        }
    }

    /**
     * Prepare the tag to be injected into the XML file.
     *
     * @param sfEvent $data Event object containing the parameters.
     *
     * @phpdoc-event reflection.docblock.tag.export
     *
     * @return void
     */
    public function exportTag(sfEvent $data)
    {
        /** @var phpDocumentor_Reflection_DocBlockedAbstract $subject  */
        $subject = $data->getSubject();

        phpDocumentor_Plugin_Core_Parser_DocBlock_Tag_Definition::create(
            $subject->getNamespace(),
            $subject->getNamespaceAliases(),
            $data['xml'],
            $data['object']
        );
    }

    /**
     * Load the configuration from the plugin.xml file
     *
     * @return array
     */
    protected function loadConfiguration()
    {
        $configOptions = $this->plugin->getOptions();
        $validatorOptions = array();

        foreach (array('deprecated', 'required') as $tag) {
            $validatorOptions[$tag] = $this->loadConfigurationByElement(
                $configOptions, $tag
            );
        }

        return $validatorOptions;
    }

    /**
     * Load the configuration for given element (deprecated/required)
     *
     * @param array  $configOptions The configuration from the plugin.xml file
     * @param string $configType    Required/Deprecated for the time being
     *
     * @return array
     */
    protected function loadConfigurationByElement($configOptions, $configType)
    {
        $validatorOptions = array();

        if (isset($configOptions[$configType]->tag)) {

            foreach ($configOptions[$configType]->tag as $tag) {
                $tagName = (string)$tag['name'];

                if (isset($tag->element)) {
                    foreach ($tag->element as $type) {
                        $typeName = (string)$type;
                        $validatorOptions[$typeName][] = $tagName;
                    }
                } else {
                    $validatorOptions['__ALL__'][] = $tagName;
                }
            }
        }

        return $validatorOptions;
    }
}
