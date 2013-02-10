<?php
namespace phpDocumentor\Descriptor;

class FunctionDescriptor extends DescriptorAbstract implements Interfaces\FunctionInterface
{
    /** @var Collection $arguments */
    protected $arguments;

    public function __construct()
    {
        parent::__construct();
        $this->setArguments(new Collection());
    }

    /**
     * @param Collection $arguments
     */
    protected function setArguments(Collection $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return Collection
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
