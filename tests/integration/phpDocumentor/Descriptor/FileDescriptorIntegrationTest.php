<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Validation\Error;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class FileDescriptorIntegrationTest extends TestCase
{
    public function testClassMethodWithInvalidTag()
    {
        $expectedError = new Error('ERROR', 'test error', null);

        $tag = new TagDescriptor('test');
        $tag->getErrors()->add(
            $expectedError
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
