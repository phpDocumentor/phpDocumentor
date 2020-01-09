<?php

declare(strict_types=1);

namespace phpDocumentor;

use InvalidArgumentException;
use League\Uri\Contracts\UriInterface;
use League\Uri\Uri as LeagueUri;
use Throwable;
use const DIRECTORY_SEPARATOR;
use function preg_match;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

final class UriFactory
{
    public const WINDOWS_URI_FORMAT = '~^(file:\/\/\/)?(?<root>[a-zA-Z][:|\|])~';

    public static function createUri(string $uriString) : UriInterface
    {
        try {
            $uriString = str_replace(DIRECTORY_SEPARATOR, '/', $uriString);
            if (preg_match(self::WINDOWS_URI_FORMAT, $uriString)) {
                if (strpos($uriString, 'file:///') === 0) {
                    $uriString = substr($uriString, strlen('file:///'));
                }

                return LeagueUri::createFromWindowsPath($uriString);
            }

            return LeagueUri::createFromString($uriString);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException(
                sprintf(
                    'The uri "%s" could not be parsed, the following error occured: %s',
                    $uriString,
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }
    }
}
