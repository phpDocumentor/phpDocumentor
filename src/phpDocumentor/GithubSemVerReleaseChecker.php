<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use OutOfRangeException;
use RuntimeException;
use Throwable;
use function array_column;
use function array_filter;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function json_decode;
use function sprintf;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const CURLOPT_USERAGENT;

class GithubSemVerReleaseChecker
{
    /** @var VersionParser */
    private $parser;

    public function __construct(VersionParser $parser)
    {
        $this->parser = $parser;
    }

    public function check(string $installedVersion) : void
    {
        $parsedVersion = $this->validateAndNormalizeVersion($installedVersion);
        if ($parsedVersion === '') {
            throw new RuntimeException(
                sprintf('Unable to parse the installed version, received: %s', $installedVersion)
            );
        }

        $releases = $this->fetchReleases();

        $latestRelease = $releases[0];
        if (Comparator::greaterThan($latestRelease, $parsedVersion)) {
            throw new OutOfRangeException(
                sprintf(
                    'A newer version is available (%s), please upgrade to benefit from bug fixes and added features',
                    $latestRelease
                )
            );
        }
    }

    /**
     * @return array<string>
     */
    private function fetchReleases() : array
    {
        $releases = array_column($this->fetchGithubReleases(), 'tag_name');

        foreach ($releases as &$version) {
            $version = $this->validateAndNormalizeVersion($version) ?: null;
        }

        unset($version); // always unset after looping with a reference

        // filter away null values (invalid versions)
        $releases = array_filter($releases);

        return Semver::rsort($releases);
    }

    /**
     * @return array<string|array<string>>
     */
    private function fetchGithubReleases() : array
    {
        $url = 'https://api.github.com/repos/phpDocumentor/phpDocumentor/releases';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
        );
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    private function validateAndNormalizeVersion(string $version) : string
    {
        try {
            return $this->parser->normalize($version);
        } catch (Throwable $e) {
            return '';
        }
    }
}
