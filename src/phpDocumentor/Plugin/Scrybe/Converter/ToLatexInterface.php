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

namespace phpDocumentor\Plugin\Scrybe\Converter;

/**
 * Interface that dictates the custom options that all converters that convert to Latex should have.
 */
interface ToLatexInterface
{
    /**
     * Sets the title that is supposed to be on the front page.
     */
    public function setTitle(string $title): void;

    /**
     * Sets the author name that is supposed to be on the frontpage.
     */
    public function setAuthor(string $author): void;

    /**
     * Sets the filename used as logo or cover picture.
     *
     * The image location should be absolute or relative to the destination location.
     */
    public function setCoverLogo(string $cover_logo): void;
}
