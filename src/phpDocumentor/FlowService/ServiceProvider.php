<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService;

use Exception;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use function get_class;

/**
 * @template T of object
 */
final class ServiceProvider
{
    /** @var array<string, T> */
    private $services;

    public function __construct(iterable $services)
    {
        foreach ($services as $key => $service) {
            $this->services[$key] = $service;
        }
    }

    /** @return T */
    public function get(DocumentationSetDescriptor $documetationSet) : object
    {
        if (isset($this->services[get_class($documetationSet)])) {
            return $this->services[get_class($documetationSet)];
        }

        throw new Exception();
    }
}
