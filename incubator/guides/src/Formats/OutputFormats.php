<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Formats;

use InvalidArgumentException;

use function sprintf;
use function strtolower;

final class OutputFormats
{
    /** @var OutputFormat[] */
    private $outputFormats = [];

    /**
     * @param iterable<OutputFormat> $outputFormats
     */
    public function __construct(iterable $outputFormats = [])
    {
        foreach ($outputFormats as $outputFormat) {
            $this->add($outputFormat);
        }
    }

    public function add(OutputFormat $outputFormat): void
    {
        $this->outputFormats[strtolower($outputFormat->getFileExtension())] = $outputFormat;
    }

    public function get(string $extension): OutputFormat
    {
        $outputFormat = $this->outputFormats[strtolower($extension)] ?? null;
        if ($outputFormat === null) {
            throw new InvalidArgumentException(sprintf('Output format "%s" is not supported', $extension));
        }

        return $outputFormat;
    }
}
