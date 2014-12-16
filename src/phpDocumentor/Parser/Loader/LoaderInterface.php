<?php
namespace phpDocumentor\Parser\Loader;

interface LoaderInterface
{
    public function match(Uri $location);

    /**
     * @param Uri $location
     *
     * @return array {
     *   @element string[] 'files'?
     *   @element string[] 'directories'?
     * }
     */
    public function fetch(Uri $location);
}