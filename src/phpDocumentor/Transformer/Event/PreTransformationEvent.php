<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Transformer\Transformation;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event happening prior to each individual transformation.
 */
final class PreTransformationEvent extends Event
{
    /** @var Transformation */
    private $transformation;

    /** @var object */
    private $subject;

    public function __construct(object $subject, Transformation $transformation)
    {
        $this->subject = $subject;
        $this->transformation = $transformation;
    }

    public static function create(object $subject, Transformation $transformation): self
    {
        return new self($subject, $transformation);
    }

    public function getTransformation(): Transformation
    {
        return $this->transformation;
    }

    public function getSubject(): object
    {
        return $this->subject;
    }
}
