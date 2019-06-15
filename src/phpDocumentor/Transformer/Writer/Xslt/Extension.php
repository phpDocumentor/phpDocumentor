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

namespace phpDocumentor\Transformer\Writer\Xslt;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Router\Queue;

/**
 * XSLT filters that can be used inside a template.
 */
class Extension
{
    /**
     * @var Queue
     */
    public static $routers;

    /**
     * @var ProjectDescriptor
     */
    public static $projectDescriptor;

    /**
     * Markdown filter.
     *
     * Example usage inside template would be:
     *
     * ```
     * <div class="long_description">
     *     <xsl:value-of
     *         select="php:function('phpDocumentor\Transformer\Writer\Xslt\Extension::markdown',
     *             string(docblock/long-description))"
     *         disable-output-escaping="yes" />
     * </div>
     * ```
     *
     * @param string $text
     *
     * @return string
     */
    public static function markdown($text)
    {
        if (!is_string($text)) {
            return $text;
        }

        $markdown = \Parsedown::instance();

        return $markdown->parse($text);
    }

    /**
     * Returns a relative URL from the webroot if the given FQSEN exists in the project.
     *
     * Example usage inside template would be (where @link is an attribute called link):
     *
     * ```
     * <xsl:value-of select="php:function('phpDocumentor\Transformer\Writer\Xslt\Extension::path', string(@link))" />
     * ```
     *
     * @param string $fqsen
     *
     * @return bool|string
     */
    public static function path($fqsen)
    {
        $node = self::getDocumentedElement($fqsen) ?: $fqsen;

        $rule = self::$routers->match($node);
        if (! $rule) {
            return '';
        }

        $generatedUrl = $rule->generate($node);

        return $generatedUrl ? ltrim($generatedUrl, '/') : false;
    }

    /**
     * Example usage inside template would be (where @link is an attribute called link):
     *
     * ```
     * <xsl:value-of
     *   select="php:function('phpDocumentor\Transformer\Writer\Xslt\Extension::typeOfElement', string(@link))"
     * />
     * ```
     * @param string $link
     *
     * @return string
     */
    public static function typeOfElement($link)
    {
        if (self::getDocumentedElement($link)) {
            return 'documented';
        }

        if (!self::getDocumentedElement($link) && filter_var($link, FILTER_VALIDATE_URL)) {
            return 'url';
        }

        return 'undocumented';
    }

    /**
     * Returns a Descriptor Object if the given FQSEN exists in the project.
     *
     * @param string $fqsen
     *
     * @return bool|DescriptorAbstract
     */
    private static function getDocumentedElement($fqsen)
    {
        $projectDescriptor = self::$projectDescriptor;
        $elementList = $projectDescriptor->getIndexes()->get('elements');
        $prefixedLink = '~\\' . $fqsen;

        if (isset($elementList[$fqsen])) {
            return $elementList[$fqsen];
        } elseif (isset($elementList[$prefixedLink])) {
            return $elementList[$prefixedLink];
        }

        return false;
    }
}
