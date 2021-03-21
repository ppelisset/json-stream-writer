<?php

namespace JsonStream\Tests;

use Generator;
use JsonStream\JsonStreamEncoder;
use PHPUnit\Framework\TestCase;

class JsonStreamEncoderTest extends TestCase
{
    private const LIST = [
        1,
        2,
        3
    ];
    private const HASH = [
        'name' => 'Pierre',
        'company' => 'KaraFun'
    ];

    private $encoder;

    public function setUp(): void
    {
        $this->encoder = new JsonStreamEncoder();
    }

    public function testScalableValue(): void
    {
        $this->assertEncodedJsonEquals(true);
        $this->assertEncodedJsonEquals(15.2);
        $this->assertEncodedJsonEquals('test');
    }

    public function testArrayValue(): void
    {
        $this->assertEncodedJsonEquals(self::LIST);
    }

    public function testArrayWithKeyStartAtNonZeroIndex()
    {
        $list = self::LIST;
        array_unshift($list, null);
        unset($list[0]);
        $this->assertEncodedJsonEquals($list);
    }

    public function testHashValue(): void
    {
        $this->assertEncodedJsonEquals(self::HASH);
    }

    public function testGeneratorArray(): void
    {
        $this->testGenerator(function () {
            return $this->buildGenerator(self::LIST);
        });
    }

    public function testGeneratorHash(): void
    {
        $this->testGenerator(function () {
            return $this->buildGenerator(self::HASH);
        });
    }

    public function testGeneratorInList(): void
    {
        $this->testGeneratorInArray(function () {
            return $this->buildListOfGenerator();
        });
    }

    public function testGeneratorInHash(): void
    {
        $this->testGeneratorInArray(function () {
            return $this->buildHashOfGenerator();
        });
    }

    public function testCallable(): void
    {
        $callable = function () {
            return self::HASH;
        };
        $expected = json_encode($callable());
        $actual = $this->encoder->encodeAsString($callable);
        $this->assertEquals($expected, $actual);
    }

    private function testGenerator(callable $generatorFunction): void
    {
        $expected = json_encode(iterator_to_array($generatorFunction()));
        $actual = $this->encoder->encodeAsString($generatorFunction());
        $this->assertEquals($expected, $actual);
    }

    private function testGeneratorInArray(callable $generatorInArrayFunction): void
    {
        $expected = json_encode(array_map(function (Generator $generator) {
            return iterator_to_array($generator);
        }, $generatorInArrayFunction()));
        $actual = $this->encoder->encodeAsString($generatorInArrayFunction());
        $this->assertEquals($expected, $actual);
    }


    private function assertEncodedJsonEquals($value): void
    {
        $this->assertEquals(json_encode($value), $this->encoder->encodeAsString($value));
    }

    private function buildGenerator($traversable): Generator
    {
        foreach ($traversable as $key => $value) {
            yield $key => $value;
        }
    }

    private function buildListOfGenerator(): array
    {
        return [
            $this->buildGenerator(self::LIST),
            $this->buildGenerator(self::LIST),
            $this->buildGenerator(self::LIST)
        ];
    }

    private function buildHashOfGenerator(): array
    {
        return [
            'Gen1' => $this->buildGenerator(self::LIST),
            'Gen2' => $this->buildGenerator(self::HASH),
            'Gen3' => $this->buildGenerator(self::LIST)
        ];
    }
}