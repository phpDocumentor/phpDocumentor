<?php
namespace phpDocumentor\DomainModel\Renderer;

use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\Uri;
use phpDocumentor\DomainModel\Renderer\RenderContext;

/**
 * Creates a new Template based on an array with a Template definition.
 */
interface TemplateFactory
{
    /**
     * Creates a new Template entity with the given name, parameters and options.
     *
     * @param RenderContext $renderContext
     * @param string[] $options Array with a 'name', 'parameters' and 'actions' key.
     *
     * @throws \InvalidArgumentException if the given options array does not map onto a Template.
     *
     * @return Template
     */
    public function create(RenderContext $renderContext, array $options);

    /**
     * @param RenderContext $renderContext
     * @param string $name
     *
     * @return null|Template
     */
    public function createFromName(RenderContext $renderContext, $name);

    /**
     * @param RenderContext $renderContext
     * @param Uri $uri
     *
     * @return Template
     */
    public function createFromUri(RenderContext $renderContext, Uri $uri);
}
