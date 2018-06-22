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

namespace phpDocumentor\Plugin;

use JMS\Serializer\Annotation as Serializer;

/**
 * Model representing a plugin parameter
 *
 * @Serializer\XmlRoot("parameter")
 */
class Parameter
{
    /**
     * @Serializer\Type("string")
     * @var string
     * @Serializer\XmlAttribute
     */
    protected $key;

    /**
     * @Serializer\Type("string")
     * @var string
     * @Serializer\XmlValue
     */
    protected $value;

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
