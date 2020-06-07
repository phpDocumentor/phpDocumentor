<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @link      https://phpdoc.org
 */

interface ExampleInterface
{
    /**
     * Do something with $object and return that it worked
     *
     * @param \stdClass $object The object
     * @return bool
     */
    public function doSomething(\stdClass $object);
}

class Example implements ExampleInterface
{
    /** {@inheritdoc} */
    public function doSomething(\stdClass $object)
    {
        // what ever
        return true;
    }
}

interface DeepExampleInterface extends ExampleInterface
{
    /**
     * Convert $json to object and doSomething
     *
     * @param string $json
     * @return bool
     */
    public function doSomethingJson($json);
}

class DeepExample extends Example implements DeepExampleInterface
{
    /** {@inheritdoc} */
    public function doSomethingJson($json)
    {
        return $this->doSomething(json_encode($json));
    }

    /** {@inheritdoc} */
    public function doSomething(\stdClass $object)
    {
        // do something really special
        return true;
    }
}
