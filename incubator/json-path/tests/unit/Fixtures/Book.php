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

namespace phpDocumentor\JsonPath\Fixtures;

class Book
{
    /** @var Book[] */
    private array $chapters = [];

    /** @var Annotation[] */
    private array $annotations = [];

    public function __construct(private readonly string $title)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function addChapter(Book $chapter): void
    {
        $this->chapters[] = $chapter;
    }

    /** @return Book[] */
    public function getChapters(): array
    {
        return $this->chapters;
    }

    public function addAnnotation(Annotation $annotation): void
    {
        $this->annotations[] = $annotation;
    }

    /** @return Annotation[] */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }
}
