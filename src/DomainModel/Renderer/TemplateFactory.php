<?php
namespace phpDocumentor\DomainModel\Renderer;

use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\Uri;
use phpDocumentor\DomainModel\Renderer\Template\RenderPass;

/**
 * Creates a new Template based on an array with a Template definition.
 */
interface TemplateFactory
{
    /**
     * Creates a new Template entity with the given name, parameters and options.
     *
     * @param RenderPass $renderPass
     * @param string[] $options Array with a 'name', 'parameters' and 'actions' key.
     *
     * @throws \InvalidArgumentException if the given options array does not map onto a Template.
     *
     * @return Template
     */
    public function create(RenderPass $renderPass, array $options);

    /**
     * @param RenderPass $renderPass
     * @param string $name
     *
     * @return null|Template
     */
    public function createFromName(RenderPass $renderPass, $name);

    /**
     * @param RenderPass $renderPass
     * @param Uri $uri
     *
     * @return Template
     */
    public function createFromUri(RenderPass $renderPass, Uri $uri);
}
