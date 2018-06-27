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

namespace phpDocumentor\Plugin\Scrybe\Command\Manual;

use \phpDocumentor\Plugin\Scrybe\Converter\Format;

/**
 * Command used to tell the application to convert from a format to HTML.
 */
class ToHtmlCommand extends BaseConvertCommand
{
    /** @var string The string representation of the output format */
    protected $output_format = Format\Format::HTML;

    /**
     * Defines the name and description for this command and inherits the
     * behaviour of the parent configure.
     *
     * @see ConvertCommandAbstract::configure() for the common business rules.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('manual:to-html');
        $this->setDescription('Generates reference documentation as HTML files');
    }
}
