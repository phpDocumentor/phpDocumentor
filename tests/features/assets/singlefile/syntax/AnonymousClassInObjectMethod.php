<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

/**
 * Test class
 */
class TTT
{
    /**
     * Test method
     * @return string
     */
    public function a()
    {
        $a = null ?? new class() {
            public function export()
            {
                return null;
            }
        };
        return 'test';
    }
}
