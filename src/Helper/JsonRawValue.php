<?php


namespace JsonStream\Helper;


use Generator;
use JsonStream\JsonRawValueInterface;

class JsonRawValue implements JsonRawValueInterface
{
    private $jsonRawValue;

    public function __construct(string $jsonRawValue)
    {
        $this->jsonRawValue = $jsonRawValue;
    }

    public function getJson(): Generator
    {
        yield $this->jsonRawValue;
    }
}