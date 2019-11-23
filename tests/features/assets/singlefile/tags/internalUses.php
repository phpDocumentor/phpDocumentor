<?php

namespace phpDocumentor\Descriptor;

/**
 * Class to check if uses tags are working correctly
 *
 * @uses TestUsesTagIssue
 * @uses TestUsesTagIssue class itself
 *
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue class itself
 *
 * @uses TestUsesTagIssue::$property
 * @uses TestUsesTagIssue::$property own property
 *
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property own property
 *
 * @uses TestUsesTagIssue::method()
 * @uses TestUsesTagIssue::method() own method
 *
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method()
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method() own method
 *
 * @uses TestUsesTagIssue::CONSTANT
 * @uses TestUsesTagIssue::CONSTANT own constant
 *
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT
 * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT own constant
 */
class TestUsesTagIssue
{
    /**
     * Constant to check if uses tags are working correctly
     *
     * @uses TestUsesTagIssue
     * @uses TestUsesTagIssue class itself
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue class itself
     *
     * @uses TestUsesTagIssue::$property
     * @uses TestUsesTagIssue::$property own property
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property own property
     *
     * @uses TestUsesTagIssue::method()
     * @uses TestUsesTagIssue::method() own method
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method()
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method() own method
     *
     * @uses TestUsesTagIssue::CONSTANT
     * @uses TestUsesTagIssue::CONSTANT own constant
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT own constant
     */
    const CONSTANT = 1;

    /**
     * Property to check if uses tags are working correctly
     *
     * @uses TestUsesTagIssue
     * @uses TestUsesTagIssue class itself
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue class itself
     *
     * @uses TestUsesTagIssue::$property
     * @uses TestUsesTagIssue::$property own property
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property own property
     *
     * @uses TestUsesTagIssue::method()
     * @uses TestUsesTagIssue::method() own method
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method()
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method() own method
     *
     * @uses TestUsesTagIssue::CONSTANT
     * @uses TestUsesTagIssue::CONSTANT own constant
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT own constant
     */
    public $property;

    /**
     * Method to check if uses tags are working correctly
     *
     * @uses TestUsesTagIssue
     * @uses TestUsesTagIssue class itself
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue class itself
     *
     * @uses TestUsesTagIssue::$property
     * @uses TestUsesTagIssue::$property own property
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::$property own property
     *
     * @uses TestUsesTagIssue::method()
     * @uses TestUsesTagIssue::method() own method
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method()
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::method() own method
     *
     * @uses TestUsesTagIssue::CONSTANT
     * @uses TestUsesTagIssue::CONSTANT own constant
     *
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT
     * @uses \phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT own constant
     */
    public function method()
    {
        // body of method
    }
}
