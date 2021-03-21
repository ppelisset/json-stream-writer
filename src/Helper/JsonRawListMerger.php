<?php


namespace JsonStream\Helper;


use Generator;
use JsonStream\JsonRawValueInterface;
use JsonStream\JsonToken;
use JsonStream\RuntimeException;

class JsonRawListMerger implements JsonRawValueInterface
{
    private $jsonLists;

    /**
     * @param iterable<string> $jsonLists
     */
    public function __construct(iterable $jsonLists)
    {
        $this->jsonLists = $jsonLists;
    }

    public function getJson(): Generator
    {
        $isFirst = true;
        $previousEmptyList = false;
        foreach ($this->jsonLists as $list) {
            $this->isJsonList($list);
            if ($isFirst) {
                yield JsonToken::LIST_OPEN;
                $isFirst = false;
            } elseif ($previousEmptyList) {
                yield JsonToken::COMMA;
            }
            yield substr($list, 1, -1);
            $previousEmptyList = $this->isEmptyList($list);
        }
        if ($isFirst) {
            yield JsonToken::LIST_OPEN;
        }
        yield JsonToken::LIST_CLOSE;
    }

    private function isJsonList(&$list): void
    {
        if (!is_string($list)) {
            throw RuntimeException::notAJsonList($list);
        }
        $list = trim($list);
        if ($list[0] !== JsonToken::LIST_OPEN || $list[strlen($list) - 1] !== JsonToken::LIST_CLOSE) {
            throw RuntimeException::notAJsonList($list);
        }
    }

    private function isEmptyList($list): bool
    {
        return strlen(trim(substr($list, 1, -1))) < 1;
    }
}