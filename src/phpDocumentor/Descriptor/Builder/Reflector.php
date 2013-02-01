<?php
namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\FileReflector;

class Reflector extends BuilderAbstract
{
    /**
     * @param FileReflector $data
     */
    public function buildFile($data)
    {
        $file = new FileDescriptor($data->getHash());
        $file->setLocation($data->getFilename());
        $file->setName(basename($data->getFilename()));

        if ($data->getDocBlock()) {
            $file->setSummary($data->getDocBlock()->getShortDescription());
            $file->setDescription($data->getDocBlock()->getLongDescription()->getContents());

            /** @var Tag $tag */
            foreach($data->getDocBlock()->getTags() as $tag) {
                // TODO: create Tag Descriptor
                $file->getTags()->offsetSet($tag->getName(), $tag);
            }
        }

        $file->setSource($data->getContents());

        foreach ($data->getIncludes() as $include) {
            $file->getIncludes()->append($include);
        }
        foreach ($data->getNamespaceAliases() as $alias) {
            $file->getNamespaceAliases()->append($alias);
        }
        foreach ($data->getParseErrors() as $error) {
            // TODO: improve addition of errors.
            $file->getErrors()->append($error);
        }

        foreach ($data->getConstants() as $constant) {
            $this->buildConstant($constant);
        }
        foreach ($data->getFunctions() as $function) {
            $this->buildFunction($function);
        }
        foreach ($data->getClasses() as $class) {
            $this->buildClass($class);
        }
        foreach ($data->getInterfaces() as $interface) {
            $this->buildInterface($interface);
        }
        foreach ($data->getTraits() as $trait) {
            $this->buildTrait($trait);
        }

        $this->getProjectDescriptor()->getFiles()->offsetSet($file->getPath(), $file);
    }

    public function buildClass($data)
    {
    }

    public function buildInterface($data)
    {
    }

    public function buildTrait($data)
    {
    }

    public function buildFunction($data)
    {
    }

    public function buildMethod($data)
    {
    }

    public function buildProperty($data)
    {
    }

    public function buildConstant($data)
    {
    }
}