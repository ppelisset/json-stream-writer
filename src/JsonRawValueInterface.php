<?php


namespace JsonStream;


use Generator;

interface JsonRawValueInterface
{
    public function getJson(): Generator;
}