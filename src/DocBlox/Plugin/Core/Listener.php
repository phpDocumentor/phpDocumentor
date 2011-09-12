<?php
/**
 *
 */
class DocBlox_Plugin_Core_Listener
{
    protected $event_dispatcher = null;
    protected $configuration    = null;
    protected $logger           = null;

    function __construct($event_dispatcher, $configuration)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->configuration    = $configuration;

        $this->logger = new DocBlox_Core_Log(DocBlox_Core_Log::FILE_STDOUT);
        $this->logger->setThreshold($configuration->logging->level);

        $event_dispatcher->connect('system.log', array($this->logger, 'log'));
    }

    /**
     *
     * @event transformer.pre-transform
     *
     * @param sfEvent $data
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

        $data[1] = $behaviours->process($data[0]);
    }

    /**
     *
     *
     * @param sfEvent $data
     *
     * @event reflection.post-docblock-extraction
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
        if (!$docblock && ($type !== 'Function')
            && ($element->getName() !== 'Closure')
        ) {
            $element->logParserError(
                'ERROR', 'No DocBlock was found for ' . $type . ' '
                . $element->getName(), $element->getLineNumber()
            );
        }

        // no docblock so no reason to validate
        if (!$docblock) {
            return;
        }

        $class = 'DocBlox_Parser_DocBlock_Validator_' . $type;
        if (@class_exists($class)) {

            /** @var DocBlox_Parser_DocBlock_Validator_Abstract $validator  */
            $validator = new $class(
                $element->getFilename(),
                $docblock->line_number,
                $docblock,
                $element
            );

            $validator->isValid();
        }
    }
}
