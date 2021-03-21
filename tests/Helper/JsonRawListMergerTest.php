<?php

namespace JsonStream\Helper;

use JsonStream\JsonStreamEncoder;
use PHPUnit\Framework\TestCase;

class JsonRawListMergerTest extends TestCase
{
    private $encoder;

    public function setUp(): void
    {
        $this->encoder = new JsonStreamEncoder();
    }

    public function testRawListMerge(): void
    {
        $this->testIsSameAfterDecoded($this->generateMultipleList());
    }

    public function testWithEmptyList(): void
    {
        $lists = $this->generateMultipleList();
        $lists[1] = json_encode([]);
        $this->testIsSameAfterDecoded($lists);
    }

    public function testWithEmptyIterable(): void
    {
        $this->testIsSameAfterDecoded([]);
    }

    private function testIsSameAfterDecoded(array $jsonLists)
    {
        $expected = array_merge(...array_map(function ($json) {
            return json_decode($json, true);
        }, $jsonLists));
        $jsonRawListMerger = new JsonRawListMerger($jsonLists);
        $actual = json_decode($this->encoder->encodeAsString($jsonRawListMerger), true);
        $this->assertEquals($expected, $actual);
    }

    private function generateMultipleList()
    {
        $lists = [];
        for ($i = 0; $i < 3; $i++) {
            $current = [];
            for ($i = 0; $i < 10; $i++) {
                $current[] = ["time_" . $i => microtime()];
            }
            $lists[] = json_encode($current);
        }
        return $lists;
    }
}