<?php

declare(strict_types=1);

/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;

use function implode;
use function preg_match_all;
use function str_replace;
use function str_split;
use function strlen;
use function trim;

use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

final class ResolveInlineMarkers implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 9000;

    public function getDescription(): string
    {
        return 'Collect all markers in a file';
    }

    /**
     * Scans the files for markers and records them in the markers property of a file.
     */
    public function execute(ProjectDescriptor $project): void
    {
        ///This looks ugly, when versions are introduced we get rid of these 2 foreach loops.
        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                if ($documentationSet instanceof ApiSetDescriptor === false) {
                    continue;
                }

                $markerTerms = $documentationSet->getSettings()['markers'];

                /** @var FileDescriptor $file */
                foreach ($project->getFiles() as $file) {
                    $matches = [];
                    $source  = $file->getSource() ?? '';

                    preg_match_all(
                        '~//[\s]*(' . implode('|', $markerTerms) . ')\:?[\s]*(.*)~',
                        $source,
                        $matches,
                        PREG_SET_ORDER | PREG_OFFSET_CAPTURE
                    );

                    foreach ($matches as $match) {
                        [$before] = str_split($source, $match[1][1]); // fetches all the text before the match

                        $lineNumber = strlen($before) - strlen(str_replace("\n", '', $before)) + 1;
                        $file->getMarkers()->add(
                            [
                                'type' => trim($match[1][0], '@'),
                                'line' => $lineNumber,
                                'message' => $match[2][0],
                            ]
                        );
                    }
                }
            }
        }
    }
}
