<?php
namespace phpDocumentor\Descriptor;

use Zend\Serializer\Adapter\AdapterInterface;

abstract class BuilderAbstract
{
    /** @var ProjectDescriptor $project */
    protected $project;

    /** @var AdapterInterface $serializer */
    protected $serializer;

    public function __construct(ProjectDescriptor $project = null)
    {
        $this->project = $project ?: new ProjectDescriptor('Untitled project');
        $this->project->getIndexes()->set('classes', new Collection());
        $this->project->getIndexes()->set('interfaces', new Collection());
        $this->project->getIndexes()->set('traits', new Collection());
    }

    public function getProjectDescriptor()
    {
        return $this->project;
    }

    abstract public function buildFile($data);
    abstract public function buildClass($data);
    abstract public function buildInterface($data);
    abstract public function buildTrait($data);
    abstract public function buildFunction($data);
    abstract public function buildConstant($data, $container = null);
    abstract public function buildMethod($data, $container);
    abstract public function buildProperty($data, $container);

    public function setSerializer(AdapterInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function export()
    {
        return $this->serializer->serialize($this->project);
    }

    public function import($data)
    {
        $this->project = $this->serializer->unserialize($data);
    }
}