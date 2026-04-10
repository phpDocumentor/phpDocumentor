<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\InvalidTagAssembler */
final class InvalidTagAssemblerTest extends TestCase
{
    public function testCreateWithError(): void
    {
        $assembler = new InvalidTagAssembler();
        $tag = $assembler->create(InvalidTag::create('Tag body', 'name'));

        self::assertEquals('Tag body', $tag->getDescription());
        self::assertSame('name', $tag->getName());
        self::assertEquals('ERROR', $tag->getErrors()[0]->getSeverity());
        self::assertEquals(
            'Tag "name" with body "@name Tag body" has error ',
            $tag->getErrors()[0]->getCode(),
        );
    }

    public function testCreateWithUrlEncodedBody(): void
    {
        $assembler = new InvalidTagAssembler();
        $tag = $assembler->create(InvalidTag::create(
            'LDAP-Error-Code https://ldapwiki.com/wiki/Wiki.jsp?page=LDAP%20Result%20Codes',
            'see',
        ));

        self::assertSame('see', $tag->getName());
        // Rendering the description must not throw a ValueError from vsprintf
        // interpreting URL-encoded sequences like %20 as format specifiers.
        self::assertStringContainsString(
            'LDAP%20Result%20Codes',
            (string) $tag->getDescription(),
        );
    }
}
