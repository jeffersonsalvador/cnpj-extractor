<?php

namespace App;

trait Header
{
    public static function getHeader()
    {
        $constants = (new \ReflectionClass(static::class))->getConstants();
        return array_values($constants);
    }

}
