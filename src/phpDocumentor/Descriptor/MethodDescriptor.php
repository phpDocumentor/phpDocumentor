<?php
namespace phpDocumentor\Descriptor;

class MethodDescriptor extends DescriptorAbstract implements Interfaces\MethodInterface
{
    /** @var bool $abstract */
    protected $abstract = false;

    /** @var bool $final */
    protected $final = false;

    /** @var bool $static */
    protected $static = false;

    /** @var string $visibility */
    protected $visibility = 'public';

    /** @var Collection */
    protected $arguments;

    public function __construct()
    {
        parent::__construct();
        $this->setArguments(new Collection());
    }

    /**
     * @param boolean $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * @return boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
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

    /**
     * @param boolean $final
     */
    public function setFinal($final)
    {
        $this->final = $final;
    }

    /**
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * @param boolean $static
     */
    public function setStatic($static)
    {
        $this->static = $static;
    }

    /**
     * @return boolean
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }
}
