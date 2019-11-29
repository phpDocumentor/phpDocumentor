<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\TagDescriptor;

class MethodDescriptor extends TagDescriptor
{
    protected $methodName = '';

    protected $arguments;

    protected $response;

    /** @var bool */
    protected $static;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->arguments = new Collection();
    }

    public function setMethodName(string $methodName) : void
    {
        $this->methodName = $methodName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @param mixed $arguments
     */
    public function setArguments($arguments) : void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response) : void
    {
        $this->response = $response;
    }

    public function getResponse() : ?ReturnDescriptor
    {
        return $this->response;
    }

    public function setStatic(bool $static) : void
    {
        $this->static = $static;
    }

    public function isStatic() : bool
    {
        return $this->static;
    }
}
