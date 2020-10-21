<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Span;

class SpanToken
{
    public const TYPE_LITERAL = 'literal';
    public const TYPE_REFERENCE = 'reference';
    public const TYPE_LINK = 'link';

    /** @var string */
    private $type;

    /** @var string */
    private $id;

    /** @var string[] */
    private $token;

    /**
     * @param string[] $token
     */
    public function __construct(string $type, string $id, array $token)
    {
        $this->type = $type;
        $this->id = $id;
        $this->token = $token;
        $this->token['type'] = $type;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function get(string $key) : string
    {
        return $this->token[$key] ?? '';
    }

    /**
     * @return string[]
     */
    public function getTokenData() : array
    {
        return $this->token;
    }
}
