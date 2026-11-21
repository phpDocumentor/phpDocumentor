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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Validation\Error;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class FileDescriptorIntegrationTest extends TestCase
{
    public function testClassMethodWithInvalidTag()
    {
        $expectedError = new Error('ERROR', 'test error', null);

        $tag = new TagDescriptor('test');
        $tag->getErrors()->add(
            $expectedError,
        );

        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setTags(new Collection(['test' => new Collection([$tag])]));

        $classDescriptor = new ClassDescriptor();
        $classDescriptor->setMethods(new Collection([$methodDescriptor]));

        $fileDescriptor = new FileDescriptor('foobar');
        $fileDescriptor->setClasses(new Collection([$classDescriptor]));

        self::assertSame([$expectedError], $fileDescriptor->getAllErrors()->getAll());
    }
}
