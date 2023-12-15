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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;

use function implode;
use function preg_match_all;
use function str_replace;
use function str_split;
use function strlen;
use function trim;

use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9000,
    'Collect all markers in a file',
)]
final class ResolveInlineMarkers extends ApiDocumentationPass
{
    /**
     * Scans the files for markers and records them in the markers property of a file.
     */
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        $markerTerms = $subject->getSettings()['markers'];

        /** @var FileDescriptor $file */
        foreach ($subject->getFiles() as $file) {
            $matches = [];
            $source  = $file->getSource() ?? '';

            preg_match_all(
                '~//[\s]*(' . implode('|', $markerTerms) . ')\:?[\s]*(.*)~',
                $source,
                $matches,
                PREG_SET_ORDER | PREG_OFFSET_CAPTURE,
            );

            foreach ($matches as $match) {
                [$before] = str_split($source, $match[1][1]); // fetches all the text before the match

                $lineNumber = strlen($before) - strlen(str_replace("\n", '', $before)) + 1;
                $file->getMarkers()->add(
                    [
                        'type' => trim($match[1][0], '@'),
                        'line' => $lineNumber,
                        'message' => $match[2][0],
                    ],
                );
            }
        }

        return $subject;
    }
}
