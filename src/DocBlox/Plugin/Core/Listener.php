<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Plugin
 * @subpackage Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Listener for the Core Plugin.
 *
 * @category   DocBlox
 * @package    Plugin
 * @subpackage Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Listener extends DocBlox_Plugin_ListenerAbstract
{
    /** @var DocBlox_Core_Log Logger for DocBlox*/
    protected $logger = null;

    /**
     * Configures this listener; initializes and connects the logger.
     *
     * @return void
     */
    protected function configure()
    {
        $this->logger = new DocBlox_Core_Log(DocBlox_Core_Log::FILE_STDOUT);
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
     * @docblox-event transformer.transform.pre
     *
     * @return void
     */
    public function applyBehaviours(sfEvent $data)
    {
        if (!$data->getSubject() instanceof DocBlox_Transformer) {
            throw new DocBlox_Plugin_Core_Exception(
                'Unable to apply behaviours, the invoking object is not a '
                . 'DocBlox_Transformer'
            );
        }

        $behaviours = new DocBlox_Plugin_Core_Transformer_Behaviour_Collection(
            $data->getSubject(),
            array(
                 new DocBlox_Plugin_Core_Transformer_Behaviour_GeneratePaths(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Inherit(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Ignore(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Return(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Param(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Property(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Method(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Uses(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Author(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_License(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_Tag_Internal(),
                 new DocBlox_Plugin_Core_Transformer_Behaviour_AddLinkInformation(),
            )
        );

        $data['source'] = $behaviours->process($data['source']);
    }

    /**
     * Checks all DocBlox whether they match the given rules.
     *
     * @param sfEvent $data Event object containing the parameters.
     *
     * @docblox-event reflection.docblock-extraction.post
     *
     * @return void
     */
    public function validateDocBlocks(sfEvent $data)
    {
        /** @var DocBlox_Reflection_DocBlockedAbstract $element  */
        $element = $data->getSubject();

        /** @var DocBlox_Reflection_DocBlock $docblock  */
        $docblock = $data['docblock'];

        // get the type of element
        $type = substr(
            get_class($element),
            strrpos(get_class($element), '_') + 1
        );

        // if the object has no DocBlock _and_ is not a Closure; throw a warning
        if (!$docblock && !(($type == 'Function')
            && ($element->getName() == 'Closure'))
        ) {
            $this->logParserError(
                'ERROR', 'No DocBlock was found for ' . $type . ' '
                . $element->getName(), $element->getLineNumber()
            );
        }

        // no docblock so no reason to validate
        if (!$docblock) {
            return;
        }

        $class = 'DocBlox_Plugin_Core_Parser_DocBlock_Validator_' . $type;
        if (@class_exists($class)) {
            /**
             * @var DocBlox_Plugin_Core_Parser_DocBlock_Validator_Abstract $validator
             */
            $validator = new $class(
                $element->getName(),
                $docblock->line_number,
                $docblock,
                $element
            );

            $validator->isValid();
        }

        $configOptions = $this->plugin->getOptions();
        $validatorOptions = array();

        if (isset($configOptions['deprecated']->tag)) {
            foreach ($configOptions['deprecated']->tag as $tag) {
                $tagName = (string)$tag['name'];

                if (isset($tag->element)) {
                    foreach ($tag->element as $type) {
                        $typeName = (string)$type;
                        $validatorOptions['deprecated'][$typeName][] = $tagName;
                    }
                } else {
                    $validatorOptions['deprecated']['__ALL__'][] = $tagName;
                }
            }
        }

        if (isset($configOptions['required']->tag)) {
            foreach ($configOptions['required']->tag as $tag) {
                $tagName = (string)$tag['name'];

                if (isset($tag->element)) {
                    foreach ($tag->element as $type) {
                        $typeName = (string)$type;
                        $validatorOptions['required'][$typeName][] = $tagName;
                    }
                } else {
                    $validatorOptions['required']['__ALL__'][] = $tagName;
                }
            }
        }

        foreach (array('Deprecated', 'Required') as $validator) {
            $class = 'DocBlox_Plugin_Core_Parser_DocBlock_Validator_' . $validator;
            $val = new $class(
                $element->getName(),
                $docblock->line_number,
                $docblock,
                $element
            );

            $val->setOptions($validatorOptions);
            $val->isValid();
        }
    }

    /**
     * Prepare the tag to be injected into the XML file.
     *
     * @param sfEvent $data Event object containing the parameters.
     *
     * @docblox-event reflection.docblock.tag.export
     *
     * @return void
     */
    public function exportTag(sfEvent $data)
    {
        /** @var DocBlox_Reflection_DocBlockedAbstract $subject  */
        $subject = $data->getSubject();

        DocBlox_Plugin_Core_Parser_DocBlock_Tag_Definition::create(
            $subject->getNamespace(),
            $subject->getNamespaceAliases(),
            $data['xml'],
            $data['object']
        );
    }
}
