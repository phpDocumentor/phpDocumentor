<?php

namespace phpDocumentor\Descriptor;

/**
 * Class to check if see tags are working correctly
 *
 * Inline see to same class relative {@see TestSeeTagIssue}
 * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue}
 *
 * Inline see to same class relative {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
 * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
 *
 * Inline see to property in same class relative {@see TestSeeTagIssue::$property}
 * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property}
 *
 * Inline see to property in same class relative {@see TestSeeTagIssue::$property own property}
 * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property}
 *
 * Inline see to method in same class relative {@see TestSeeTagIssue::method()}
 * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method()}
 *
 * Inline see to method in same class relative {@see TestSeeTagIssue::method() own method}
 * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method}
 *
 * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT}
 * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT}
 *
 * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT own constant}
 * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant}
 *
 * @see http://www.phpdoc.org
 * @see https://www.phpdoc.org
 * @see http://phpdoc.org
 * @see http://www.phpdoc.org/docs/latest/references/phpdoc/tags/uses.html
 * @see ftp://somesite.nl
 *
 * @see TestSeeTagIssue
 * @see TestSeeTagIssue class itself
 *
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue class itself
 *
 * @see TestSeeTagIssue::$property
 * @see TestSeeTagIssue::$property own property
 *
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property
 *
 * @see TestSeeTagIssue::method()
 * @see TestSeeTagIssue::method() own method
 *
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method()
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method
 *
 * @see TestSeeTagIssue::CONSTANT
 * @see TestSeeTagIssue::CONSTANT own constant
 *
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT
 * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant
 */
class TestSeeTagIssue
{
    /**
     * Constant to check if see tags are working correctly
     *
     * Inline see to same class relative {@see TestSeeTagIssue}
     * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue}
     *
     * Inline see to same class relative {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
     * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
     *
     * Inline see to property in same class relative {@see TestSeeTagIssue::$property}
     * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property}
     *
     * Inline see to property in same class relative {@see TestSeeTagIssue::$property own property}
     * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property}
     *
     * Inline see to method in same class relative {@see TestSeeTagIssue::method()}
     * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method()}
     *
     * Inline see to method in same class relative {@see TestSeeTagIssue::method() own method}
     * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method}
     *
     * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT}
     * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT}
     *
     * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT own constant}
     * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant}
     *
     * @see http://www.phpdoc.org
     * @see https://www.phpdoc.org
     * @see http://phpdoc.org
     * @see http://www.phpdoc.org/docs/latest/references/phpdoc/tags/uses.html
     * @see ftp://somesite.nl
     *
     * @see TestSeeTagIssue
     * @see TestSeeTagIssue class itself
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue class itself
     *
     * @see TestSeeTagIssue::$property
     * @see TestSeeTagIssue::$property own property
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property
     *
     * @see TestSeeTagIssue::method()
     * @see TestSeeTagIssue::method() own method
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method()
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method
     *
     * @see TestSeeTagIssue::CONSTANT
     * @see TestSeeTagIssue::CONSTANT own constant
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant
     */
    const CONSTANT = 1;

    /**
     * Property to check if see tags are working correctly
     *
     * Inline see to same class relative {@see TestSeeTagIssue}
     * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue}
     *
     * Inline see to same class relative {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
     * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
     *
     * Inline see to property in same class relative {@see TestSeeTagIssue::$property}
     * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property}
     *
     * Inline see to property in same class relative {@see TestSeeTagIssue::$property own property}
     * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property}
     *
     * Inline see to method in same class relative {@see TestSeeTagIssue::method()}
     * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method()}
     *
     * Inline see to method in same class relative {@see TestSeeTagIssue::method() own method}
     * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method}
     *
     * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT}
     * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT}
     *
     * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT own constant}
     * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant}
     *
     * @see http://www.phpdoc.org
     * @see https://www.phpdoc.org
     * @see http://phpdoc.org
     * @see http://www.phpdoc.org/docs/latest/references/phpdoc/tags/uses.html
     * @see ftp://somesite.nl
     *
     * @see TestSeeTagIssue
     * @see TestSeeTagIssue class itself
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue class itself
     *
     * @see TestSeeTagIssue::$property
     * @see TestSeeTagIssue::$property own property
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property
     *
     * @see TestSeeTagIssue::method()
     * @see TestSeeTagIssue::method() own method
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method()
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method
     *
     * @see TestSeeTagIssue::CONSTANT
     * @see TestSeeTagIssue::CONSTANT own constant
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant
     */
    public $property;

    /**
     * Method to check if see tags are working correctly
     *
     * Inline see to same class relative {@see TestSeeTagIssue}
     * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue}
     *
     * Inline see to same class relative {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
     * Inline see to same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue class itself}
     *
     * Inline see to property in same class relative {@see TestSeeTagIssue::$property}
     * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property}
     *
     * Inline see to property in same class relative {@see TestSeeTagIssue::$property own property}
     * Inline see to property in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property}
     *
     * Inline see to method in same class relative {@see TestSeeTagIssue::method()}
     * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method()}
     *
     * Inline see to method in same class relative {@see TestSeeTagIssue::method() own method}
     * Inline see to method in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method}
     *
     * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT}
     * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT}
     *
     * Inline see to constant in same class relative {@see TestSeeTagIssue::CONSTANT own constant}
     * Inline see to constant in same class absolute {@see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant}
     *
     * @see http://www.phpdoc.org
     * @see https://www.phpdoc.org
     * @see http://phpdoc.org
     * @see http://www.phpdoc.org/docs/latest/references/phpdoc/tags/uses.html
     * @see ftp://somesite.nl
     *
     * @see TestSeeTagIssue
     * @see TestSeeTagIssue class itself
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue class itself
     *
     * @see TestSeeTagIssue::$property
     * @see TestSeeTagIssue::$property own property
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::$property own property
     *
     * @see TestSeeTagIssue::method()
     * @see TestSeeTagIssue::method() own method
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method()
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::method() own method
     *
     * @see TestSeeTagIssue::CONSTANT
     * @see TestSeeTagIssue::CONSTANT own constant
     *
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT
     * @see \phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT own constant
     */
    public function method()
    {
        // body of method
    }
}
