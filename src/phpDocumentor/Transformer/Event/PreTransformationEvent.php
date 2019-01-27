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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;
use phpDocumentor\Transformer\Transformation;

/**
 * Event happening prior to each individual transformation.
 */
class PreTransformationEvent extends EventAbstract
{
    /** @var Transformation */
    protected $transformation;

    public function __construct($subject, Transformation $transformation)
    {
        parent::__construct($subject);
        $this->transformation = $transformation;
    }

    public static function create($subject, Transformation $transformation)
    {
        return new static($subject, $transformation);
    }

    public function getTransformation(): Transformation
    {
        return $this->transformation;
    }
}
