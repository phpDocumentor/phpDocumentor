<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Unit_tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @link       https://phpdoc.org
 */

/**
 * @api
 * @var int
 */
define('BLA', 1);

/**
 * @var int
 */
define('BLA2', 2);

/**
 * Constant description
 *
 * @api
 *
 * @var int
 */
const BLA3 = 1;

/**
 * @var int
 */
const BLA4 = 1;

/**
 * Function description
 *
 * @api
 */
function bla5()
{
}

function bla6()
{
}

/**
 * Fixture file for the API tests.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Unit_tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @link       https://phpdoc.org
 * @api
 */
class phpDocumentor_Tests_Data_ApiFixture
{
    /**
     * @api
     * @var int
     */
    const BLA7 = 1;

    /**
     * #var int
     */
    const BLA8 = 1;

    /**
     * Property description
     *
     * @api
     *
     * @var string
     */
    public $bla9 = '1';

    /**
     * @var string
     */
    public $bla10 = '1';

    /**
     * Function description
     *
     * @api
     */
    public function bla11()
    {
    }

    public function bla12()
    {
    }
}
