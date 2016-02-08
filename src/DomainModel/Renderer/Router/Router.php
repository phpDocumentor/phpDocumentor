<?php
namespace phpDocumentor\DomainModel\Renderer\Router;

use phpDocumentor\DomainModel\Renderer\Router\Rule;

/**
 * Object containing a collection of routes.
 */
interface Router
{
    /**
     * Configuration function to add routing rules to a router.
     *
     * @return void
     */
    public function configure();

    /**
     * Tries to match the provided node with one of the rules in this router.
     *
     * @param mixed $node
     *
     * @return Rule|null
     */
    public function match($node);
}
