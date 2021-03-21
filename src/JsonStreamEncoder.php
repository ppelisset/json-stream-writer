<?php


namespace JsonStream;


use Generator;

final class JsonStreamEncoder
{
    public function encodeAsStream($value): Generator
    {
        return $this->encodeValue($value);
    }

    public function encodeAsString($value): string
    {
        $generator = $this->encodeAsStream($value);
        return implode("", iterator_to_array($generator, false));
    }

    private function encodeValue($value): Generator
    {
        if (is_callable($value) && !is_string($value)) {
            /** @var callable $value */
            yield from $this->encodeValue($value());
            return;
        }
        if (is_iterable($value)) {
            /** @var iterable $value */
            yield from $this->encodeIterable($value);
            return;
        }
        if ($value instanceof JsonRawValueInterface) {
            /** @var JsonRawValueInterface $value */
            yield from $value->getJson();
            return;
        }
        yield json_encode($value);
    }

    private function encodeIterable(iterable $iterator): Generator
    {
        $isFirstIteration = true;
        $isList = true;
        foreach ($iterator as $key => $value) {
            if ($isFirstIteration) {
                $isList = $key === 0;
                yield $isList ? JsonToken::LIST_OPEN : JsonToken::OBJECT_OPEN;
                $isFirstIteration = false;
            } else {
                yield JsonToken::COMMA;
            }
            if (!$isList) {
                yield from $this->encodeValue((string) $key);
                yield JsonToken::COLON;
            }
            yield from $this->encodeValue($value);
        }
        yield $isList ? JsonToken::LIST_CLOSE : JsonToken::OBJECT_CLOSE;
    }
}