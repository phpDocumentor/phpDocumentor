<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\ApplicationName;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Version;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Extension\Validator */
final class ValidatorTest extends TestCase
{
    use Faker;

    public function testValidatesExtensionFor(): void
    {
        $application = new ApplicationName('phpDocumentor/other');
        $version = new Version();
        $validator = new Validator($application, $version);

        $manifest = self::faker()->extensionManifest('1.0.0');
        $extension = ExtensionInfo::fromManifest($manifest, '/extension');

        self::assertFalse($validator->isValid($extension));
    }
}
