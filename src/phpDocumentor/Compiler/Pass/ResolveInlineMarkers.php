<?php

declare(strict_types=1);

/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;
use function implode;
use function preg_match_all;
use function str_replace;
use function str_split;
use function strlen;
use function trim;

final class ResolveInlineMarkers implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 9000;

    /**
     * {@inheritDoc}
     */
    public function getDescription() : string
    {
        return 'Collect all markers in a file';
    }

    /**
     * Scans the files for markers and records them in the markers property of a file.
     */
    public function execute(ProjectDescriptor $project) : void
    {
        $markerTerms = $project->getSettings()->getMarkers();

        foreach ($project->getFiles() as $file) {
            $marker_data = [];
            $matches     = [];
            $source = $file->getSource() ?: '';

            preg_match_all(
                '~//[\s]*(' . implode('|', $markerTerms) . ')\:?[\s]*(.*)~',
                $source,
                $matches,
                PREG_SET_ORDER | PREG_OFFSET_CAPTURE
            );

            foreach ($matches as $match) {
                [$before] = str_split($source, $match[1][1]); // fetches all the text before the match

                $line_number = strlen($before) - strlen(str_replace("\n", '', $before)) + 1;

                $marker_data[] = ['type' => trim($match[1][0], '@'), 'line' => $line_number, $match[2][0]];
            }

            $file->setMarkers(new Collection($marker_data));
        }
    }
}
