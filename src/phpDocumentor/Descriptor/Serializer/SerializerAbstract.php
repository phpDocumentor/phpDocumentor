<?php
namespace phpDocumentor\Descriptor\Serializer;


use phpDocumentor\Descriptor\ProjectDescriptor;

abstract class SerializerAbstract
{
    abstract public function serialize(ProjectDescriptor $project);
    abstract public function unserialize($data);

    public function getFilename()
    {
        return 'structure.'.$this->getExtension().'.pdast';
    }

    abstract protected function getExtension();
}