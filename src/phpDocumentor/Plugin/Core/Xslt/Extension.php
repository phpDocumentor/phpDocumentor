<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Xslt;

/**
 * XSLT filters that can be used inside a template.
 */
class Extension
{
    /**
     * Markdown filter.
     *
     * Example usage inside template would be:
     * ```
     * <div class="long_description">
     *     <xsl:value-of
     *         select="php:function('phpDocumentor\Plugin\Core\Xslt\Extension::markdown',
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
}
