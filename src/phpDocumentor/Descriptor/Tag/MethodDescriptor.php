<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
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

    /**
     * @param string $methodName
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @param mixed $arguments
     */
    public function setArguments($arguments)
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
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return ReturnDescriptor
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param bool $static
     */
    public function setStatic($static)
    {
        $this->static = $static;
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }
}
