<?php


namespace JsonStream;


class RuntimeException extends \RuntimeException
{
    public static function notAJsonList($jsonRawValue): self
    {
        return new self(sprintf("Value \"%s\" is not a valid json list", substr($jsonRawValue, 10)));
    }
}