<?php
namespace phpDocumentor\Descriptor;

abstract class BuilderAbstract
{
    /** @var ProjectDescriptor $project */
    protected $project;

    public function __construct(ProjectDescriptor $project = null)
    {
        $this->project = $project ?: new ProjectDescriptor('Untitled project');
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
    abstract public function buildMethod($data);
    abstract public function buildProperty($data);
    abstract public function buildConstant($data);
}